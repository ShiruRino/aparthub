<?php

namespace Database\Seeders;

use App\Models\Resident;
use App\Models\ResidentFamilyMember;
use App\Models\ResidentMoveRequest;
use App\Models\ResidentVehicle;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class ResidentManagementSeeder extends Seeder
{
    /**
     * Seed resident management operational data.
     */
    public function run(): void
    {
        $units = collect([
            ['code' => 'A-1808', 'tower' => 'Tower A', 'floor' => 18, 'unit_type' => '2BR Premium', 'occupancy_status' => 'Terisi', 'payment_status' => 'Lunas', 'thumbnail_tone' => 'default'],
            ['code' => 'B-2001', 'tower' => 'Tower B', 'floor' => 20, 'unit_type' => '1BR Deluxe', 'occupancy_status' => 'Kosong', 'payment_status' => 'Cicilan/Lunas', 'thumbnail_tone' => 'empty'],
            ['code' => 'A-0503', 'tower' => 'Tower A', 'floor' => 5, 'unit_type' => 'Studio', 'occupancy_status' => 'Perbaikan', 'payment_status' => 'Cicilan/Lunas', 'thumbnail_tone' => 'repair'],
            ['code' => 'A-2002', 'tower' => 'Tower A', 'floor' => 20, 'unit_type' => '2BR Premium', 'occupancy_status' => 'Terisi', 'payment_status' => 'Cicilan/Lunas', 'thumbnail_tone' => 'default'],
            ['code' => 'C-1204', 'tower' => 'Tower C', 'floor' => 12, 'unit_type' => '3BR Deluxe', 'occupancy_status' => 'Menunggu Inspeksi', 'payment_status' => 'Belum Lunas', 'thumbnail_tone' => 'empty'],
        ])->mapWithKeys(fn (array $unit) => [
            $unit['code'] => Unit::query()->updateOrCreate(
                ['code' => $unit['code']],
                $unit
            ),
        ]);

        $residents = collect([
            ['name' => 'Ahmad Rizky', 'unit_id' => $units['A-1808']->id, 'resident_type' => 'Pemilik', 'status' => 'Aktif', 'move_in_date' => '2026-06-07', 'avatar_tone' => 'default'],
            ['name' => 'Sarah Lim', 'unit_id' => $units['A-1808']->id, 'resident_type' => 'Penyewa', 'status' => 'Aktif', 'move_in_date' => '2026-06-07', 'avatar_tone' => 'female'],
            ['name' => 'John Doe', 'unit_id' => $units['B-2001']->id, 'resident_type' => 'Pemilik', 'status' => 'Menunggu Approval', 'move_in_date' => null, 'avatar_tone' => 'pending'],
            ['name' => 'Jane Smith', 'unit_id' => $units['A-0503']->id, 'resident_type' => 'Penyewa', 'status' => 'Keluar', 'move_in_date' => '2024-01-10', 'move_out_date' => '2024-05-15', 'avatar_tone' => 'out'],
            ['name' => 'Mark Wang', 'unit_id' => $units['A-2002']->id, 'resident_type' => 'Pemilik', 'status' => 'Aktif', 'move_in_date' => '2025-11-03', 'avatar_tone' => 'default'],
            ['name' => 'Kevin Chen', 'unit_id' => $units['C-1204']->id, 'resident_type' => 'Penyewa', 'status' => 'Menunggu Approval', 'move_in_date' => null, 'avatar_tone' => 'pending'],
        ])->mapWithKeys(fn (array $resident) => [
            $resident['name'] => Resident::query()->updateOrCreate(
                ['name' => $resident['name'], 'unit_id' => $resident['unit_id']],
                $resident
            ),
        ]);

        foreach ([
            ['resident' => 'Ahmad Rizky', 'name' => 'Sarah Lim', 'relationship' => 'Pasangan', 'birth_date' => '1995-09-12', 'access_status' => 'Aktif'],
            ['resident' => 'Ahmad Rizky', 'name' => 'Alya Rizky', 'relationship' => 'Anak', 'birth_date' => '2020-03-05', 'access_status' => 'Aktif'],
            ['resident' => 'John Doe', 'name' => 'Sarah Lim', 'relationship' => 'Pasangan', 'birth_date' => null, 'access_status' => 'Aktif'],
            ['resident' => 'Mark Wang', 'name' => 'Ibu Mark Wang', 'relationship' => 'Orang Tua', 'birth_date' => null, 'access_status' => 'Aktif'],
            ['resident' => 'Jane Smith', 'name' => 'Anggota Baru Unit A-0503', 'relationship' => 'Anak', 'birth_date' => null, 'access_status' => 'Menunggu Approval'],
        ] as $member) {
            ResidentFamilyMember::query()->updateOrCreate(
                ['resident_id' => $residents[$member['resident']]->id, 'name' => $member['name']],
                [
                    'relationship' => $member['relationship'],
                    'birth_date' => $member['birth_date'],
                    'access_status' => $member['access_status'],
                ]
            );
        }

        foreach ([
            ['request_number' => 'MOI-2026-001', 'resident' => 'Sarah Lim', 'unit' => 'A-1808', 'request_type' => 'Pindah Keluar', 'scheduled_date' => '2026-06-15', 'status' => 'Menunggu Approval', 'status_note' => 'Kuning'],
            ['request_number' => 'MIO-2026-002', 'resident' => 'John Doe', 'unit' => 'B-2001', 'request_type' => 'Pindah Masuk', 'scheduled_date' => '2026-06-10', 'status' => 'Menunggu Approval', 'status_note' => 'Kuning'],
            ['request_number' => 'MIO-2026-003', 'resident' => 'Jane Smith', 'unit' => 'A-0503', 'request_type' => 'Pindah Masuk', 'scheduled_date' => null, 'status' => 'Sedang Berlangsung', 'status_note' => 'Biru'],
            ['request_number' => 'MOI-2026-004', 'resident' => 'Mark Wang', 'unit' => 'A-2002', 'request_type' => 'Pindah Keluar', 'scheduled_date' => '2026-06-08', 'status' => 'Selesai', 'status_note' => null],
            ['request_number' => 'MIO-2026-005', 'resident' => 'Kevin Chen', 'unit' => 'C-1204', 'request_type' => 'Pindah Masuk', 'scheduled_date' => '2026-06-20', 'status' => 'Menunggu Approval', 'status_note' => 'Kuning'],
        ] as $request) {
            ResidentMoveRequest::query()->updateOrCreate(
                ['request_number' => $request['request_number']],
                [
                    'resident_id' => $residents[$request['resident']]->id,
                    'unit_id' => $units[$request['unit']]->id,
                    'request_type' => $request['request_type'],
                    'scheduled_date' => $request['scheduled_date'],
                    'status' => $request['status'],
                    'status_note' => $request['status_note'],
                ]
            );
        }

        foreach ([
            ['plate_number' => 'B 1234 ABC', 'resident' => 'Ahmad Rizky', 'unit' => 'A-1808', 'vehicle_type' => 'Mobil', 'owner_name' => 'Ahmad Rizky', 'make_model' => 'Toyota Fortuner', 'parking_status' => 'Aktif', 'slot_label' => 'A-01'],
            ['plate_number' => 'B 5678 DEF', 'resident' => 'Sarah Lim', 'unit' => 'A-1808', 'vehicle_type' => 'Mobil', 'owner_name' => 'Sarah Lim', 'make_model' => 'Honda HR-V', 'parking_status' => 'Aktif', 'slot_label' => 'A-02'],
            ['plate_number' => 'B 9012 GHI', 'resident' => 'John Doe', 'unit' => 'B-2001', 'vehicle_type' => 'Mobil', 'owner_name' => 'John Doe', 'make_model' => 'BMW 3 Series', 'parking_status' => 'Aktif', 'slot_label' => 'B-01'],
            ['plate_number' => 'B 3456 JKL', 'resident' => 'Mark Wang', 'unit' => 'A-2002', 'vehicle_type' => 'Mobil', 'owner_name' => 'Mark Wang', 'make_model' => 'Nissan X-Trail', 'parking_status' => 'Aktif', 'slot_label' => 'A-17'],
            ['plate_number' => 'B 7890 MNO', 'resident' => 'Jane Smith', 'unit' => 'A-0503', 'vehicle_type' => 'Motor', 'owner_name' => 'Jane Smith', 'make_model' => 'Yamaha NMax', 'parking_status' => 'Menunggu Approval', 'slot_label' => null],
        ] as $vehicle) {
            ResidentVehicle::query()->updateOrCreate(
                ['plate_number' => $vehicle['plate_number']],
                [
                    'resident_id' => $residents[$vehicle['resident']]->id,
                    'unit_id' => $units[$vehicle['unit']]->id,
                    'vehicle_type' => $vehicle['vehicle_type'],
                    'owner_name' => $vehicle['owner_name'],
                    'make_model' => $vehicle['make_model'],
                    'parking_status' => $vehicle['parking_status'],
                    'slot_label' => $vehicle['slot_label'],
                ]
            );
        }
    }
}
