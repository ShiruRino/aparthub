<?php

namespace Tests\Feature;

use App\Models\Announcement;
use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use App\Models\ResidentMoveRequest;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestEvent;
use App\Models\Unit;
use App\Models\User;
use App\Models\Visitor;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardStabilizationFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_real_records_for_metrics_and_activity_feed(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('username', 'admin')->firstOrFail();
        $resident = Resident::query()->firstOrFail();
        $unit = $resident->unit ?? Unit::query()->firstOrFail();

        $ticket = ServiceRequest::query()->create([
            'ticket_number' => 'SR-2026-999',
            'resident_id' => $resident->id,
            'category' => 'Plumbing',
            'title' => 'Emergency Water Leak',
            'description' => 'Water leak in kitchen area.',
            'priority' => ServiceRequest::PRIORITY_EMERGENCY,
            'status' => ServiceRequest::STATUS_ASSIGNED,
            'source' => 'Resident App',
            'assigned_at' => now(),
        ]);

        ServiceRequestEvent::query()->create([
            'service_request_id' => $ticket->id,
            'acted_by_user_id' => $admin->id,
            'event_type' => 'Assigned',
            'from_status' => ServiceRequest::STATUS_SUBMITTED,
            'to_status' => ServiceRequest::STATUS_ASSIGNED,
        ]);

        $visitor = Visitor::query()->create([
            'resident_id' => $resident->id,
            'visitor_name' => 'Dashboard Guest',
            'visitor_phone' => '081233344455',
            'visit_date' => today()->toDateString(),
            'estimated_arrival_time' => '10:00:00',
            'guest_count' => 2,
            'visit_purpose' => 'Meeting',
            'status' => Visitor::STATUS_CHECKED_IN,
            'registration_source' => Visitor::SOURCE_ADMIN_WALK_IN,
            'access_code' => 'DASHBOARD-GUEST',
            'approved_at' => now(),
            'checked_in_at' => now(),
            'expires_at' => now()->endOfDay(),
        ]);

        $facility = Facility::query()->create([
            'name' => 'Function Hall Dashboard',
            'location' => 'Tower A',
            'category' => 'Community',
            'status' => 'Booked',
            'capacity' => 80,
            'description' => 'Function hall for dashboard test.',
        ]);

        FacilityBooking::query()->create([
            'facility_id' => $facility->id,
            'resident_id' => $resident->id,
            'booking_title' => 'Community Sync Session',
            'booking_date' => today()->toDateString(),
            'time_slot' => '19:00 - 21:00',
            'status' => 'Confirmed',
            'notes' => 'Dashboard booking note',
        ]);

        Announcement::query()->create([
            'title' => 'Water Shutdown Advisory',
            'content' => 'Maintenance at Tower A.',
            'category' => 'Maintenance',
            'status' => Announcement::STATUS_PUBLISHED,
            'is_pinned' => true,
            'published_at' => now(),
        ]);

        ResidentMoveRequest::query()->create([
            'request_number' => 'MIO-DASH-001',
            'resident_id' => $resident->id,
            'unit_id' => $unit->id,
            'request_type' => 'Pindah Masuk',
            'scheduled_date' => today()->toDateString(),
            'status' => 'Selesai',
            'status_note' => 'Dashboard test',
        ]);

        $response = $this->actingAs($admin)->get(route('dashboard'));

        $response->assertOk()
            ->assertSee('Operational Overview')
            ->assertSee('Service Request Snapshot')
            ->assertSee('Emergency Water Leak')
            ->assertSee('Water Shutdown Advisory')
            ->assertSee('Community Sync Session')
            ->assertSee('MIO-DASH-001')
            ->assertSee((string) Resident::query()->count())
            ->assertSee((string) Visitor::query()->whereDate('visit_date', today())->count())
            ->assertSee((string) Facility::query()->count());
    }
}
