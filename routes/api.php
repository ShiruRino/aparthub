<?php

use App\Http\Controllers\Api\ResidentAnnouncementController;
use App\Http\Controllers\Api\ResidentAuthController;
use App\Http\Controllers\Api\ResidentServiceRequestController;
use App\Http\Controllers\Api\ResidentVisitorController;
use App\Http\Controllers\Api\SecurityAuthController;
use App\Http\Controllers\Api\SecurityVisitorAccessController;
use App\Http\Controllers\Api\TechnicianAuthController;
use App\Http\Controllers\Api\TechnicianServiceRequestController;
use Illuminate\Support\Facades\Route;

Route::prefix('resident')->group(function () {
    Route::post('/login', [ResidentAuthController::class, 'login'])
        ->middleware('throttle:resident-login')
        ->name('api.resident.login');

    Route::middleware(['auth:sanctum', 'resident.api'])->group(function () {
        Route::get('/me', [ResidentAuthController::class, 'me'])->name('api.resident.me');
        Route::post('/logout', [ResidentAuthController::class, 'logout'])->name('api.resident.logout');
        Route::get('/announcements', [ResidentAnnouncementController::class, 'index'])->name('api.resident.announcements.index');
        Route::get('/announcements/{announcement}', [ResidentAnnouncementController::class, 'show'])->name('api.resident.announcements.show');
        Route::get('/visitors', [ResidentVisitorController::class, 'index'])->name('api.resident.visitors.index');
        Route::post('/visitors', [ResidentVisitorController::class, 'store'])->name('api.resident.visitors.store');
        Route::get('/visitors/{visitor}', [ResidentVisitorController::class, 'show'])->name('api.resident.visitors.show');
        Route::patch('/visitors/{visitor}', [ResidentVisitorController::class, 'update'])->name('api.resident.visitors.update');
        Route::post('/visitors/{visitor}/cancel', [ResidentVisitorController::class, 'cancel'])->name('api.resident.visitors.cancel');
        Route::get('/visitors/{visitor}/qr', [ResidentVisitorController::class, 'qr'])->name('api.resident.visitors.qr');
        Route::get('/visitors/{visitor}/identity-photo', [ResidentVisitorController::class, 'identityPhoto'])->name('api.resident.visitors.identity-photo');
    });
});

Route::middleware(['auth:sanctum', 'resident.api'])->group(function () {
    Route::get('/service-request-catalog', [ResidentServiceRequestController::class, 'catalog'])
        ->name('api.service-request-catalog');
    Route::get('/service-requests', [ResidentServiceRequestController::class, 'index'])
        ->name('api.service-requests.index');
    Route::post('/service-requests', [ResidentServiceRequestController::class, 'store'])
        ->name('api.service-requests.store');
    Route::get('/service-requests/{ticket}', [ResidentServiceRequestController::class, 'show'])
        ->name('api.service-requests.show');
});

Route::prefix('security')->group(function () {
    Route::post('/login', [SecurityAuthController::class, 'login'])
        ->middleware('throttle:security-login')
        ->name('api.security.login');

    Route::middleware(['auth:sanctum', 'security.api'])->group(function () {
        Route::post('/logout', [SecurityAuthController::class, 'logout'])->name('api.security.logout');
        Route::post('/visitor-access/validate', [SecurityVisitorAccessController::class, 'validateCode'])->name('api.security.visitor-access.validate');
        Route::get('/visitors/{visitor}/identity-photo', [SecurityVisitorAccessController::class, 'identityPhoto'])->name('api.security.visitors.identity-photo');
    });
});

Route::prefix('technician')->group(function () {
    Route::post('/login', [TechnicianAuthController::class, 'login'])
        ->middleware('throttle:technician-login')
        ->name('api.technician.login');

    Route::middleware(['auth:sanctum', 'technician.api'])->group(function () {
        Route::get('/me', [TechnicianAuthController::class, 'me'])->name('api.technician.me');
        Route::patch('/profile', [TechnicianAuthController::class, 'updateProfile'])->name('api.technician.profile');
        Route::post('/logout', [TechnicianAuthController::class, 'logout'])->name('api.technician.logout');
        Route::get('/hotline', [TechnicianServiceRequestController::class, 'hotline'])->name('api.technician.hotline');
        Route::get('/service-requests', [TechnicianServiceRequestController::class, 'index'])->name('api.technician.service-requests.index');
        Route::get('/service-requests/{ticket}', [TechnicianServiceRequestController::class, 'show'])->name('api.technician.service-requests.show');
        Route::post('/service-requests/{ticket}/on-the-way', [TechnicianServiceRequestController::class, 'onTheWay'])->name('api.technician.service-requests.on-the-way');
        Route::post('/service-requests/{ticket}/start', [TechnicianServiceRequestController::class, 'start'])->name('api.technician.service-requests.start');
        Route::post('/service-requests/{ticket}/complete', [TechnicianServiceRequestController::class, 'complete'])->name('api.technician.service-requests.complete');
    });
});
