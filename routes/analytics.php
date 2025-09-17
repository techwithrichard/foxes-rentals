<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AnalyticsController;

/*
|--------------------------------------------------------------------------
| Analytics Routes
|--------------------------------------------------------------------------
|
| These routes handle comprehensive analytics and reporting functionality
|
*/

Route::middleware(['auth', 'role_or_permission:super_admin|admin'])
    ->prefix('admin/analytics')
    ->name('admin.analytics.')
    ->group(function () {
        
        // Main Analytics Dashboard
        Route::get('/', [AnalyticsController::class, 'dashboard'])->name('dashboard');
        
        // User Analytics
        Route::get('users', [AnalyticsController::class, 'users'])->name('users');
        
        // Financial Analytics
        Route::get('financial', [AnalyticsController::class, 'financial'])->name('financial');
        
        // Property Analytics
        Route::get('properties', [AnalyticsController::class, 'properties'])->name('properties');
        
        // Performance Analytics
        Route::get('performance', [AnalyticsController::class, 'performance'])->name('performance');
        
        // System Analytics
        Route::get('system', [AnalyticsController::class, 'system'])->name('system');
        
        // Export Analytics Data
        Route::get('export', [AnalyticsController::class, 'export'])->name('export');
    });
