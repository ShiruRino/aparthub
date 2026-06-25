<?php

namespace Database\Seeders;

use App\Models\Module;
use App\Models\Role;
use App\Models\TechnicianTeam;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Database\Seeder;

class TechnicianManagementSeeder extends Seeder
{
    /**
     * Seed technician role, teams, and demo technician accounts.
     */
    public function run(): void
    {
        $technicianRole = Role::query()->updateOrCreate(
            ['slug' => 'technician'],
            ['name' => 'Technician']
        );

        $modules = Module::query()
            ->whereIn('slug', ['technician-management', 'service-request'])
            ->get()
            ->keyBy('slug');

        $teams = collect([
            ['name' => 'Mechanical Response Team', 'description' => 'Menangani plumbing, AC, dan general mechanical issues.', 'is_active' => true],
            ['name' => 'Electrical Rapid Team', 'description' => 'Menangani kelistrikan, panel, dan perangkat daya.', 'is_active' => true],
            ['name' => 'Vendor Coordination Team', 'description' => 'Koordinasi vendor eksternal dan schedule khusus.', 'is_active' => true],
        ])->map(fn (array $team) => TechnicianTeam::query()->updateOrCreate(
            ['name' => $team['name']],
            $team
        ))->keyBy('name');

        $technicians = [
            [
                'username' => 'tech.budi',
                'name' => 'Budi Santoso',
                'email' => 'budi.tech@example.com',
                'mobile_no' => '081300000111',
                'skills' => ['Plumbing', 'Water pump', 'General repair'],
                'certifications' => ['K3 Basic', 'Hydraulic Maintenance'],
                'teams' => ['Mechanical Response Team'],
            ],
            [
                'username' => 'tech.rizky',
                'name' => 'Rizky Maulana',
                'email' => 'rizky.tech@example.com',
                'mobile_no' => '081300000222',
                'skills' => ['Electrical panel', 'Breaker inspection', 'Lighting'],
                'certifications' => ['Electrical Safety LV1'],
                'teams' => ['Electrical Rapid Team'],
            ],
            [
                'username' => 'tech.dewi',
                'name' => 'Dewi Anggraini',
                'email' => 'dewi.tech@example.com',
                'mobile_no' => '081300000333',
                'skills' => ['Vendor coordination', 'QA handover', 'Documentation'],
                'certifications' => ['Vendor Control', 'Asset Verification'],
                'teams' => ['Vendor Coordination Team', 'Mechanical Response Team'],
            ],
        ];

        foreach ($technicians as $technician) {
            $user = User::query()->updateOrCreate(
                ['username' => $technician['username']],
                [
                    'role_id' => $technicianRole->id,
                    'name' => $technician['name'],
                    'email' => $technician['email'],
                    'mobile_no' => $technician['mobile_no'],
                    'is_active' => true,
                    'password' => 'password',
                ]
            );

            $user->technicianProfile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'skills' => $technician['skills'],
                    'certifications' => $technician['certifications'],
                    'notification_enabled' => true,
                ]
            );

            $user->technicianTeams()->sync(
                collect($technician['teams'])
                    ->map(fn (string $teamName) => $teams[$teamName]->id)
                    ->all()
            );

            foreach ($modules as $slug => $module) {
                UserModule::query()->updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'module_id' => $module->id,
                    ],
                    [
                        'can_create' => $slug === 'technician-management',
                        'can_read' => true,
                        'can_update' => true,
                        'can_delete' => false,
                    ]
                );
            }
        }
    }
}
