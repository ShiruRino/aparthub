<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Resident;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Models\UserModule;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SecurityVisitorAccessApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config(['app.timezone' => 'Asia/Jakarta']);
        Carbon::setTestNow(Carbon::parse('2026-06-23 10:00:00', config('app.timezone')));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();

        parent::tearDown();
    }

    public function test_security_validation_requires_authentication(): void
    {
        $this->postJson('/api/security/visitor-access/validate', [
            'code' => 'UNKNOWN',
        ])->assertUnauthorized();
    }

    public function test_authenticated_non_security_user_is_rejected(): void
    {
        $user = $this->createUserWithSecurityAccess(false);
        $token = $user->createToken('plain-user')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/security/visitor-access/validate', [
                'code' => 'UNKNOWN',
            ])
            ->assertForbidden();
    }

    public function test_valid_access_code_returns_valid_payload_without_mutating_status(): void
    {
        $user = $this->createUserWithSecurityAccess(true);
        $resident = $this->createResident();
        $visitor = $this->createVisitor($resident, [
            'status' => Visitor::STATUS_APPROVED,
            'approved_at' => now(),
            'visit_date' => '2026-06-23',
            'estimated_arrival_time' => '12:00:00',
            'expires_at' => Carbon::parse('2026-06-23 23:59:59', config('app.timezone')),
        ]);

        $token = $user->createToken('security')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/security/visitor-access/validate', [
                'code' => $visitor->access_code,
            ])
            ->assertOk()
            ->assertJsonPath('data.is_valid', true)
            ->assertJsonPath('data.visitor_name', $visitor->visitor_name)
            ->assertJsonPath('data.resident_name', $resident->name)
            ->assertJsonPath('data.unit', $resident->unit->code)
            ->assertJsonPath('data.status', Visitor::STATUS_APPROVED);

        $this->assertSame(Visitor::STATUS_APPROVED, $visitor->fresh()->status);
    }

    public function test_invalid_visitor_states_and_expiry_return_false_without_check_in_side_effect(): void
    {
        $user = $this->createUserWithSecurityAccess(true);
        $resident = $this->createResident();
        $pendingVisitor = $this->createVisitor($resident, [
            'status' => Visitor::STATUS_PENDING,
            'visit_date' => '2026-06-23',
            'estimated_arrival_time' => '12:00:00',
            'expires_at' => Carbon::parse('2026-06-23 23:59:59', config('app.timezone')),
        ]);
        $expiredVisitor = $this->createVisitor($resident, [
            'status' => Visitor::STATUS_APPROVED,
            'approved_at' => now()->subHour(),
            'visit_date' => '2026-06-22',
            'estimated_arrival_time' => '12:00:00',
            'expires_at' => Carbon::parse('2026-06-22 23:59:59', config('app.timezone')),
            'access_code' => strtoupper(bin2hex(random_bytes(8))),
        ]);

        $token = $user->createToken('security')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/security/visitor-access/validate', [
                'code' => $pendingVisitor->access_code,
            ])
            ->assertStatus(422)
            ->assertJsonPath('data.is_valid', false)
            ->assertJsonPath('data.status', Visitor::STATUS_PENDING);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/security/visitor-access/validate', [
                'code' => $expiredVisitor->access_code,
            ])
            ->assertStatus(422)
            ->assertJsonPath('data.is_valid', false);

        $this->assertSame(Visitor::STATUS_PENDING, $pendingVisitor->fresh()->status);
        $this->assertSame(Visitor::STATUS_EXPIRED, $expiredVisitor->fresh()->status);
    }

    private function createUserWithSecurityAccess(bool $canReadSecurity): User
    {
        $role = Role::query()->create([
            'name' => $canReadSecurity ? 'Security' : 'Staff',
            'slug' => $canReadSecurity ? 'security' : 'staff',
        ]);

        $user = User::query()->create([
            'name' => $canReadSecurity ? 'Security User' : 'Plain User',
            'username' => $canReadSecurity ? 'security-user' : 'plain-user',
            'password' => Hash::make('secret-pass'),
            'role_id' => $role->id,
        ]);

        $module = Module::query()->create([
            'name' => 'Security Management',
            'slug' => 'security-management',
            'description' => 'Security module',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        if ($canReadSecurity) {
            UserModule::query()->create([
                'user_id' => $user->id,
                'module_id' => $module->id,
                'can_create' => false,
                'can_read' => true,
                'can_update' => false,
                'can_delete' => false,
            ]);
        }

        return $user;
    }

    private function createResident(): Resident
    {
        $unit = Unit::query()->create([
            'code' => 'B-'.random_int(1000, 9999),
            'tower' => 'Tower B',
            'floor' => 8,
            'unit_type' => 'Studio',
            'occupancy_status' => 'Terisi',
            'payment_status' => 'Lunas',
            'thumbnail_tone' => 'default',
        ]);

        return Resident::query()->create([
            'unit_id' => $unit->id,
            'name' => 'Resident Security',
            'email' => 'security-resident@example.com',
            'mobile_no' => '081299900011',
            'password' => Hash::make('secret-pass'),
            'resident_type' => 'Penyewa',
            'status' => 'Aktif',
            'move_in_date' => '2026-06-01',
            'contract_end_date' => '2027-06-01',
            'avatar_tone' => 'default',
        ]);
    }

    /**
     * @param  array<string, mixed>  $overrides
     */
    private function createVisitor(Resident $resident, array $overrides = []): Visitor
    {
        return Visitor::query()->create(array_merge([
            'resident_id' => $resident->id,
            'visitor_name' => 'Secure Guest',
            'visitor_phone' => '081344455566',
            'visit_date' => '2026-06-23',
            'estimated_arrival_time' => '12:00:00',
            'guest_count' => 1,
            'visit_purpose' => 'Meeting',
            'status' => Visitor::STATUS_PENDING,
            'registration_source' => Visitor::SOURCE_RESIDENT_APP,
            'access_code' => strtoupper(bin2hex(random_bytes(8))),
            'expires_at' => Carbon::parse('2026-06-23 23:59:59', config('app.timezone')),
        ], $overrides));
    }
}
