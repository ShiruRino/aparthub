<?php

namespace Tests\Feature;

use App\Models\Facility;
use App\Models\Module;
use App\Models\Resident;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacilityManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_open_facility_workspace(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('facility-management.index'))
            ->assertOk()
            ->assertSee('Facility Management')
            ->assertSee('Facility Bookings');
    }

    public function test_admin_can_create_facility_and_booking(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();
        $resident = Resident::query()->firstOrFail();

        $this->actingAs($admin)
            ->post(route('facility-management.facilities.store'), [
                'name' => 'Padel Court',
                'location' => 'Tower D - Rooftop',
                'category' => 'Sports',
                'status' => 'Available',
                'capacity' => 4,
                'description' => 'New sports court.',
            ])
            ->assertRedirect(route('facility-management.index'));

        $facility = Facility::query()->where('name', 'Padel Court')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('facility-management.bookings.store'), [
                'facility_id' => $facility->id,
                'resident_id' => $resident->id,
                'booking_title' => 'Morning Session',
                'booking_date' => now()->addDay()->toDateString(),
                'time_slot' => '07:00 - 08:00',
                'status' => 'Confirmed',
                'notes' => 'Test booking',
            ])
            ->assertRedirect(route('facility-management.index'));

        $this->assertDatabaseHas('facility_bookings', [
            'facility_id' => $facility->id,
            'booking_title' => 'Morning Session',
        ]);
    }

    public function test_facility_module_slug_is_protected(): void
    {
        $this->seed(DatabaseSeeder::class);

        $module = Module::query()->where('slug', 'facility-management')->firstOrFail();

        $this->assertTrue($module->isSystem());
    }
}
