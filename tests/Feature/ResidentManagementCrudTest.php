<?php

namespace Tests\Feature;

use App\Models\AppSetting;
use App\Models\Resident;
use App\Models\ResidentFamilyMember;
use App\Models\ResidentMoveRequest;
use App\Models\ResidentVehicle;
use App\Models\Unit;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResidentManagementCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(DatabaseSeeder::class);
        $this->admin = User::query()->where('username', 'admin')->firstOrFail();
    }

    public function test_admin_can_create_update_and_delete_resident(): void
    {
        $unit = Unit::query()->where('code', 'C-1204')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('resident-management.residents.store'), [
                'unit_id' => $unit->id,
                'name' => 'Nadia Putri',
                'resident_type' => 'Penyewa',
                'status' => 'Aktif',
                'gender' => 'Female',
                'move_in_date' => '2026-06-14',
                'move_out_date' => null,
                'avatar_tone' => 'female',
            ])
            ->assertRedirect(route('resident-management.residents'));

        $resident = Resident::query()->where('name', 'Nadia Putri')->firstOrFail();

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'occupancy_status' => 'Terisi',
        ]);

        $this->actingAs($this->admin)
            ->put(route('resident-management.residents.update', $resident), [
                'unit_id' => $unit->id,
                'name' => 'Nadia Putri Updated',
                'resident_type' => 'Pemilik',
                'status' => 'Keluar',
                'gender' => 'Male',
                'move_in_date' => '2026-06-14',
                'move_out_date' => '2026-06-15',
                'avatar_tone' => 'out',
            ])
            ->assertRedirect(route('resident-management.residents'));

        $resident->refresh();

        $this->assertSame('Nadia Putri Updated', $resident->name);
        $this->assertSame('Keluar', $resident->status);
        $this->assertSame('Male', $resident->gender);

        $this->actingAs($this->admin)
            ->delete(route('resident-management.residents.destroy', $resident))
            ->assertRedirect(route('resident-management.residents'));

        $this->assertDatabaseMissing('residents', [
            'id' => $resident->id,
        ]);
    }

    public function test_admin_can_create_update_and_delete_unit(): void
    {
        $this->actingAs($this->admin)
            ->post(route('resident-management.units.store'), [
                'code' => 'D-2201',
                'tower' => 'Tower D',
                'floor' => 22,
                'unit_type' => '1BR Deluxe',
                'occupancy_status' => 'Kosong',
                'payment_status' => 'Belum Lunas',
                'thumbnail_tone' => 'empty',
            ])
            ->assertRedirect(route('resident-management.units'));

        $unit = Unit::query()->where('code', 'D-2201')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('resident-management.units.update', $unit), [
                'code' => 'D-2201',
                'tower' => 'Tower D',
                'floor' => 23,
                'unit_type' => '2BR Premium',
                'occupancy_status' => 'Perbaikan',
                'payment_status' => 'Lunas',
                'thumbnail_tone' => 'repair',
            ])
            ->assertRedirect(route('resident-management.units'));

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'floor' => 23,
            'occupancy_status' => 'Perbaikan',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('resident-management.units.destroy', $unit))
            ->assertRedirect(route('resident-management.units'));

        $this->assertDatabaseMissing('units', [
            'id' => $unit->id,
        ]);
    }

    public function test_admin_can_create_update_and_delete_move_request(): void
    {
        $resident = Resident::query()->where('name', 'Kevin Chen')->firstOrFail();
        $unit = Unit::query()->where('code', 'C-1204')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('resident-management.move-in-out.store'), [
                'request_number' => 'MIO-2026-999',
                'resident_id' => $resident->id,
                'unit_id' => $unit->id,
                'request_type' => 'Pindah Masuk',
                'scheduled_date' => '2026-06-25',
                'status' => 'Menunggu Approval',
                'status_note' => 'Kuning',
            ])
            ->assertRedirect(route('resident-management.move-in-out'));

        $moveRequest = ResidentMoveRequest::query()->where('request_number', 'MIO-2026-999')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('resident-management.move-in-out.update', $moveRequest), [
                'request_number' => 'MIO-2026-999',
                'resident_id' => $resident->id,
                'unit_id' => $unit->id,
                'request_type' => 'Pindah Masuk',
                'scheduled_date' => '2026-06-26',
                'status' => 'Selesai',
                'status_note' => 'Biru',
            ])
            ->assertRedirect(route('resident-management.move-in-out'));

        $this->assertDatabaseHas('resident_move_requests', [
            'id' => $moveRequest->id,
            'status' => 'Selesai',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('resident-management.move-in-out.destroy', $moveRequest))
            ->assertRedirect(route('resident-management.move-in-out'));

        $this->assertDatabaseMissing('resident_move_requests', [
            'id' => $moveRequest->id,
        ]);
    }

    public function test_admin_can_create_update_and_delete_family_member(): void
    {
        $resident = Resident::query()->where('name', 'Ahmad Rizky')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('resident-management.family-members.store'), [
                'resident_id' => $resident->id,
                'name' => 'Nadia Child',
                'relationship' => 'Anak',
                'birth_date' => '2021-01-02',
                'access_status' => 'Aktif',
            ])
            ->assertRedirect(route('resident-management.family-members'));

        $member = ResidentFamilyMember::query()->where('name', 'Nadia Child')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('resident-management.family-members.update', $member), [
                'resident_id' => $resident->id,
                'name' => 'Nadia Child Updated',
                'relationship' => 'Anak',
                'birth_date' => '2021-01-03',
                'access_status' => 'Menunggu Approval',
            ])
            ->assertRedirect(route('resident-management.family-members'));

        $this->assertDatabaseHas('resident_family_members', [
            'id' => $member->id,
            'name' => 'Nadia Child Updated',
            'access_status' => 'Menunggu Approval',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('resident-management.family-members.destroy', $member))
            ->assertRedirect(route('resident-management.family-members'));

        $this->assertDatabaseMissing('resident_family_members', [
            'id' => $member->id,
        ]);
    }

    public function test_admin_can_create_update_and_delete_vehicle(): void
    {
        $resident = Resident::query()->where('name', 'Mark Wang')->firstOrFail();
        $unit = Unit::query()->where('code', 'A-2002')->firstOrFail();

        $this->actingAs($this->admin)
            ->post(route('resident-management.vehicles.store'), [
                'resident_id' => $resident->id,
                'unit_id' => $unit->id,
                'plate_number' => 'B 7777 XYZ',
                'vehicle_type' => 'Mobil',
                'owner_name' => 'Mark Wang',
                'make_model' => 'Mazda CX-5',
                'parking_status' => 'Aktif',
                'slot_label' => 'A-77',
            ])
            ->assertRedirect(route('resident-management.vehicles'));

        $vehicle = ResidentVehicle::query()->where('plate_number', 'B 7777 XYZ')->firstOrFail();

        $this->actingAs($this->admin)
            ->put(route('resident-management.vehicles.update', $vehicle), [
                'resident_id' => $resident->id,
                'unit_id' => $unit->id,
                'plate_number' => 'B 7777 XYZ',
                'vehicle_type' => 'Mobil',
                'owner_name' => 'Mark Wang Updated',
                'make_model' => 'Mazda CX-60',
                'parking_status' => 'Menunggu Approval',
                'slot_label' => 'A-78',
            ])
            ->assertRedirect(route('resident-management.vehicles'));

        $this->assertDatabaseHas('resident_vehicles', [
            'id' => $vehicle->id,
            'owner_name' => 'Mark Wang Updated',
            'parking_status' => 'Menunggu Approval',
        ]);

        $this->actingAs($this->admin)
            ->delete(route('resident-management.vehicles.destroy', $vehicle))
            ->assertRedirect(route('resident-management.vehicles'));

        $this->assertDatabaseMissing('resident_vehicles', [
            'id' => $vehicle->id,
        ]);
    }

    public function test_admin_cannot_create_resident_when_maximum_capacity_is_reached(): void
    {
        $unit = Unit::query()->where('code', 'C-1204')->firstOrFail();

        AppSetting::putInteger('resident_max_capacity', Resident::query()->count());

        $this->actingAs($this->admin)
            ->post(route('resident-management.residents.store'), [
                'unit_id' => $unit->id,
                'name' => 'Blocked Resident',
                'resident_type' => 'Penyewa',
                'status' => 'Aktif',
                'move_in_date' => '2026-06-14',
                'avatar_tone' => 'female',
            ])
            ->assertSessionHasErrors('resident_limit');

        $this->assertDatabaseMissing('residents', [
            'name' => 'Blocked Resident',
        ]);
    }

    public function test_admin_can_update_resident_capacity_configuration(): void
    {
        $this->actingAs($this->admin)
            ->patch(route('resident-management.settings.resident-capacity'), [
                'max_residents' => 250,
            ])
            ->assertRedirect(route('resident-management.residents'));

        $this->assertSame(250, AppSetting::getInteger('resident_max_capacity'));
    }

    public function test_resident_listing_uses_gender_column_and_unit_page_hides_photo_controls(): void
    {
        $this->actingAs($this->admin)
            ->get(route('resident-management.residents'))
            ->assertOk()
            ->assertSee('Gender')
            ->assertDontSee('>Foto<', false);

        $this->actingAs($this->admin)
            ->get(route('resident-management.units'))
            ->assertOk()
            ->assertDontSee('Foto Unit')
            ->assertDontSee('Thumbnail Tone');
    }

    public function test_resident_and_unit_filters_render_auto_submit_markup(): void
    {
        $this->actingAs($this->admin)
            ->get(route('resident-management.residents', ['tower' => 'Tower A']))
            ->assertOk()
            ->assertSee('data-auto-submit-get', false)
            ->assertSee('data-auto-submit-control', false);

        $this->actingAs($this->admin)
            ->get(route('resident-management.units', ['tower' => 'Tower A']))
            ->assertOk()
            ->assertSee('data-auto-submit-get', false)
            ->assertSee('data-auto-submit-control', false);
    }
}
