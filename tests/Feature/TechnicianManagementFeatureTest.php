<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TechnicianManagementFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_technician_workspace_uses_compact_pagination_markup(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('technician-management.index', ['search' => 'tech']))
            ->assertOk()
            ->assertSee('resident-pagination');
    }
}
