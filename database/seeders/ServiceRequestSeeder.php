<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\ServiceRequest;
use Illuminate\Database\Seeder;

class ServiceRequestSeeder extends Seeder
{
    /**
     * Seed the service request operational data.
     */
    public function run(): void
    {
        $residents = Resident::query()->get()->keyBy('name');

        foreach ([
            ['ticket_number' => 'SR-2026-001', 'resident' => 'Ahmad Rizky', 'category' => 'Plumbing', 'title' => 'Kitchen faucet leaking', 'description' => 'Leak under sink cabinet needs immediate check.', 'priority' => 'High', 'status' => 'New', 'source' => 'Mobile App', 'assigned_to' => null, 'created_at' => now()->subHours(7)],
            ['ticket_number' => 'SR-2026-002', 'resident' => 'Sarah Lim', 'category' => 'AC', 'title' => 'Master bedroom AC warm', 'description' => 'Cooling dropped since last night.', 'priority' => 'Emergency', 'status' => 'Assigned', 'source' => 'Front Office', 'assigned_to' => 'John Technical', 'created_at' => now()->subHours(6)],
            ['ticket_number' => 'SR-2026-003', 'resident' => 'John Doe', 'category' => 'Electrical', 'title' => 'Living room power trip', 'description' => 'Breaker frequently trips when lights are turned on.', 'priority' => 'High', 'status' => 'In Progress', 'source' => 'Phone Call', 'assigned_to' => 'Michael Eng.', 'created_at' => now()->subHours(5)],
            ['ticket_number' => 'SR-2026-004', 'resident' => 'Jane Smith', 'category' => 'Housekeeping', 'title' => 'Corridor deep cleaning', 'description' => 'Post move-out cleanup request.', 'priority' => 'Low', 'status' => 'Pending', 'source' => 'Management', 'assigned_to' => 'Housekeeping Team', 'created_at' => now()->subHours(4)],
            ['ticket_number' => 'SR-2026-005', 'resident' => 'Mark Wang', 'category' => 'Internet', 'title' => 'Router signal unstable', 'description' => 'Connection drops every few minutes.', 'priority' => 'Medium', 'status' => 'Over SLA', 'source' => 'Resident App', 'assigned_to' => 'Vendor ISP', 'created_at' => now()->subDays(1)],
            ['ticket_number' => 'SR-2026-006', 'resident' => 'Kevin Chen', 'category' => 'General', 'title' => 'Lobby light replacement', 'description' => 'Hallway decorative light needs replacement.', 'priority' => 'Medium', 'status' => 'Completed', 'source' => 'Security Desk', 'assigned_to' => 'Budi Maintenance', 'completed_at' => now()->subHours(2), 'completion_notes' => 'Lamp replaced and tested.', 'created_at' => now()->subDays(2)],
        ] as $request) {
            $resident = $residents->get($request['resident']);

            ServiceRequest::query()->updateOrCreate(
                ['ticket_number' => $request['ticket_number']],
                [
                    'resident_id' => $resident?->id,
                    'category' => $request['category'],
                    'title' => $request['title'],
                    'description' => $request['description'],
                    'priority' => $request['priority'],
                    'status' => $request['status'],
                    'source' => $request['source'],
                    'assigned_to' => $request['assigned_to'],
                    'completion_notes' => $request['completion_notes'] ?? null,
                    'completed_at' => $request['completed_at'] ?? null,
                    'created_at' => $request['created_at'],
                    'updated_at' => now(),
                ]
            );
        }
    }
}
