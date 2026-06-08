<?php

namespace Tests\Feature;

use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\UserModule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class DynamicAccessControlTest extends TestCase
{
    use RefreshDatabase;

    private Role $adminRole;

    private Role $staffRole;

    /**
     * @var Collection<int, Module>
     */
    private Collection $modules;

    protected function setUp(): void
    {
        parent::setUp();

        $this->adminRole = Role::query()->create([
            'name' => 'Admin',
            'slug' => 'admin',
        ]);

        $this->staffRole = Role::query()->create([
            'name' => 'Staff',
            'slug' => 'staff',
        ]);

        $this->modules = collect([
            ['name' => 'Resident Management', 'slug' => 'resident-management', 'sort_order' => 10],
            ['name' => 'Visitor Management', 'slug' => 'visitor-management', 'sort_order' => 20],
            ['name' => 'Service Request', 'slug' => 'service-request', 'sort_order' => 30],
            ['name' => 'Users', 'slug' => 'users', 'sort_order' => 40],
            ['name' => 'Modules', 'slug' => 'modules', 'sort_order' => 50],
            ['name' => 'Access', 'slug' => 'access', 'sort_order' => 60],
            ['name' => 'Roles', 'slug' => 'roles', 'sort_order' => 70],
        ])->map(fn (array $module) => Module::query()->create($module + ['is_active' => true]));
    }

    public function test_login_uses_username(): void
    {
        $user = $this->makeUser($this->adminRole, [
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $response = $this->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_login_rejects_invalid_username_password_pair(): void
    {
        $this->makeUser($this->adminRole, [
            'username' => 'admin',
            'password' => 'secret123',
        ]);

        $response = $this->post(route('login.store'), [
            'username' => 'admin',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('users.index'))->assertRedirect(route('login'));
        $this->get(route('resident-management.residents'))->assertRedirect(route('login'));
        $this->get(route('visitor-management.index'))->assertRedirect(route('login'));
        $this->get(route('visitor-management.registration'))->assertRedirect(route('login'));
        $this->get(route('service-request.index'))->assertRedirect(route('login'));
        $this->get(route('service-request.ticket-queue'))->assertRedirect(route('login'));
    }

    public function test_admin_role_can_access_modules_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Management Operations Center')
            ->assertDontSee('Access');

        $this->actingAs($admin)
            ->get(route('users.index'))
            ->assertOk()
            ->assertSee('Akses');
    }

    public function test_admin_can_access_resident_management_pages_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        foreach ($this->residentRoutes() as $routeName => $expectedText) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Resident Management Flow')
                ->assertSee($expectedText);
        }
    }

    public function test_admin_can_access_visitor_management_pages_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('visitor-management.index'))
            ->assertRedirect(route('visitor-management.registration'));

        foreach ($this->visitorRoutes() as $routeName => $expectedText) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Visitor Management')
                ->assertSee($expectedText);
        }
    }

    public function test_admin_can_access_service_request_pages_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('service-request.index'))
            ->assertRedirect(route('service-request.ticket-queue'));

        foreach ($this->serviceRoutes() as $routeName => $expectedText) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Service Request')
                ->assertSee($expectedText);
        }
    }

    public function test_visitor_management_uses_subpage_routes_without_separate_check_in_or_check_out(): void
    {
        $visitorRouteNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn (?string $name) => str_starts_with($name ?? '', 'visitor-management.'))
            ->values()
            ->all();

        $this->assertContains('visitor-management.index', $visitorRouteNames);

        foreach (array_keys($this->visitorRoutes()) as $routeName) {
            $this->assertContains($routeName, $visitorRouteNames);
        }

        $this->assertNotContains('visitor-management.check-in', $visitorRouteNames);
        $this->assertNotContains('visitor-management.check-out', $visitorRouteNames);
    }

    public function test_service_request_uses_dropdown_subpage_routes(): void
    {
        $serviceRouteNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn (?string $name) => str_starts_with($name ?? '', 'service-request.'))
            ->values()
            ->all();

        $this->assertContains('service-request.index', $serviceRouteNames);

        foreach (array_keys($this->serviceRoutes()) as $routeName) {
            $this->assertContains($routeName, $serviceRouteNames);
        }
    }

    public function test_non_admin_is_forbidden_without_user_module_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('resident-management.residents'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('visitor-management.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('visitor-management.registration'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('service-request.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('service-request.ticket-queue'))
            ->assertForbidden();
    }

    public function test_non_admin_can_access_resident_management_pages_with_read_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'resident-management', [
            'can_read' => true,
        ]);

        foreach ($this->residentRoutes() as $routeName => $expectedText) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_non_admin_can_access_visitor_management_pages_with_read_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'visitor-management', [
            'can_read' => true,
        ]);

        $this->actingAs($user)
            ->get(route('visitor-management.index'))
            ->assertRedirect(route('visitor-management.registration'));

        foreach ($this->visitorRoutes() as $routeName => $expectedText) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_non_admin_can_access_service_request_pages_with_read_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'service-request', [
            'can_read' => true,
        ]);

        $this->actingAs($user)
            ->get(route('service-request.index'))
            ->assertRedirect(route('service-request.ticket-queue'));

        foreach ($this->serviceRoutes() as $routeName => $expectedText) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_non_admin_can_only_access_checked_crud_actions(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'users', [
            'can_read' => true,
        ]);

        $this->actingAs($user)
            ->get(route('users.index'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('users.create'))
            ->assertForbidden();

        $this->actingAs($user)
            ->post(route('users.store'), [
                'name' => 'New User',
                'username' => 'newuser',
                'role_id' => $this->staffRole->id,
                'password' => 'password',
            ])
            ->assertForbidden();
    }

    public function test_per_user_access_page_saves_permissions_for_non_admin_user(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);
        $staff = $this->makeUser($this->staffRole, ['username' => 'staff']);
        $usersModule = $this->module('users');

        $this->actingAs($admin)
            ->get(route('users.access.show', $staff))
            ->assertOk()
            ->assertSee('Hak Akses')
            ->assertSee('staff');

        $response = $this->actingAs($admin)
            ->put(route('users.access.update', $staff), [
                'permissions' => [
                    $usersModule->id => [
                        'can_read' => '1',
                        'can_update' => '1',
                    ],
                ],
            ]);

        $response->assertRedirect(route('users.access.show', $staff));
        $this->assertDatabaseHas('user_modules', [
            'user_id' => $staff->id,
            'module_id' => $usersModule->id,
            'can_create' => 0,
            'can_read' => 1,
            'can_update' => 1,
            'can_delete' => 0,
        ]);
    }

    public function test_admin_access_page_is_read_only_and_cannot_be_updated(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $this->actingAs($admin)
            ->get(route('users.access.show', $admin))
            ->assertOk()
            ->assertSee('Full Access');

        $response = $this->actingAs($admin)
            ->put(route('users.access.update', $admin), [
                'permissions' => [
                    $this->module('users')->id => [
                        'can_delete' => '1',
                    ],
                ],
            ]);

        $response->assertSessionHasErrors('access');
        $this->assertDatabaseMissing('user_modules', [
            'user_id' => $admin->id,
        ]);
    }

    public function test_resident_management_sidebar_links_use_real_routes(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Resident Management')
            ->assertDontSee('<a href="#">Residents</a>', false)
            ->assertDontSee('<a href="#">Unit Management</a>', false)
            ->assertDontSee('<a href="#">Move In / Out</a>', false)
            ->assertDontSee('<a href="#">Family Member</a>', false)
            ->assertDontSee('<a href="#">Vehicle Management</a>', false)
            ->assertDontSee('>Access</a>', false);

        foreach ($this->residentRoutes() as $routeName => $expectedText) {
            $response->assertSee('href="'.route($routeName).'"', false);
        }
    }

    public function test_visitor_management_sidebar_dropdown_uses_real_routes(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Visitor Management')
            ->assertSee('Check-In / Check-Out')
            ->assertDontSee('<a href="#" class="side-link" title="Visitor Management">', false);

        foreach (array_keys($this->visitorRoutes()) as $routeName) {
            $response->assertSee('href="'.route($routeName).'"', false);
        }
    }

    public function test_service_request_sidebar_dropdown_uses_real_routes(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Service Request')
            ->assertSee('Ticket Queue')
            ->assertSee('Create New Request')
            ->assertSee('Assign Technician')
            ->assertSee('Generate Report')
            ->assertDontSee('<a href="#" class="side-link" title="Service Request">', false);

        foreach (array_keys($this->serviceRoutes()) as $routeName) {
            $response->assertSee('href="'.route($routeName).'"', false);
        }
    }

    public function test_system_role_and_modules_are_protected(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);
        $usersModule = $this->module('users');
        $residentModule = $this->module('resident-management');
        $visitorModule = $this->module('visitor-management');
        $serviceModule = $this->module('service-request');

        $this->actingAs($admin)
            ->put(route('roles.update', $this->adminRole), [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
            ])
            ->assertRedirect(route('roles.index'));

        $this->assertDatabaseHas('roles', [
            'id' => $this->adminRole->id,
            'name' => 'Super Admin',
            'slug' => 'admin',
        ]);

        $this->actingAs($admin)
            ->delete(route('roles.destroy', $this->adminRole))
            ->assertSessionHasErrors('role');

        $this->assertDatabaseHas('roles', [
            'id' => $this->adminRole->id,
            'slug' => 'admin',
        ]);

        $this->actingAs($admin)
            ->put(route('modules.update', $usersModule), [
                'name' => 'People',
                'slug' => 'people',
                'sort_order' => 1,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $usersModule->id,
            'name' => 'People',
            'slug' => 'users',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)
            ->delete(route('modules.destroy', $usersModule))
            ->assertSessionHasErrors('module');

        $this->assertDatabaseHas('modules', [
            'id' => $usersModule->id,
            'slug' => 'users',
        ]);

        $this->actingAs($admin)
            ->put(route('modules.update', $residentModule), [
                'name' => 'Resident Operations',
                'slug' => 'resident-ops',
                'sort_order' => 2,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $residentModule->id,
            'name' => 'Resident Operations',
            'slug' => 'resident-management',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)
            ->put(route('modules.update', $visitorModule), [
                'name' => 'Visitor Operations',
                'slug' => 'visitor-ops',
                'sort_order' => 3,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $visitorModule->id,
            'name' => 'Visitor Operations',
            'slug' => 'visitor-management',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)
            ->put(route('modules.update', $serviceModule), [
                'name' => 'Service Operations',
                'slug' => 'service-ops',
                'sort_order' => 4,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $serviceModule->id,
            'name' => 'Service Operations',
            'slug' => 'service-request',
            'is_active' => 1,
        ]);
    }

    /**
     * Create a user for the given role.
     *
     * @param  array<string, mixed>  $attributes
     */
    private function makeUser(Role $role, array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role_id' => $role->id,
        ], $attributes));
    }

    /**
     * Grant direct module permissions to a user.
     *
     * @param  array<string, bool>  $abilities
     */
    private function grant(User $user, string $moduleSlug, array $abilities): UserModule
    {
        return UserModule::query()->create(array_merge([
            'user_id' => $user->id,
            'module_id' => $this->module($moduleSlug)->id,
            'can_create' => false,
            'can_read' => false,
            'can_update' => false,
            'can_delete' => false,
        ], $abilities));
    }

    private function module(string $slug): Module
    {
        return $this->modules->firstWhere('slug', $slug);
    }

    /**
     * Get resident management route names and their page-specific text.
     *
     * @return array<string, string>
     */
    private function residentRoutes(): array
    {
        return [
            'resident-management.residents' => 'Resident Registration',
            'resident-management.units' => 'Unit Assignment',
            'resident-management.move-in-out' => 'Move-In Approval',
            'resident-management.family-members' => 'Family Members',
            'resident-management.vehicles' => 'Vehicle Registration',
        ];
    }

    /**
     * Get visitor management route names and their page-specific text.
     *
     * @return array<string, string>
     */
    private function visitorRoutes(): array
    {
        return [
            'visitor-management.registration' => 'Visitor Registration',
            'visitor-management.pending-approval' => 'Pending Approval',
            'visitor-management.expected-visitors' => 'Expected Visitors',
            'visitor-management.check-in-out' => 'Visitor Check-In / Check-Out',
            'visitor-management.history' => 'Visitor History Log',
            'visitor-management.vehicles' => 'Visitor Vehicle Management',
            'visitor-management.blacklist' => 'Visitor Blacklist Management',
            'visitor-management.reports' => 'Visitor Reports & Analytics',
        ];
    }

    /**
     * Get service request route names and their page-specific text.
     *
     * @return array<string, string>
     */
    private function serviceRoutes(): array
    {
        return [
            'service-request.ticket-queue' => 'Ticket Queue',
            'service-request.new-request' => 'Create New Service Request',
            'service-request.assignment-board' => 'Ticket Assignment Board',
            'service-request.work-orders' => 'Work Orders',
            'service-request.technician-schedule' => 'Technician Schedule',
            'service-request.work-in-progress' => 'Active Work In Progress',
            'service-request.completed-requests' => 'Recently Completed Requests',
            'service-request.sla-monitoring' => 'SLA Performance Dashboard',
            'service-request.service-history' => 'Service Request History Log',
            'service-request.settings' => 'Suite Settings Configuration',
        ];
    }
}
