<?php

namespace Database\Seeders;

use App\Models\Facility;
use App\Models\FacilityBooking;
use App\Models\Resident;
use Illuminate\Database\Seeder;

class FacilityManagementSeeder extends Seeder
{
    /**
     * Seed facility management data.
     */
    public function run(): void
    {
        $facilities = collect([
            ['name' => 'Sky Lounge', 'location' => 'Tower A - Level 25', 'category' => 'Event Space', 'status' => 'Available', 'capacity' => 60, 'description' => 'Private lounge for resident events and gatherings.'],
            ['name' => 'Meeting Room', 'location' => 'Tower B - Level 3', 'category' => 'Meeting', 'status' => 'Booked', 'capacity' => 12, 'description' => 'Reservable meeting room for private sessions.'],
            ['name' => 'Swimming Pool', 'location' => 'Central Podium', 'category' => 'Recreation', 'status' => 'Maintenance', 'capacity' => 80, 'description' => 'Shared pool area with scheduled maintenance windows.'],
            ['name' => 'Function Hall', 'location' => 'Tower C - Level 1', 'category' => 'Event Space', 'status' => 'Available', 'capacity' => 120, 'description' => 'Large function hall for community activities.'],
        ])->mapWithKeys(fn (array $facility) => [
            $facility['name'] => Facility::query()->updateOrCreate(['name' => $facility['name']], $facility),
        ]);

        $residents = Resident::query()->get()->keyBy('name');

        foreach ([
            ['facility' => 'Meeting Room', 'resident' => 'Ahmad Rizky', 'booking_title' => 'Board Meeting', 'booking_date' => now()->addDay()->toDateString(), 'time_slot' => '09:00 - 11:00', 'status' => 'Confirmed', 'notes' => 'Internal coordination meeting.'],
            ['facility' => 'Sky Lounge', 'resident' => 'Sarah Lim', 'booking_title' => 'Family Gathering', 'booking_date' => now()->addDays(2)->toDateString(), 'time_slot' => '18:00 - 21:00', 'status' => 'Pending', 'notes' => 'Awaiting final approval from management.'],
            ['facility' => 'Function Hall', 'resident' => 'Mark Wang', 'booking_title' => 'Tenant Workshop', 'booking_date' => now()->addDays(4)->toDateString(), 'time_slot' => '13:00 - 16:00', 'status' => 'Confirmed', 'notes' => 'Joint event with marketplace tenants.'],
        ] as $booking) {
            FacilityBooking::query()->updateOrCreate(
                [
                    'facility_id' => $facilities[$booking['facility']]->id,
                    'booking_title' => $booking['booking_title'],
                ],
                [
                    'resident_id' => $residents[$booking['resident']]?->id,
                    'booking_date' => $booking['booking_date'],
                    'time_slot' => $booking['time_slot'],
                    'status' => $booking['status'],
                    'notes' => $booking['notes'],
                ]
            );
        }
    }
}
