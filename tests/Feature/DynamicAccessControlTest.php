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
            ['name' => 'Security Management', 'slug' => 'security-management', 'sort_order' => 40],
            ['name' => 'Community Management', 'slug' => 'community-management', 'sort_order' => 50],
            ['name' => 'Tenant Marketplace', 'slug' => 'tenant-marketplace', 'sort_order' => 60],
            ['name' => 'Package Center', 'slug' => 'package-center', 'sort_order' => 70],
            ['name' => 'Billing & Finance', 'slug' => 'billing-finance', 'sort_order' => 80],
            ['name' => 'Users', 'slug' => 'users', 'sort_order' => 90],
            ['name' => 'Modules', 'slug' => 'modules', 'sort_order' => 100],
            ['name' => 'Access', 'slug' => 'access', 'sort_order' => 110],
            ['name' => 'Roles', 'slug' => 'roles', 'sort_order' => 120],
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
        $this->get(route('billing-finance.index'))->assertRedirect(route('login'));
        $this->get(route('billing-finance.invoices'))->assertRedirect(route('login'));
        $this->get(route('visitor-management.index'))->assertRedirect(route('login'));
        $this->get(route('visitor-management.registration'))->assertRedirect(route('login'));
        $this->get(route('service-request.index'))->assertRedirect(route('login'));
        $this->get(route('service-request.ticket-queue'))->assertRedirect(route('login'));
        $this->get(route('security-management.index'))->assertRedirect(route('login'));
        $this->get(route('security-management.task-assignment'))->assertRedirect(route('login'));
        $this->get(route('community-management.index'))->assertRedirect(route('login'));
        $this->get(route('community-management.announcements'))->assertRedirect(route('login'));
        $this->get(route('tenant-marketplace.index'))->assertRedirect(route('login'));
        $this->get(route('tenant-marketplace.directory'))->assertRedirect(route('login'));
        $this->get(route('package-center.index'))->assertRedirect(route('login'));
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
                ->assertSee($expectedText, false)
                ->assertDontSee('Step 1');
        }
    }

    public function test_admin_can_access_billing_finance_pages_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('billing-finance.index'))
            ->assertRedirect(route('billing-finance.invoices'));

        foreach ($this->billingRoutes() as $routeName => $expectedText) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Billing &amp; Finance', false)
                ->assertSee($expectedText)
                ->assertSee('Export Excel');
        }
    }

    public function test_admin_can_access_visitor_management_pages_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('visitor-management.index'))
            ->assertOk()
            ->assertSee('Visitor Registration')
            ->assertSee('All Visitor Registration')
            ->assertSee('Pending Approval')
            ->assertSee('Blacklist');

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
            ->assertOk()
            ->assertSee('Ticket Queue')
            ->assertSee('Ticket Queue')
            ->assertSee('Ticket Queue')
            ->assertSee('SLA Monitoring');

        foreach ($this->serviceRoutes() as $routeName => $expectedText) {
            $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Service Request')
                ->assertSee($expectedText);
        }
    }

    public function test_admin_can_access_security_management_pages_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('security-management.index'))
            ->assertOk()
            ->assertSee('Security Operations Center')
            ->assertSee('Task Assignment')
            ->assertSee('Create Task')
            ->assertSee('Export');

        foreach ($this->securityRoutes() as $routeName => $expectedText) {
            $response = $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Security Management')
                ->assertSee($expectedText);

            $response->assertSee('href="'.route($routeName).'"', false);
        }
    }

    public function test_admin_can_access_community_management_pages_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('community-management.index'))
            ->assertRedirect(route('community-management.announcements'));

        foreach ($this->communityRoutes() as $routeName => $expectedText) {
            $response = $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Community Management')
                ->assertSee($expectedText);

            $response->assertSee('data-modal-open="community-action-modal"', false);
        }
    }

    public function test_admin_can_access_tenant_marketplace_pages_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('tenant-marketplace.index'))
            ->assertOk()
            ->assertSee('Tenant Directory');

        foreach ($this->tenantRoutes() as $routeName => $expectedText) {
            $response = $this->actingAs($admin)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee('Tenant Marketplace')
                ->assertSee($expectedText);

            $response->assertSee('data-modal-open="tenant-action-modal"', false);
        }
    }

    public function test_admin_can_access_package_center_without_permission_rows(): void
    {
        $admin = $this->makeUser($this->adminRole, [
            'username' => 'admin',
        ]);

        $this->actingAs($admin)
            ->get(route('package-center.index'))
            ->assertOk()
            ->assertSee('Package Center')
            ->assertSee('Incoming Packages')
            ->assertDontSee('Alur Pengelolaan Paket');
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

    public function test_billing_finance_uses_dropdown_subpage_routes(): void
    {
        $billingRouteNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn (?string $name) => str_starts_with($name ?? '', 'billing-finance.'))
            ->values()
            ->all();

        $this->assertContains('billing-finance.index', $billingRouteNames);

        foreach (array_keys($this->billingRoutes()) as $routeName) {
            $this->assertContains($routeName, $billingRouteNames);
        }
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

    public function test_community_management_uses_dropdown_subpage_routes(): void
    {
        $communityRouteNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn (?string $name) => str_starts_with($name ?? '', 'community-management.'))
            ->values()
            ->all();

        $this->assertContains('community-management.index', $communityRouteNames);

        foreach (array_keys($this->communityRoutes()) as $routeName) {
            $this->assertContains($routeName, $communityRouteNames);
        }
    }

    public function test_tenant_marketplace_uses_dropdown_subpage_routes(): void
    {
        $tenantRouteNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn (?string $name) => str_starts_with($name ?? '', 'tenant-marketplace.'))
            ->values()
            ->all();

        $this->assertContains('tenant-marketplace.index', $tenantRouteNames);

        foreach (array_keys($this->tenantRoutes()) as $routeName) {
            $this->assertContains($routeName, $tenantRouteNames);
        }
    }

    public function test_security_management_uses_workspace_routes(): void
    {
        $securityRouteNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn (?string $name) => str_starts_with($name ?? '', 'security-management.'))
            ->values()
            ->all();

        $this->assertContains('security-management.index', $securityRouteNames);

        foreach (array_keys($this->securityRoutes()) as $routeName) {
            $this->assertContains($routeName, $securityRouteNames);
        }
    }

    public function test_package_center_uses_single_real_route(): void
    {
        $packageRouteNames = collect(Route::getRoutes())
            ->map(fn ($route) => $route->getName())
            ->filter(fn (?string $name) => str_starts_with($name ?? '', 'package-center.'))
            ->values()
            ->all();

        $this->assertSame(['package-center.index'], $packageRouteNames);
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
            ->get(route('billing-finance.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('billing-finance.invoices'))
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

        $this->actingAs($user)
            ->get(route('security-management.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('security-management.task-assignment'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('community-management.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('community-management.announcements'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('tenant-marketplace.index'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('tenant-marketplace.directory'))
            ->assertForbidden();

        $this->actingAs($user)
            ->get(route('package-center.index'))
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
                ->assertSee($expectedText, false);
        }
    }

    public function test_non_admin_can_access_billing_finance_pages_with_read_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'billing-finance', [
            'can_read' => true,
        ]);

        $this->actingAs($user)
            ->get(route('billing-finance.index'))
            ->assertRedirect(route('billing-finance.invoices'));

        foreach ($this->billingRoutes() as $routeName => $expectedText) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($expectedText)
                ->assertSee('Export Excel');
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
            ->assertOk()
            ->assertSee('Visitor Registration')
            ->assertSee('All Visitor Registration');

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
            ->assertOk()
            ->assertSee('Ticket Queue');

        foreach ($this->serviceRoutes() as $routeName => $expectedText) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_non_admin_can_access_security_management_pages_with_read_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'security-management', [
            'can_read' => true,
        ]);

        $this->actingAs($user)
            ->get(route('security-management.index'))
            ->assertOk()
            ->assertSee('Task Assignment');

        foreach ($this->securityRoutes() as $routeName => $expectedText) {
            $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($expectedText);
        }
    }

    public function test_non_admin_can_access_community_management_pages_with_read_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'community-management', [
            'can_read' => true,
        ]);

        $this->actingAs($user)
            ->get(route('community-management.index'))
            ->assertRedirect(route('community-management.announcements'));

        foreach ($this->communityRoutes() as $routeName => $expectedText) {
            $response = $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($expectedText);

            $response->assertSee('data-modal-open="community-action-modal"', false);
        }
    }

    public function test_non_admin_can_access_tenant_marketplace_pages_with_read_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'tenant-marketplace', [
            'can_read' => true,
        ]);

        $this->actingAs($user)
            ->get(route('tenant-marketplace.index'))
            ->assertOk()
            ->assertSee('Tenant Directory');

        foreach ($this->tenantRoutes() as $routeName => $expectedText) {
            $response = $this->actingAs($user)
                ->get(route($routeName))
                ->assertOk()
                ->assertSee($expectedText);

            $response->assertSee('data-modal-open="tenant-action-modal"', false);
        }
    }

    public function test_non_admin_can_access_package_center_with_read_permission(): void
    {
        $user = $this->makeUser($this->staffRole);

        $this->grant($user, 'package-center', [
            'can_read' => true,
        ]);

        $this->actingAs($user)
            ->get(route('package-center.index'))
            ->assertOk()
            ->assertSee('Package Center')
            ->assertSee('Incoming Packages');
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

    public function test_billing_finance_sidebar_dropdown_uses_real_routes(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('billing-finance.invoices'))
            ->assertOk()
            ->assertSeeText('Billing & Finance')
            ->assertSee('Invoice Management')
            ->assertSee('Debt Collection')
            ->assertSee('Auto Bills')
            ->assertSee('History Payment')
            ->assertDontSee('<a href="#">Invoice Management</a>', false);

        foreach (array_keys($this->billingRoutes()) as $routeName) {
            $response->assertSee('href="'.route($routeName).'"', false);
        }

        $this->actingAs($admin)
            ->get(route('billing-finance.invoices'))
            ->assertOk()
            ->assertSee('Export Excel')
            ->assertSee('View Collection')
            ->assertSee('Auto Billing');
    }

    public function test_visitor_management_sidebar_dropdown_uses_real_routes(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Visitor Management')
            ->assertSee('href="'.route('visitor-management.index').'"', false)
            ->assertDontSee('href="'.route('visitor-management.registration').'"', false)
            ->assertDontSee('href="'.route('visitor-management.pending-approval').'"', false)
            ->assertDontSee('href="'.route('visitor-management.expected-visitors').'"', false)
            ->assertDontSee('href="'.route('visitor-management.check-in-out').'"', false)
            ->assertDontSee('href="'.route('visitor-management.history').'"', false)
            ->assertDontSee('href="'.route('visitor-management.vehicles').'"', false)
            ->assertDontSee('href="'.route('visitor-management.blacklist').'"', false)
            ->assertDontSee('href="'.route('visitor-management.reports').'"', false);

        $hub = $this->actingAs($admin)
            ->get(route('visitor-management.index'))
            ->assertOk()
            ->assertSee('Visitor Registration')
            ->assertSee('Register Visitor')
            ->assertSee('Check-In / Out')
            ->assertSee('All Visitor Registration');

        foreach (array_keys($this->visitorRoutes()) as $routeName) {
            $hub->assertSee('href="'.route($routeName).'"', false);
        }
    }

    public function test_service_request_sidebar_dropdown_uses_real_routes(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Service Request')
            ->assertSee('href="'.route('service-request.index').'"', false)
            ->assertDontSee('href="'.route('service-request.ticket-queue').'"', false)
            ->assertDontSee('href="'.route('service-request.new-request').'"', false)
            ->assertDontSee('href="'.route('service-request.assignment-board').'"', false)
            ->assertDontSee('href="'.route('service-request.work-orders').'"', false)
            ->assertDontSee('href="'.route('service-request.technician-schedule').'"', false)
            ->assertDontSee('href="'.route('service-request.work-in-progress').'"', false)
            ->assertDontSee('href="'.route('service-request.completed-requests').'"', false)
            ->assertDontSee('href="'.route('service-request.sla-monitoring').'"', false)
            ->assertDontSee('href="'.route('service-request.service-history').'"', false)
            ->assertDontSee('href="'.route('service-request.settings').'"', false);

        $hub = $this->actingAs($admin)
            ->get(route('service-request.index'))
            ->assertOk()
            ->assertSee('Ticket Queue')
            ->assertSee('Create New Request')
            ->assertSee('SLA Monitoring')
            ->assertSee('Assignment Board');

        foreach (array_keys($this->serviceRoutes()) as $routeName) {
            $hub->assertSee('href="'.route($routeName).'"', false);
        }
    }

    public function test_security_management_sidebar_uses_single_real_link(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Security Management')
            ->assertSee('href="'.route('security-management.index').'"', false)
            ->assertDontSee('href="'.route('security-management.live-monitoring').'"', false)
            ->assertDontSee('href="'.route('security-management.patrol-monitoring').'"', false)
            ->assertDontSee('href="'.route('security-management.task-assignment').'"', false)
            ->assertDontSee('href="'.route('security-management.officers').'"', false)
            ->assertDontSee('href="'.route('security-management.schedule').'"', false)
            ->assertDontSee('href="'.route('security-management.incidents').'"', false)
            ->assertDontSee('href="'.route('security-management.devices').'"', false)
            ->assertDontSee('href="'.route('security-management.reports').'"', false)
            ->assertDontSee('href="'.route('security-management.settings').'"', false);

        $workspace = $this->actingAs($admin)
            ->get(route('security-management.index'))
            ->assertOk()
            ->assertSee('Security Operations Center')
            ->assertSee('Task Assignment')
            ->assertSee('Create Task')
            ->assertSee('Export')
            ->assertSee('data-modal-open="security-task-modal"', false)
            ->assertSee('data-modal-open="security-route-modal"', false)
            ->assertSee('Open Task Assignment')
            ->assertSee('Incident Log')
            ->assertSee('Patrol Schedule');

        foreach (array_keys($this->securityRoutes()) as $routeName) {
            $workspace->assertSee('href="'.route($routeName).'"', false);
        }
    }

    public function test_community_management_sidebar_dropdown_uses_real_routes(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Community Management')
            ->assertSee('Announcement Center')
            ->assertSee('Event Management')
            ->assertSee('Polling &amp; Survey', false)
            ->assertSee('Community Settings')
            ->assertDontSee('<a href="#" class="side-link" title="Community Management">', false);

        foreach (array_keys($this->communityRoutes()) as $routeName) {
            $response->assertSee('href="'.route($routeName).'"', false);
        }

        $this->actingAs($admin)
            ->get(route('community-management.announcements'))
            ->assertOk()
            ->assertSee('Create Announcement')
            ->assertSee('Create Event')
            ->assertSee('Send Broadcast')
            ->assertSee('Create Polling');
    }

    public function test_tenant_marketplace_sidebar_dropdown_uses_real_routes(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Tenant Marketplace')
            ->assertSee('href="'.route('tenant-marketplace.index').'"', false)
            ->assertDontSee('href="'.route('tenant-marketplace.directory').'"', false)
            ->assertDontSee('href="'.route('tenant-marketplace.add-input').'"', false)
            ->assertDontSee('<a href="#" class="side-link" title="Tenant Marketplace">', false);

        $this->actingAs($admin)
            ->get(route('tenant-marketplace.index'))
            ->assertOk()
            ->assertSee('Tenant Directory')
            ->assertSee('Add / Input Tenant')
            ->assertSee('Generate Report');

        $this->actingAs($admin)
            ->get(route('tenant-marketplace.directory'))
            ->assertOk()
            ->assertSee('Add / Input Tenant')
            ->assertSee('Generate Report');
    }

    public function test_package_center_sidebar_link_uses_real_route(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);

        $response = $this->actingAs($admin)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Package Center')
            ->assertDontSee('<a href="#" class="side-link" title="Package Center">', false);

        $response->assertSee('href="'.route('package-center.index').'"', false);

        $this->actingAs($admin)
            ->get(route('package-center.index'))
            ->assertOk()
            ->assertSee('Register Package')
            ->assertSee('Pickup Status')
            ->assertSee('Package Reports');
    }

    public function test_system_role_and_modules_are_protected(): void
    {
        $admin = $this->makeUser($this->adminRole, ['username' => 'admin']);
        $usersModule = $this->module('users');
        $residentModule = $this->module('resident-management');
        $visitorModule = $this->module('visitor-management');
        $serviceModule = $this->module('service-request');
        $securityModule = $this->module('security-management');
        $communityModule = $this->module('community-management');
        $tenantModule = $this->module('tenant-marketplace');
        $packageModule = $this->module('package-center');
        $billingModule = $this->module('billing-finance');

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

        $this->actingAs($admin)
            ->put(route('modules.update', $securityModule), [
                'name' => 'Security Operations',
                'slug' => 'security-ops',
                'sort_order' => 5,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $securityModule->id,
            'name' => 'Security Operations',
            'slug' => 'security-management',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)
            ->put(route('modules.update', $communityModule), [
                'name' => 'Community Operations',
                'slug' => 'community-ops',
                'sort_order' => 6,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $communityModule->id,
            'name' => 'Community Operations',
            'slug' => 'community-management',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)
            ->put(route('modules.update', $tenantModule), [
                'name' => 'Tenant Operations',
                'slug' => 'tenant-ops',
                'sort_order' => 7,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $tenantModule->id,
            'name' => 'Tenant Operations',
            'slug' => 'tenant-marketplace',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)
            ->put(route('modules.update', $packageModule), [
                'name' => 'Package Operations',
                'slug' => 'package-ops',
                'sort_order' => 8,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $packageModule->id,
            'name' => 'Package Operations',
            'slug' => 'package-center',
            'is_active' => 1,
        ]);

        $this->actingAs($admin)
            ->put(route('modules.update', $billingModule), [
                'name' => 'Billing Operations',
                'slug' => 'billing-ops',
                'sort_order' => 9,
                'is_active' => '0',
            ])
            ->assertRedirect(route('modules.index'));

        $this->assertDatabaseHas('modules', [
            'id' => $billingModule->id,
            'name' => 'Billing Operations',
            'slug' => 'billing-finance',
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
            'resident-management.residents' => 'Daftar Residen Aether Residences',
            'resident-management.units' => 'Daftar Unit & Inventaris Aether Residences',
            'resident-management.move-in-out' => 'Manajemen Pindah Masuk & Keluar Aether Residences',
            'resident-management.family-members' => 'Daftar Anggota Keluarga Aether Residences',
            'resident-management.vehicles' => 'Daftar Kendaraan Penghuni Aether Residences',
        ];
    }

    /**
     * Get billing finance route names and their page-specific text.
     *
     * @return array<string, string>
     */
    private function billingRoutes(): array
    {
        return [
            'billing-finance.invoices' => 'Invoice Management',
            'billing-finance.debt-collection' => 'Bills Collecting',
            'billing-finance.auto-bills' => 'Auto Billing Control Center',
            'billing-finance.history-payment' => 'History Payment',
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

    /**
     * Get security management route names and their page-specific text.
     *
     * @return array<string, string>
     */
    private function securityRoutes(): array
    {
        return [
            'security-management.live-monitoring' => 'Live Monitoring',
            'security-management.patrol-monitoring' => 'Patrol Monitoring',
            'security-management.task-assignment' => 'Task Assignment',
            'security-management.officers' => 'Security Officer',
            'security-management.schedule' => 'Security Schedule',
            'security-management.incidents' => 'Incident Management',
            'security-management.devices' => 'Device Management',
            'security-management.reports' => 'Reports & Analytics',
            'security-management.settings' => 'Security Settings',
        ];
    }

    /**
     * Get community management route names and their page-specific text.
     *
     * @return array<string, string>
     */
    private function communityRoutes(): array
    {
        return [
            'community-management.announcements' => 'Community Management',
            'community-management.events' => 'Event Management',
            'community-management.polling-survey' => 'Polling & Survey Management',
            'community-management.forum' => 'Resident Forum Management',
            'community-management.broadcasts' => 'Broadcast Notification Management',
            'community-management.programs' => 'Community Program Management',
            'community-management.calendar' => 'Community Event Calendar',
            'community-management.engagement' => 'Dasbor Keterlibatan Penduduk',
            'community-management.archive' => 'Arsip Komunitas',
            'community-management.settings' => 'Pengaturan Komunitas',
        ];
    }

    /**
     * Get tenant marketplace route names and their page-specific text.
     *
     * @return array<string, string>
     */
    private function tenantRoutes(): array
    {
        return [
            'tenant-marketplace.directory' => 'Tenant Directory',
            'tenant-marketplace.add-input' => 'Add New Tenant',
        ];
    }
}
