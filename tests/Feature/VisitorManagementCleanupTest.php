<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class VisitorManagementCleanupTest extends TestCase
{
    use RefreshDatabase;

    public function test_removed_visitor_routes_are_no_longer_registered(): void
    {
        $this->assertFalse(Route::has('visitor-management.vehicles'));
        $this->assertFalse(Route::has('visitor-management.reports'));
    }

    public function test_visitor_workspace_no_longer_shows_vehicles_or_reports_tabs(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::query()->where('username', 'admin')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('visitor-management.index'))
            ->assertOk()
            ->assertSee('Visitor Registration')
            ->assertSee('Blacklist')
            ->assertDontSee('Vehicle Visitor')
            ->assertDontSee('Visitor Reports & Analytics');
    }
}
