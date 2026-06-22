<?php

namespace Tests\Feature;

use App\Models\Resident;
use App\Models\ServiceRequest;
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

        $this->actingAs($admin)
            ->post(route('service-request.store'), [
                'resident_id' => $residentId,
                'category' => 'Electrical',
                'title' => 'Main breaker urgent issue',
                'description' => 'Emergency shutdown in unit panel.',
                'priority' => 'Emergency',
                'source' => 'Front Office',
                'assigned_to' => 'Night Technician',
            ])
            ->assertRedirect(route('service-request.ticket-queue'));

        $serviceRequest = ServiceRequest::query()->where('title', 'Main breaker urgent issue')->firstOrFail();

        $this->assertSame('Emergency', $serviceRequest->priority);
        $this->assertNotNull($serviceRequest->created_at);
    }

    public function test_dashboard_shows_consolidated_service_widgets(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Service Request Status')
            ->assertSee('Service Dispatch');
    }

    public function test_assignment_board_and_sla_monitoring_routes_are_removed(): void
    {
        $this->assertFalse(Route::has('service-request.assignment-board'));
        $this->assertFalse(Route::has('service-request.sla-monitoring'));
    }
}
