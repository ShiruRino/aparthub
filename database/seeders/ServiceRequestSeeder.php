<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestSubcategory;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Seed the service request operational data.
     */
    public function run(): void
    {
        $residents = Resident::query()->get()->keyBy('name');
        $categories = ServiceRequestCategory::query()->get()->keyBy('name');
        $subcategories = ServiceRequestSubcategory::query()
            ->with('category')
            ->get()
            ->keyBy(fn (ServiceRequestSubcategory $subcategory) => $subcategory->category?->name.'::'.$subcategory->name);

        foreach ([
            ['ticket_number' => 'SR-2026-001', 'resident' => 'Ahmad Rizky', 'category' => 'Plumbing', 'subcategory' => 'Leak Repair', 'title' => 'Kitchen faucet leaking', 'description' => 'Leak under sink cabinet needs immediate check.', 'priority' => 'High', 'status' => 'New', 'source' => 'Resident App', 'assigned_to' => null, 'created_at' => now()->subHours(7)],
            ['ticket_number' => 'SR-2026-002', 'resident' => 'Sarah Lim', 'category' => 'AC', 'subcategory' => 'Cooling Issue', 'title' => 'Master bedroom AC warm', 'description' => 'Cooling dropped since last night.', 'priority' => 'Emergency', 'status' => 'Assigned', 'source' => 'Front Office', 'assigned_to' => 'John Technical', 'assigned_at' => now()->subHours(5), 'created_at' => now()->subHours(6)],
            ['ticket_number' => 'SR-2026-003', 'resident' => 'John Doe', 'category' => 'Electrical', 'subcategory' => 'Power Trip', 'title' => 'Living room power trip', 'description' => 'Breaker frequently trips when lights are turned on.', 'priority' => 'High', 'status' => 'In Progress', 'source' => 'Phone Call', 'assigned_to' => 'Michael Eng.', 'assigned_at' => now()->subHours(4), 'in_progress_at' => now()->subHours(3), 'created_at' => now()->subHours(5)],
            ['ticket_number' => 'SR-2026-004', 'resident' => 'Jane Smith', 'category' => 'Housekeeping', 'subcategory' => 'Common Area Cleaning', 'title' => 'Corridor deep cleaning', 'description' => 'Post move-out cleanup request.', 'priority' => 'Low', 'status' => 'Pending', 'source' => 'Management', 'assigned_to' => 'Housekeeping Team', 'created_at' => now()->subHours(4)],
            ['ticket_number' => 'SR-2026-005', 'resident' => 'Mark Wang', 'category' => 'Internet', 'subcategory' => 'Connection Unstable', 'title' => 'Router signal unstable', 'description' => 'Connection drops every few minutes.', 'priority' => 'Medium', 'status' => 'Over SLA', 'source' => 'Resident App', 'assigned_to' => 'Vendor ISP', 'assigned_at' => now()->subHours(20), 'in_progress_at' => now()->subHours(18), 'created_at' => now()->subDays(1)],
            ['ticket_number' => 'SR-2026-006', 'resident' => 'Kevin Chen', 'category' => 'General', 'subcategory' => 'General Assistance', 'title' => 'Lobby light replacement', 'description' => 'Hallway decorative light needs replacement.', 'priority' => 'Medium', 'status' => 'Completed', 'source' => 'Security Desk', 'assigned_to' => 'Budi Maintenance', 'assigned_at' => now()->subDays(2)->addHours(3), 'in_progress_at' => now()->subDays(2)->addHours(6), 'completed_at' => now()->subHours(2), 'completion_notes' => 'Lamp replaced and tested.', 'created_at' => now()->subDays(2)],
        ] as $request) {
            $resident = $residents->get($request['resident']);
            $category = $categories->get($request['category']);
            $subcategory = $subcategories->get($request['category'].'::'.$request['subcategory']);
            $slaMinutes = $subcategory?->slaMinutesFor($request['priority']);
            $createdAt = $request['created_at'];

            ServiceRequest::query()->updateOrCreate(
                ['ticket_number' => $request['ticket_number']],
                [
                    'resident_id' => $resident?->id,
                    'service_request_category_id' => $category?->id,
                    'service_request_subcategory_id' => $subcategory?->id,
                    'category' => $request['category'],
                    'title' => $request['title'],
                    'description' => $request['description'],
                    'priority' => $request['priority'],
                    'status' => $request['status'],
                    'source' => $request['source'],
                    'sla_target_minutes' => $slaMinutes,
                    'sla_due_at' => $slaMinutes ? $createdAt->copy()->addMinutes($slaMinutes) : null,
                    'assigned_to' => $request['assigned_to'],
                    'assigned_at' => $request['assigned_at'] ?? null,
                    'in_progress_at' => $request['in_progress_at'] ?? null,
                    'completion_notes' => $request['completion_notes'] ?? null,
                    'completed_at' => $request['completed_at'] ?? null,
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ]
            );
        }
    }
}
