<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class VisitorManagementFeatureTest extends TestCase
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

    public function test_admin_walk_in_create_starts_as_approved(): void
    {
        $admin = $this->createAdmin();
        $resident = $this->createResident();

        $this->actingAs($admin)
            ->post(route('visitor-management.walk-in.store'), [
                'resident_id' => $resident->id,
                'visitor_name' => 'Walk In Guest',
                'visitor_phone' => '081377700000',
                'visit_date' => '2026-06-23',
                'estimated_arrival_time' => '12:00',
                'guest_count' => 2,
                'visit_purpose' => 'Meeting',
            ])
            ->assertRedirect();

        $visitor = Visitor::query()->firstOrFail();

        $this->assertSame(Visitor::STATUS_APPROVED, $visitor->status);
        $this->assertSame(Visitor::SOURCE_ADMIN_WALK_IN, $visitor->registration_source);
        $this->assertNotNull($visitor->approved_at);
        $this->assertNotEmpty($visitor->access_code);
    }

    public function test_admin_can_approve_reject_check_in_and_check_out_with_valid_transitions_only(): void
    {
        $admin = $this->createAdmin();
        $resident = $this->createResident();

        $pendingVisitor = $this->createVisitor($resident, [
            'status' => Visitor::STATUS_PENDING,
        ]);

        $this->actingAs($admin)
            ->post(route('visitor-management.approve', $pendingVisitor))
            ->assertRedirect();

        $this->assertSame(Visitor::STATUS_APPROVED, $pendingVisitor->fresh()->status);

        $approvedVisitor = $this->createVisitor($resident, [
            'status' => Visitor::STATUS_APPROVED,
            'approved_at' => now(),
            'expires_at' => Carbon::parse('2026-06-23 23:59:59', config('app.timezone')),
        ]);

        $this->actingAs($admin)
            ->from(route('visitor-management.pending-approval'))
            ->post(route('visitor-management.reject', $approvedVisitor), [
                'rejection_reason' => 'Late',
            ])
            ->assertRedirect(route('visitor-management.pending-approval'));

        $this->assertSame(Visitor::STATUS_APPROVED, $approvedVisitor->fresh()->status);
        $this->assertTrue(session()->has('errors'));

        $this->actingAs($admin)
            ->post(route('visitor-management.check-in', $approvedVisitor), [
                'access_code' => $approvedVisitor->access_code,
                'access_card_number' => 'CARD-01',
            ])
            ->assertRedirect();

        $this->assertSame(Visitor::STATUS_CHECKED_IN, $approvedVisitor->fresh()->status);

        $this->actingAs($admin)
            ->from(route('visitor-management.check-in-out'))
            ->post(route('visitor-management.check-in', $approvedVisitor), [
                'access_code' => $approvedVisitor->access_code,
            ])
            ->assertRedirect(route('visitor-management.check-in-out'));

        $this->assertSame(Visitor::STATUS_CHECKED_IN, $approvedVisitor->fresh()->status);

        $this->actingAs($admin)
            ->post(route('visitor-management.check-out', $approvedVisitor))
            ->assertRedirect();

        $this->assertSame(Visitor::STATUS_CHECKED_OUT, $approvedVisitor->fresh()->status);
        $this->assertNotNull($approvedVisitor->fresh()->checked_out_at);
    }

    public function test_code_lookup_redirects_to_check_in_out_with_selected_visitor(): void
    {
        $admin = $this->createAdmin();
        $resident = $this->createResident();
        $visitor = $this->createVisitor($resident, [
            'status' => Visitor::STATUS_APPROVED,
            'approved_at' => now(),
            'expires_at' => Carbon::parse('2026-06-23 23:59:59', config('app.timezone')),
        ]);

        $this->actingAs($admin)
            ->post(route('visitor-management.lookup'), [
                'access_code' => $visitor->access_code,
            ])
            ->assertRedirect(route('visitor-management.check-in-out', ['visitor' => $visitor->id]));
    }

    public function test_visitor_filters_render_auto_submit_markup(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin)
            ->get(route('visitor-management.registration'))
            ->assertOk()
            ->assertSee('data-auto-submit-get', false)
            ->assertSee('data-auto-submit-control', false);
    }

    private function createAdmin(): User
    {
        $role = Role::query()->create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        return User::query()->create([
            'role_id' => $role->id,
            'name' => 'Administrator',
            'username' => 'admin-visitor',
            'password' => 'password',
        ]);
    }

    private function createResident(): Resident
    {
        $unit = Unit::query()->create([
            'code' => 'C-'.random_int(1000, 9999),
            'tower' => 'Tower C',
            'floor' => 10,
            'unit_type' => '1BR',
            'occupancy_status' => 'Terisi',
            'payment_status' => 'Lunas',
            'thumbnail_tone' => 'default',
        ]);

        return Resident::query()->create([
            'unit_id' => $unit->id,
            'name' => 'Resident Web',
            'email' => 'resident-web@example.com',
            'mobile_no' => '081211100000',
            'password' => 'password',
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
            'visitor_name' => 'Web Guest',
            'visitor_phone' => '081322233344',
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
