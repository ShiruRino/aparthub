<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingFinanceController;
use App\Http\Controllers\CommunityManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityManagementController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\PackageCenterController;
use App\Http\Controllers\ResidentManagementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SecurityManagementController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\TechnicianManagementController;
use App\Http\Controllers\TenantMarketplaceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitorManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/dashboard', DashboardController::class)->name('dashboard');

    Route::prefix('resident-management')
        ->name('resident-management.')
        ->group(function () {
            Route::get('/residents', [ResidentManagementController::class, 'residents'])
                ->middleware('module.access:resident-management,read')
                ->name('residents');
            Route::post('/residents', [ResidentManagementController::class, 'storeResident'])
                ->middleware('module.access:resident-management,create')
                ->name('residents.store');
            Route::put('/residents/{resident}', [ResidentManagementController::class, 'updateResident'])
                ->middleware('module.access:resident-management,update')
                ->name('residents.update');
            Route::delete('/residents/{resident}', [ResidentManagementController::class, 'destroyResident'])
                ->middleware('module.access:resident-management,delete')
                ->name('residents.destroy');
            Route::patch('/settings/resident-capacity', [ResidentManagementController::class, 'updateResidentCapacity'])
                ->middleware('module.access:resident-management,update')
                ->name('settings.resident-capacity');

            Route::get('/units', [ResidentManagementController::class, 'units'])
                ->middleware('module.access:resident-management,read')
                ->name('units');
            Route::post('/units', [ResidentManagementController::class, 'storeUnit'])
                ->middleware('module.access:resident-management,create')
                ->name('units.store');
            Route::put('/units/{unit}', [ResidentManagementController::class, 'updateUnit'])
                ->middleware('module.access:resident-management,update')
                ->name('units.update');
            Route::delete('/units/{unit}', [ResidentManagementController::class, 'destroyUnit'])
                ->middleware('module.access:resident-management,delete')
                ->name('units.destroy');

            Route::get('/move-in-out', [ResidentManagementController::class, 'moveInOut'])
                ->middleware('module.access:resident-management,read')
                ->name('move-in-out');
            Route::post('/move-in-out', [ResidentManagementController::class, 'storeMoveRequest'])
                ->middleware('module.access:resident-management,create')
                ->name('move-in-out.store');
            Route::put('/move-in-out/{moveRequest}', [ResidentManagementController::class, 'updateMoveRequest'])
                ->middleware('module.access:resident-management,update')
                ->name('move-in-out.update');
            Route::delete('/move-in-out/{moveRequest}', [ResidentManagementController::class, 'destroyMoveRequest'])
                ->middleware('module.access:resident-management,delete')
                ->name('move-in-out.destroy');

            Route::get('/family-members', [ResidentManagementController::class, 'familyMembers'])
                ->middleware('module.access:resident-management,read')
                ->name('family-members');
            Route::post('/family-members', [ResidentManagementController::class, 'storeFamilyMember'])
                ->middleware('module.access:resident-management,create')
                ->name('family-members.store');
            Route::put('/family-members/{familyMember}', [ResidentManagementController::class, 'updateFamilyMember'])
                ->middleware('module.access:resident-management,update')
                ->name('family-members.update');
            Route::delete('/family-members/{familyMember}', [ResidentManagementController::class, 'destroyFamilyMember'])
                ->middleware('module.access:resident-management,delete')
                ->name('family-members.destroy');

            Route::get('/vehicles', [ResidentManagementController::class, 'vehicles'])
                ->middleware('module.access:resident-management,read')
                ->name('vehicles');
            Route::post('/vehicles', [ResidentManagementController::class, 'storeVehicle'])
                ->middleware('module.access:resident-management,create')
                ->name('vehicles.store');
            Route::put('/vehicles/{vehicle}', [ResidentManagementController::class, 'updateVehicle'])
                ->middleware('module.access:resident-management,update')
                ->name('vehicles.update');
            Route::delete('/vehicles/{vehicle}', [ResidentManagementController::class, 'destroyVehicle'])
                ->middleware('module.access:resident-management,delete')
                ->name('vehicles.destroy');
        });

    Route::prefix('billing-finance')
        ->name('billing-finance.')
        ->middleware('module.access:billing-finance,read')
        ->group(function () {
            Route::redirect('/', '/billing-finance/invoices')->name('index');
            Route::get('/invoices', [BillingFinanceController::class, 'invoices'])->name('invoices');
            Route::get('/debt-collection', [BillingFinanceController::class, 'debtCollection'])->name('debt-collection');
            Route::get('/auto-bills', [BillingFinanceController::class, 'autoBills'])->name('auto-bills');
            Route::get('/history-payment', [BillingFinanceController::class, 'historyPayment'])->name('history-payment');
        });

    Route::prefix('facility-management')
        ->name('facility-management.')
        ->group(function () {
            Route::get('/', [FacilityManagementController::class, 'index'])
                ->middleware('module.access:facility-management,read')
                ->name('index');
            Route::post('/facilities', [FacilityManagementController::class, 'storeFacility'])
                ->middleware('module.access:facility-management,create')
                ->name('facilities.store');
            Route::put('/facilities/{facility}', [FacilityManagementController::class, 'updateFacility'])
                ->middleware('module.access:facility-management,update')
                ->name('facilities.update');
            Route::delete('/facilities/{facility}', [FacilityManagementController::class, 'destroyFacility'])
                ->middleware('module.access:facility-management,delete')
                ->name('facilities.destroy');
            Route::post('/bookings', [FacilityManagementController::class, 'storeBooking'])
                ->middleware('module.access:facility-management,create')
                ->name('bookings.store');
            Route::put('/bookings/{booking}', [FacilityManagementController::class, 'updateBooking'])
                ->middleware('module.access:facility-management,update')
                ->name('bookings.update');
            Route::delete('/bookings/{booking}', [FacilityManagementController::class, 'destroyBooking'])
                ->middleware('module.access:facility-management,delete')
                ->name('bookings.destroy');
        });

    Route::prefix('visitor-management')
        ->name('visitor-management.')
        ->group(function () {
            Route::get('/', [VisitorManagementController::class, 'index'])
                ->middleware('module.access:visitor-management,read')
                ->name('index');
            Route::get('/registration', [VisitorManagementController::class, 'registration'])
                ->middleware('module.access:visitor-management,read')
                ->name('registration');
            Route::get('/pending-approval', [VisitorManagementController::class, 'pendingApproval'])
                ->middleware('module.access:visitor-management,read')
                ->name('pending-approval');
            Route::get('/expected-visitors', [VisitorManagementController::class, 'expectedVisitors'])
                ->middleware('module.access:visitor-management,read')
                ->name('expected-visitors');
            Route::get('/check-in-out', [VisitorManagementController::class, 'checkInOut'])
                ->middleware('module.access:visitor-management,read')
                ->name('check-in-out');
            Route::get('/history', [VisitorManagementController::class, 'history'])
                ->middleware('module.access:visitor-management,read')
                ->name('history');
            Route::get('/blacklist', [VisitorManagementController::class, 'blacklist'])
                ->middleware('module.access:visitor-management,read')
                ->name('blacklist');
            Route::post('/walk-in', [VisitorManagementController::class, 'storeWalkIn'])
                ->middleware('module.access:visitor-management,create')
                ->name('walk-in.store');
            Route::post('/lookup-code', [VisitorManagementController::class, 'lookupByCode'])
                ->middleware('module.access:visitor-management,update')
                ->name('lookup');
            Route::post('/visitors/{visitor}/approve', [VisitorManagementController::class, 'approve'])
                ->middleware('module.access:visitor-management,update')
                ->name('approve');
            Route::post('/visitors/{visitor}/reject', [VisitorManagementController::class, 'reject'])
                ->middleware('module.access:visitor-management,update')
                ->name('reject');
            Route::post('/visitors/{visitor}/check-in', [VisitorManagementController::class, 'checkIn'])
                ->middleware('module.access:visitor-management,update')
                ->name('check-in');
            Route::post('/visitors/{visitor}/check-out', [VisitorManagementController::class, 'checkOut'])
                ->middleware('module.access:visitor-management,update')
                ->name('check-out');
            Route::get('/visitors/{visitor}/identity-photo', [VisitorManagementController::class, 'identityPhoto'])
                ->middleware('module.access:visitor-management,read')
                ->name('identity-photo');
        });

    Route::prefix('service-request')
        ->name('service-request.')
        ->middleware('module.access:service-request,read')
        ->group(function () {
            Route::get('/', [ServiceRequestController::class, 'index'])->name('index');
            Route::get('/ticket-queue', [ServiceRequestController::class, 'ticketQueue'])->name('ticket-queue');
            Route::get('/new-request', [ServiceRequestController::class, 'newRequest'])->name('new-request');
            Route::get('/work-orders', [ServiceRequestController::class, 'workOrders'])->name('work-orders');
            Route::get('/technician-schedule', [ServiceRequestController::class, 'technicianSchedule'])->name('technician-schedule');
            Route::get('/work-in-progress', [ServiceRequestController::class, 'workInProgress'])->name('work-in-progress');
            Route::get('/completed-requests', [ServiceRequestController::class, 'completedRequests'])->name('completed-requests');
            Route::post('/', [ServiceRequestController::class, 'store'])
                ->middleware('module.access:service-request,create')
                ->name('store');
            Route::put('/{serviceRequest}', [ServiceRequestController::class, 'update'])
                ->middleware('module.access:service-request,update')
                ->name('update');
            Route::post('/settings/categories', [ServiceRequestController::class, 'storeCategory'])
                ->middleware('module.access:service-request,create')
                ->name('settings.categories.store');
            Route::put('/settings/categories/{category}', [ServiceRequestController::class, 'updateCategory'])
                ->middleware('module.access:service-request,update')
                ->name('settings.categories.update');
            Route::delete('/settings/categories/{category}', [ServiceRequestController::class, 'destroyCategory'])
                ->middleware('module.access:service-request,delete')
                ->name('settings.categories.destroy');
            Route::post('/settings/subcategories', [ServiceRequestController::class, 'storeSubcategory'])
                ->middleware('module.access:service-request,create')
                ->name('settings.subcategories.store');
            Route::put('/settings/subcategories/{subcategory}', [ServiceRequestController::class, 'updateSubcategory'])
                ->middleware('module.access:service-request,update')
                ->name('settings.subcategories.update');
            Route::delete('/settings/subcategories/{subcategory}', [ServiceRequestController::class, 'destroySubcategory'])
                ->middleware('module.access:service-request,delete')
                ->name('settings.subcategories.destroy');
            Route::get('/service-history', [ServiceRequestController::class, 'serviceHistory'])->name('service-history');
            Route::get('/settings', [ServiceRequestController::class, 'settings'])->name('settings');
        });

    Route::prefix('technician-management')
        ->name('technician-management.')
        ->middleware('module.access:technician-management,read')
        ->group(function () {
            Route::get('/', [TechnicianManagementController::class, 'index'])->name('index');
            Route::post('/technicians', [TechnicianManagementController::class, 'storeTechnician'])
                ->middleware('module.access:technician-management,create')
                ->name('technicians.store');
            Route::put('/technicians/{technician}', [TechnicianManagementController::class, 'updateTechnician'])
                ->middleware('module.access:technician-management,update')
                ->name('technicians.update');
            Route::post('/teams', [TechnicianManagementController::class, 'storeTeam'])
                ->middleware('module.access:technician-management,create')
                ->name('teams.store');
            Route::put('/teams/{team}', [TechnicianManagementController::class, 'updateTeam'])
                ->middleware('module.access:technician-management,update')
                ->name('teams.update');
        });

    Route::prefix('security-management')
        ->name('security-management.')
        ->middleware('module.access:security-management,read')
        ->group(function () {
            Route::get('/', [SecurityManagementController::class, 'index'])->name('index');
            Route::get('/live-monitoring', [SecurityManagementController::class, 'liveMonitoring'])->name('live-monitoring');
            Route::get('/patrol-monitoring', [SecurityManagementController::class, 'patrolMonitoring'])->name('patrol-monitoring');
            Route::get('/task-assignment', [SecurityManagementController::class, 'taskAssignment'])->name('task-assignment');
            Route::get('/officers', [SecurityManagementController::class, 'officers'])->name('officers');
            Route::get('/schedule', [SecurityManagementController::class, 'schedule'])->name('schedule');
            Route::get('/incidents', [SecurityManagementController::class, 'incidents'])->name('incidents');
            Route::get('/devices', [SecurityManagementController::class, 'devices'])->name('devices');
            Route::get('/reports', [SecurityManagementController::class, 'reports'])->name('reports');
            Route::get('/settings', [SecurityManagementController::class, 'settings'])->name('settings');
        });

    Route::prefix('community-management')
        ->name('community-management.')
        ->middleware('module.access:community-management,read')
        ->group(function () {
            Route::redirect('/', '/community-management/announcements')->name('index');
            Route::get('/announcements', [CommunityManagementController::class, 'announcements'])->name('announcements');
            Route::post('/announcements', [CommunityManagementController::class, 'storeAnnouncement'])
                ->middleware('module.access:community-management,create')
                ->name('announcements.store');
            Route::put('/announcements/{announcement}', [CommunityManagementController::class, 'updateAnnouncement'])
                ->middleware('module.access:community-management,update')
                ->name('announcements.update');
            Route::delete('/announcements/{announcement}', [CommunityManagementController::class, 'destroyAnnouncement'])
                ->middleware('module.access:community-management,delete')
                ->name('announcements.destroy');
            Route::patch('/announcements/{announcement}/publish', [CommunityManagementController::class, 'toggleAnnouncementPublish'])
                ->middleware('module.access:community-management,update')
                ->name('announcements.publish');
            Route::patch('/announcements/{announcement}/pin', [CommunityManagementController::class, 'toggleAnnouncementPin'])
                ->middleware('module.access:community-management,update')
                ->name('announcements.pin');
            Route::get('/events', [CommunityManagementController::class, 'events'])->name('events');
            Route::get('/polling-survey', [CommunityManagementController::class, 'pollingSurvey'])->name('polling-survey');
            Route::get('/forum', [CommunityManagementController::class, 'forum'])->name('forum');
            Route::get('/broadcasts', [CommunityManagementController::class, 'broadcasts'])->name('broadcasts');
            Route::get('/programs', [CommunityManagementController::class, 'programs'])->name('programs');
            Route::get('/calendar', [CommunityManagementController::class, 'calendar'])->name('calendar');
            Route::get('/engagement', [CommunityManagementController::class, 'engagement'])->name('engagement');
            Route::get('/archive', [CommunityManagementController::class, 'archive'])->name('archive');
            Route::get('/settings', [CommunityManagementController::class, 'settings'])->name('settings');
        });

    Route::prefix('tenant-marketplace')
        ->name('tenant-marketplace.')
        ->middleware('module.access:tenant-marketplace,read')
        ->group(function () {
            Route::get('/', [TenantMarketplaceController::class, 'index'])->name('index');
            Route::get('/directory', [TenantMarketplaceController::class, 'directory'])->name('directory');
            Route::get('/add-input', [TenantMarketplaceController::class, 'addInput'])->name('add-input');
        });

    Route::get('/package-center', PackageCenterController::class)
        ->middleware('module.access:package-center,read')
        ->name('package-center.index');

    Route::get('/users', [UserController::class, 'index'])
        ->middleware('module.access:users,read')
        ->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('module.access:users,create')
        ->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('module.access:users,create')
        ->name('users.store');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])
        ->middleware('module.access:users,update')
        ->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])
        ->middleware('module.access:users,update')
        ->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])
        ->middleware('module.access:users,delete')
        ->name('users.destroy');

    Route::get('/roles', [RoleController::class, 'index'])
        ->middleware('module.access:roles,read')
        ->name('roles.index');
    Route::get('/roles/create', [RoleController::class, 'create'])
        ->middleware('module.access:roles,create')
        ->name('roles.create');
    Route::post('/roles', [RoleController::class, 'store'])
        ->middleware('module.access:roles,create')
        ->name('roles.store');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])
        ->middleware('module.access:roles,update')
        ->name('roles.edit');
    Route::put('/roles/{role}', [RoleController::class, 'update'])
        ->middleware('module.access:roles,update')
        ->name('roles.update');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])
        ->middleware('module.access:roles,delete')
        ->name('roles.destroy');

    Route::get('/modules', [ModuleController::class, 'index'])
        ->middleware('module.access:modules,read')
        ->name('modules.index');
    Route::get('/modules/create', [ModuleController::class, 'create'])
        ->middleware('module.access:modules,create')
        ->name('modules.create');
    Route::post('/modules', [ModuleController::class, 'store'])
        ->middleware('module.access:modules,create')
        ->name('modules.store');
    Route::get('/modules/{module}/edit', [ModuleController::class, 'edit'])
        ->middleware('module.access:modules,update')
        ->name('modules.edit');
    Route::put('/modules/{module}', [ModuleController::class, 'update'])
        ->middleware('module.access:modules,update')
        ->name('modules.update');
    Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])
        ->middleware('module.access:modules,delete')
        ->name('modules.destroy');

    Route::get('/access', [AccessController::class, 'index'])
        ->middleware('module.access:users,read')
        ->name('access.index');
    Route::get('/users/{user}/access', [AccessController::class, 'show'])
        ->middleware('module.access:access,update')
        ->name('users.access.show');
    Route::put('/users/{user}/access', [AccessController::class, 'update'])
        ->middleware('module.access:access,update')
        ->name('users.access.update');
});
