<?php

namespace Database\Seeders;

use App\Models\AppSetting;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\UserModule;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminRole = Role::query()->updateOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin']
        );

        $modules = collect([
            ['name' => 'Resident Management', 'slug' => 'resident-management', 'description' => 'Kelola halaman operasional penghuni.', 'sort_order' => 10],
            ['name' => 'Visitor Management', 'slug' => 'visitor-management', 'description' => 'Kelola halaman operasional visitor.', 'sort_order' => 20],
            ['name' => 'Service Request', 'slug' => 'service-request', 'description' => 'Kelola halaman operasional service request.', 'sort_order' => 30],
            ['name' => 'Security Management', 'slug' => 'security-management', 'description' => 'Kelola task assignment, patroli, insiden, dan monitoring keamanan.', 'sort_order' => 40],
            ['name' => 'Community Management', 'slug' => 'community-management', 'description' => 'Kelola halaman operasional komunitas penghuni.', 'sort_order' => 50],
            ['name' => 'Tenant Marketplace', 'slug' => 'tenant-marketplace', 'description' => 'Kelola direktori tenant marketplace dan input tenant.', 'sort_order' => 60],
            ['name' => 'Package Center', 'slug' => 'package-center', 'description' => 'Kelola paket masuk, pengambilan, dan riwayat paket.', 'sort_order' => 70],
            ['name' => 'Billing & Finance', 'slug' => 'billing-finance', 'description' => 'Kelola invoice, collection, auto billing, dan histori pembayaran.', 'sort_order' => 80],
            ['name' => 'Facility Management', 'slug' => 'facility-management', 'description' => 'Kelola fasilitas dan booking fasilitas penghuni.', 'sort_order' => 90],
            ['name' => 'Users', 'slug' => 'users', 'description' => 'Kelola data user.', 'sort_order' => 100],
            ['name' => 'Modules', 'slug' => 'modules', 'description' => 'Kelola daftar module aplikasi.', 'sort_order' => 110],
            ['name' => 'Access', 'slug' => 'access', 'description' => 'Kelola hak akses CRUD per user dan module.', 'sort_order' => 120],
            ['name' => 'Roles', 'slug' => 'roles', 'description' => 'Kelola role user.', 'sort_order' => 130],
        ])->map(fn (array $module) => Module::query()->updateOrCreate(
            ['slug' => $module['slug']],
            $module + ['is_active' => true]
        ));

        $admin = User::query()->updateOrCreate([
            'username' => 'admin',
        ], [
            'role_id' => $adminRole->id,
            'name' => 'Administrator',
            'password' => 'password',
        ]);

        $modules->each(fn (Module $module) => UserModule::query()->updateOrCreate([
            'user_id' => $admin->id,
            'module_id' => $module->id,
        ], [
            'can_create' => true,
            'can_read' => true,
            'can_update' => true,
            'can_delete' => true,
        ]));

        AppSetting::putInteger('visitor_guest_max', AppSetting::getInteger('visitor_guest_max') ?? 10);

        $this->call([
            ResidentManagementSeeder::class,
            ServiceRequestCatalogSeeder::class,
            ServiceRequestSeeder::class,
            AnnouncementSeeder::class,
            FacilityManagementSeeder::class,
        ]);
    }
}
