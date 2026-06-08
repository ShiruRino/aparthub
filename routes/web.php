<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommunityManagementController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\ResidentManagementController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\ServiceRequestController;
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
        ->middleware('module.access:resident-management,read')
        ->group(function () {
            Route::get('/residents', [ResidentManagementController::class, 'residents'])->name('residents');
            Route::get('/units', [ResidentManagementController::class, 'units'])->name('units');
            Route::get('/move-in-out', [ResidentManagementController::class, 'moveInOut'])->name('move-in-out');
            Route::get('/family-members', [ResidentManagementController::class, 'familyMembers'])->name('family-members');
            Route::get('/vehicles', [ResidentManagementController::class, 'vehicles'])->name('vehicles');
        });

    Route::prefix('visitor-management')
        ->name('visitor-management.')
        ->middleware('module.access:visitor-management,read')
        ->group(function () {
            Route::redirect('/', '/visitor-management/registration')->name('index');
            Route::get('/registration', [VisitorManagementController::class, 'registration'])->name('registration');
            Route::get('/pending-approval', [VisitorManagementController::class, 'pendingApproval'])->name('pending-approval');
            Route::get('/expected-visitors', [VisitorManagementController::class, 'expectedVisitors'])->name('expected-visitors');
            Route::get('/check-in-out', [VisitorManagementController::class, 'checkInOut'])->name('check-in-out');
            Route::get('/history', [VisitorManagementController::class, 'history'])->name('history');
            Route::get('/vehicles', [VisitorManagementController::class, 'vehicles'])->name('vehicles');
            Route::get('/blacklist', [VisitorManagementController::class, 'blacklist'])->name('blacklist');
            Route::get('/reports', [VisitorManagementController::class, 'reports'])->name('reports');
        });

    Route::prefix('service-request')
        ->name('service-request.')
        ->middleware('module.access:service-request,read')
        ->group(function () {
            Route::redirect('/', '/service-request/ticket-queue')->name('index');
            Route::get('/ticket-queue', [ServiceRequestController::class, 'ticketQueue'])->name('ticket-queue');
            Route::get('/new-request', [ServiceRequestController::class, 'newRequest'])->name('new-request');
            Route::get('/assignment-board', [ServiceRequestController::class, 'assignmentBoard'])->name('assignment-board');
            Route::get('/work-orders', [ServiceRequestController::class, 'workOrders'])->name('work-orders');
            Route::get('/technician-schedule', [ServiceRequestController::class, 'technicianSchedule'])->name('technician-schedule');
            Route::get('/work-in-progress', [ServiceRequestController::class, 'workInProgress'])->name('work-in-progress');
            Route::get('/completed-requests', [ServiceRequestController::class, 'completedRequests'])->name('completed-requests');
            Route::get('/sla-monitoring', [ServiceRequestController::class, 'slaMonitoring'])->name('sla-monitoring');
            Route::get('/service-history', [ServiceRequestController::class, 'serviceHistory'])->name('service-history');
            Route::get('/settings', [ServiceRequestController::class, 'settings'])->name('settings');
        });

    Route::prefix('community-management')
        ->name('community-management.')
        ->middleware('module.access:community-management,read')
        ->group(function () {
            Route::redirect('/', '/community-management/announcements')->name('index');
            Route::get('/announcements', [CommunityManagementController::class, 'announcements'])->name('announcements');
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
            Route::redirect('/', '/tenant-marketplace/directory')->name('index');
            Route::get('/directory', [TenantMarketplaceController::class, 'directory'])->name('directory');
            Route::get('/add-input', [TenantMarketplaceController::class, 'addInput'])->name('add-input');
        });

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
