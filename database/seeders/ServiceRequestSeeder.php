<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestCategory;
use App\Models\ServiceRequestEvent;
use App\Models\ServiceRequestSubcategory;
use App\Models\TechnicianTeam;
use App\Models\User;
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
        $teams = TechnicianTeam::query()->get()->keyBy('name');
        $actors = User::query()->whereIn('username', ['admin', 'tech.budi', 'tech.rizky', 'tech.dewi'])->get()->keyBy('username');

        foreach ([
            ['ticket_number' => 'SR-2026-001', 'resident' => 'Ahmad Rizky', 'category' => 'Plumbing', 'subcategory' => 'Leak Repair', 'title' => 'Kitchen faucet leaking', 'description' => 'Leak under sink cabinet needs immediate check.', 'priority' => 'High', 'status' => 'Submitted', 'source' => 'Resident App', 'assigned_to' => null, 'team' => null, 'created_at' => now()->subHours(7), 'events' => [['type' => 'created', 'actor' => 'admin', 'from' => null, 'to' => 'Submitted']]],
            ['ticket_number' => 'SR-2026-002', 'resident' => 'Sarah Lim', 'category' => 'AC', 'subcategory' => 'Cooling Issue', 'title' => 'Master bedroom AC warm', 'description' => 'Cooling dropped since last night.', 'priority' => 'Emergency', 'status' => 'Assigned', 'source' => 'Front Office', 'assigned_to' => 'Mechanical Response Team', 'team' => 'Mechanical Response Team', 'assigned_at' => now()->subHours(5), 'scheduled_at' => now()->addHours(2), 'created_at' => now()->subHours(6), 'events' => [['type' => 'assigned', 'actor' => 'admin', 'from' => 'Submitted', 'to' => 'Assigned']]],
            ['ticket_number' => 'SR-2026-003', 'resident' => 'John Doe', 'category' => 'Electrical', 'subcategory' => 'Power Trip', 'title' => 'Living room power trip', 'description' => 'Breaker frequently trips when lights are turned on.', 'priority' => 'High', 'status' => 'On The Way', 'source' => 'Phone Call', 'assigned_to' => 'Electrical Rapid Team', 'team' => 'Electrical Rapid Team', 'assigned_at' => now()->subHours(4), 'on_the_way_at' => now()->subHours(3), 'estimated_arrival_minutes' => 25, 'created_at' => now()->subHours(5), 'events' => [['type' => 'assigned', 'actor' => 'admin', 'from' => 'Submitted', 'to' => 'Assigned'], ['type' => 'on_the_way', 'actor' => 'tech.rizky', 'from' => 'Assigned', 'to' => 'On The Way']]],
            ['ticket_number' => 'SR-2026-004', 'resident' => 'Jane Smith', 'category' => 'Housekeeping', 'subcategory' => 'Common Area Cleaning', 'title' => 'Corridor deep cleaning', 'description' => 'Post move-out cleanup request.', 'priority' => 'Low', 'status' => 'Submitted', 'source' => 'Management', 'assigned_to' => null, 'team' => null, 'created_at' => now()->subHours(4), 'events' => [['type' => 'created', 'actor' => 'admin', 'from' => null, 'to' => 'Submitted']]],
            ['ticket_number' => 'SR-2026-005', 'resident' => 'Mark Wang', 'category' => 'Internet', 'subcategory' => 'Connection Unstable', 'title' => 'Router signal unstable', 'description' => 'Connection drops every few minutes.', 'priority' => 'Medium', 'status' => 'In Progress', 'source' => 'Resident App', 'assigned_to' => 'Vendor Coordination Team', 'team' => 'Vendor Coordination Team', 'assigned_at' => now()->subHours(20), 'on_the_way_at' => now()->subHours(19), 'in_progress_at' => now()->subHours(18), 'created_at' => now()->subDays(1), 'events' => [['type' => 'assigned', 'actor' => 'admin', 'from' => 'Submitted', 'to' => 'Assigned'], ['type' => 'on_the_way', 'actor' => 'tech.dewi', 'from' => 'Assigned', 'to' => 'On The Way'], ['type' => 'started', 'actor' => 'tech.dewi', 'from' => 'On The Way', 'to' => 'In Progress']]],
            ['ticket_number' => 'SR-2026-006', 'resident' => 'Kevin Chen', 'category' => 'General', 'subcategory' => 'General Assistance', 'title' => 'Lobby light replacement', 'description' => 'Hallway decorative light needs replacement.', 'priority' => 'Medium', 'status' => 'Completed', 'source' => 'Security Desk', 'assigned_to' => 'Mechanical Response Team', 'team' => 'Mechanical Response Team', 'assigned_at' => now()->subDays(2)->addHours(3), 'on_the_way_at' => now()->subDays(2)->addHours(5), 'in_progress_at' => now()->subDays(2)->addHours(6), 'completed_at' => now()->subHours(2), 'completion_notes' => 'Lamp replaced and tested.', 'created_at' => now()->subDays(2), 'events' => [['type' => 'assigned', 'actor' => 'admin', 'from' => 'Submitted', 'to' => 'Assigned'], ['type' => 'on_the_way', 'actor' => 'tech.budi', 'from' => 'Assigned', 'to' => 'On The Way'], ['type' => 'started', 'actor' => 'tech.budi', 'from' => 'On The Way', 'to' => 'In Progress'], ['type' => 'completed', 'actor' => 'tech.budi', 'from' => 'In Progress', 'to' => 'Completed']]],
        ] as $request) {
            $resident = $residents->get($request['resident']);
            $category = $categories->get($request['category']);
            $subcategory = $subcategories->get($request['category'].'::'.$request['subcategory']);
            $team = ! empty($request['team']) ? $teams->get($request['team']) : null;
            $slaMinutes = $subcategory?->slaMinutesFor($request['priority']);
            $createdAt = $request['created_at'];

            $ticket = ServiceRequest::query()->updateOrCreate(
                ['ticket_number' => $request['ticket_number']],
                [
                    'resident_id' => $resident?->id,
                    'service_request_category_id' => $category?->id,
                    'service_request_subcategory_id' => $subcategory?->id,
                    'technician_team_id' => $team?->id,
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
                    'scheduled_at' => $request['scheduled_at'] ?? null,
                    'on_the_way_at' => $request['on_the_way_at'] ?? null,
                    'estimated_arrival_minutes' => $request['estimated_arrival_minutes'] ?? null,
                    'in_progress_at' => $request['in_progress_at'] ?? null,
                    'completion_notes' => $request['completion_notes'] ?? null,
                    'completed_at' => $request['completed_at'] ?? null,
                    'created_at' => $createdAt,
                    'updated_at' => now(),
                ]
            );

            foreach ($request['events'] ?? [] as $index => $event) {
                ServiceRequestEvent::query()->updateOrCreate(
                    [
                        'service_request_id' => $ticket->id,
                        'event_type' => $event['type'],
                        'to_status' => $event['to'],
                        'created_at' => $createdAt->copy()->addMinutes(($index + 1) * 15),
                    ],
                    [
                        'acted_by_user_id' => $actors[$event['actor']]?->id,
                        'from_status' => $event['from'],
                        'notes' => null,
                        'meta' => [],
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
