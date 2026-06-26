<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class ServiceRequestFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_service_request_with_emergency_priority(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();
        $residentId = Resident::query()->value('id');
        $subcategory = ServiceRequestSubcategory::query()->with('category')->firstOrFail();

        $this->actingAs($admin)
            ->post(route('service-request.store'), [
                'resident_id' => $residentId,
                'category_id' => $subcategory->service_request_category_id,
                'subcategory_id' => $subcategory->id,
                'title' => 'Main breaker urgent issue',
                'description' => 'Emergency shutdown in unit panel.',
                'priority' => 'Emergency',
                'source' => 'Front Office',
                'assigned_to' => 'Night Technician',
            ])
            ->assertRedirect(route('service-request.ticket-queue'));

        $serviceRequest = ServiceRequest::query()->where('title', 'Main breaker urgent issue')->firstOrFail();

        $this->assertSame('Emergency', $serviceRequest->priority);
        $this->assertSame(ServiceRequest::STATUS_SUBMITTED, ServiceRequest::mobileVisibleStatus($serviceRequest->status));
        $this->assertNotNull($serviceRequest->sla_due_at);
        $this->assertNotNull($serviceRequest->created_at);
    }

    public function test_dashboard_shows_consolidated_service_widgets(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Operational Overview')
            ->assertSee('Service Request Snapshot')
            ->assertSee('Service Request Status')
            ->assertSee('Recent Activities');
    }

    public function test_service_request_pagination_keeps_compact_markup_with_filters(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('service-request.ticket-queue', ['status' => ServiceRequest::STATUS_SUBMITTED]))
            ->assertOk()
            ->assertSee('resident-pagination');
    }

    public function test_dashboard_over_sla_summary_uses_sla_due_at_query(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();
        $resident = Resident::query()->firstOrFail();
        $category = ServiceRequestCategory::query()->firstOrFail();
        $subcategory = ServiceRequestSubcategory::query()->where('service_request_category_id', $category->id)->firstOrFail();

        ServiceRequest::query()->create([
            'ticket_number' => 'SR-TEST-OVER-1',
            'resident_id' => $resident->id,
            'service_request_category_id' => $category->id,
            'service_request_subcategory_id' => $subcategory->id,
            'category' => $category->name,
            'title' => 'Past due request',
            'description' => 'Should count as over SLA.',
            'priority' => ServiceRequest::PRIORITY_HIGH,
            'status' => ServiceRequest::STATUS_ASSIGNED,
            'source' => 'Front Office',
            'sla_target_minutes' => 60,
            'sla_due_at' => now()->subHour(),
        ]);

        ServiceRequest::query()->create([
            'ticket_number' => 'SR-TEST-OVER-2',
            'resident_id' => $resident->id,
            'service_request_category_id' => $category->id,
            'service_request_subcategory_id' => $subcategory->id,
            'category' => $category->name,
            'title' => 'Within SLA request',
            'description' => 'Should not count as over SLA.',
            'priority' => ServiceRequest::PRIORITY_HIGH,
            'status' => ServiceRequest::STATUS_ASSIGNED,
            'source' => 'Front Office',
            'sla_target_minutes' => 60,
            'sla_due_at' => now()->addHour(),
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertViewHas('serviceSummary', fn (array $summary) => $summary['over_sla'] >= 1);
    }

    public function test_assignment_board_and_sla_monitoring_routes_are_removed(): void
    {
        $this->assertFalse(Route::has('service-request.assignment-board'));
        $this->assertFalse(Route::has('service-request.sla-monitoring'));
    }
}
