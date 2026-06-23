<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Resident;
use App\Models\Unit;
use App\Models\Visitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ResidentVisitorApiTest extends TestCase
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

    public function test_guest_cannot_access_resident_visitor_api(): void
    {
        $this->getJson('/api/resident/visitors')->assertUnauthorized();
    }

    public function test_resident_can_create_pending_visitor_for_themselves_only(): void
    {
        Storage::fake('local');

        AppSetting::putInteger('visitor_guest_max', 10);
        $resident = $this->createResident('owner@example.com', '081200000111');
        $token = $resident->createToken('resident-mobile')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/resident/visitors', [
                'resident_id' => 999,
                'status' => Visitor::STATUS_APPROVED,
                'registration_source' => 'Injected',
                'access_code' => 'BADCODE',
                'visitor_name' => 'John Guest',
                'visitor_phone' => '081300000999',
                'visit_date' => '2026-06-23',
                'estimated_arrival_time' => '14:30',
                'guest_count' => 2,
                'visit_purpose' => 'Meeting',
                'identity_photo' => UploadedFile::fake()->image('identity.jpg'),
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['resident_id', 'status', 'registration_source', 'access_code']);

        $response = $this->withHeader('Authorization', 'Bearer '.$token)
            ->post('/api/resident/visitors', [
                'visitor_name' => 'John Guest',
                'visitor_phone' => '081300000999',
                'visit_date' => '2026-06-23',
                'estimated_arrival_time' => '14:30',
                'guest_count' => 2,
                'visit_purpose' => 'Meeting',
                'identity_photo' => UploadedFile::fake()->image('identity.jpg'),
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.status', Visitor::STATUS_PENDING)
            ->assertJsonPath('data.registration_source', Visitor::SOURCE_RESIDENT_APP)
            ->assertJsonPath('data.guest_count', 2)
            ->assertJsonPath('data.unit.code', $resident->unit->code)
            ->assertJsonPath('data.qr_available', false);

        $visitor = Visitor::query()->firstOrFail();

        $this->assertSame($resident->id, $visitor->resident_id);
        $this->assertSame(Visitor::STATUS_PENDING, $visitor->status);
        $this->assertSame(Visitor::SOURCE_RESIDENT_APP, $visitor->registration_source);
        $this->assertNotNull($visitor->identity_photo_path);
    }

    public function test_guest_limit_setting_blocks_create_when_exceeded(): void
    {
        AppSetting::putInteger('visitor_guest_max', 1);
        $resident = $this->createResident('limit@example.com', '081200000112');
        $token = $resident->createToken('resident-mobile')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/resident/visitors', [
                'visitor_name' => 'Family Group',
                'visitor_phone' => '081355577777',
                'visit_date' => '2026-06-23',
                'estimated_arrival_time' => '16:00',
                'guest_count' => 2,
                'visit_purpose' => 'Family Visit',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['guest_count']);
    }

    public function test_resident_can_only_manage_their_own_visitors(): void
    {
        $owner = $this->createResident('owner2@example.com', '081200000113');
        $other = $this->createResident('other@example.com', '081200000114');
        $token = $owner->createToken('resident-mobile')->plainTextToken;

        $visitor = $this->createVisitor($other, [
            'status' => Visitor::STATUS_PENDING,
            'visit_date' => '2026-06-23',
            'estimated_arrival_time' => '14:00:00',
            'expires_at' => Carbon::parse('2026-06-23 23:59:59', config('app.timezone')),
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/resident/visitors/'.$visitor->id)
            ->assertNotFound();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/resident/visitors/'.$visitor->id, [
                'visitor_name' => 'Hacker',
                'visitor_phone' => '081300000000',
                'visit_date' => '2026-06-23',
                'estimated_arrival_time' => '14:00',
                'guest_count' => 1,
                'visit_purpose' => 'Hack',
            ])
            ->assertNotFound();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/resident/visitors/'.$visitor->id.'/cancel')
            ->assertNotFound();

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/resident/visitors/'.$visitor->id.'/qr')
            ->assertNotFound();
    }

    public function test_pending_visitor_can_be_updated_and_cancelled_by_owner_only(): void
    {
        $resident = $this->createResident('pending@example.com', '081200000115');
        $token = $resident->createToken('resident-mobile')->plainTextToken;
        $visitor = $this->createVisitor($resident, [
            'status' => Visitor::STATUS_PENDING,
            'visit_date' => '2026-06-23',
            'estimated_arrival_time' => '13:00:00',
            'expires_at' => Carbon::parse('2026-06-23 23:59:59', config('app.timezone')),
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/resident/visitors/'.$visitor->id, [
                'visitor_name' => 'Updated Guest',
                'visitor_phone' => '081366688899',
                'visit_date' => '2026-06-23',
                'estimated_arrival_time' => '15:00',
                'guest_count' => 3,
                'visit_purpose' => 'Dinner',
            ])
            ->assertOk()
            ->assertJsonPath('data.visitor_name', 'Updated Guest')
            ->assertJsonPath('data.guest_count', 3);

        $visitor->refresh();
        $this->assertSame('Updated Guest', $visitor->visitor_name);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/resident/visitors/'.$visitor->id.'/cancel', [
                'reason' => 'Plans changed',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', Visitor::STATUS_CANCELLED)
            ->assertJsonPath('data.cancellation_reason', 'Plans changed');
    }

    public function test_approved_visitor_can_no_longer_be_edited_but_can_return_qr_when_valid(): void
    {
        $resident = $this->createResident('approved@example.com', '081200000116');
        $token = $resident->createToken('resident-mobile')->plainTextToken;
        $visitor = $this->createVisitor($resident, [
            'status' => Visitor::STATUS_APPROVED,
            'approved_at' => now(),
            'visit_date' => '2026-06-23',
            'estimated_arrival_time' => '14:00:00',
            'expires_at' => Carbon::parse('2026-06-23 23:59:59', config('app.timezone')),
        ]);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->patchJson('/api/resident/visitors/'.$visitor->id, [
                'visitor_name' => 'Should Fail',
                'visitor_phone' => '081399999999',
                'visit_date' => '2026-06-23',
                'estimated_arrival_time' => '16:00',
                'guest_count' => 1,
                'visit_purpose' => 'Changed',
            ])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['visitor']);

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/resident/visitors/'.$visitor->id.'/qr')
            ->assertOk()
            ->assertJsonPath('data.visitor_id', $visitor->id)
            ->assertJsonPath('data.qr_payload', $visitor->access_code)
            ->assertJsonPath('data.status', Visitor::STATUS_APPROVED);
    }

    private function createResident(string $email, string $mobile): Resident
    {
        $unit = Unit::query()->create([
            'code' => 'A-'.random_int(1000, 9999),
            'tower' => 'Tower A',
            'floor' => 12,
            'unit_type' => '2BR',
            'occupancy_status' => 'Terisi',
            'payment_status' => 'Lunas',
            'thumbnail_tone' => 'default',
        ]);

        return Resident::query()->create([
            'unit_id' => $unit->id,
            'name' => 'Resident '.substr($mobile, -3),
            'email' => $email,
            'mobile_no' => $mobile,
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
            'visitor_name' => 'Guest Visitor',
            'visitor_phone' => '081377788899',
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
