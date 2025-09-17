<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;

/*
|--------------------------------------------------------------------------
| User Management Routes
|--------------------------------------------------------------------------
|
| These routes handle comprehensive user management functionality
|
*/

Route::middleware(['auth', 'role_or_permission:super_admin|admin'])
    ->prefix('admin/user-management')
    ->name('admin.user-management.')
    ->group(function () {
        
        // Main Dashboard
        Route::get('/', [UserManagementController::class, 'dashboard'])->name('dashboard');
        
        // Landlord Management Routes
        Route::prefix('landlords')->name('landlords.')->group(function () {
            Route::get('properties', [UserManagementController::class, 'landlordProperties'])->name('properties');
            Route::get('payments', [UserManagementController::class, 'landlordPayments'])->name('payments');
            Route::get('reports', [UserManagementController::class, 'landlordReports'])->name('reports');
            Route::get('activity', [UserManagementController::class, 'landlordActivity'])->name('activity');
        });
        
        // Tenant Management Routes
        Route::prefix('tenants')->name('tenants.')->group(function () {
            Route::get('active', [UserManagementController::class, 'activeTenants'])->name('active');
            Route::get('leases', [UserManagementController::class, 'tenantLeases'])->name('leases');
            Route::get('payments', [UserManagementController::class, 'tenantPayments'])->name('payments');
            Route::get('activity', [UserManagementController::class, 'tenantActivity'])->name('activity');
        });
        
        // Analytics and Settings
        Route::get('analytics', [UserManagementController::class, 'analytics'])->name('analytics');
        Route::get('settings', [UserManagementController::class, 'settings'])->name('settings');
        Route::post('settings', [UserManagementController::class, 'updateSettings'])->name('settings.update');
    });
