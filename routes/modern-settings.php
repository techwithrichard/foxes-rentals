<?php

use App\Http\Controllers\Admin\Settings\ModernSettingsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Modern Settings Routes
|--------------------------------------------------------------------------
|
| Consolidated settings routes with better organization and no redundancy
|
*/

Route::middleware(['auth', 'role_or_permission:super_admin|admin'])
    ->prefix('admin/settings')
    ->name('admin.settings.')
    ->group(function () {
        
        // Main Settings Dashboard
        Route::get('/', [ModernSettingsController::class, 'index'])->name('index');
        
        // General Settings
        Route::get('/general', [ModernSettingsController::class, 'general'])->name('general');
        Route::get('/company', [ModernSettingsController::class, 'company'])->name('company');
        Route::get('/appearance', [ModernSettingsController::class, 'appearance'])->name('appearance');
        
        // Property Management Settings
        Route::get('/property-types', [ModernSettingsController::class, 'propertyTypes'])->name('property-types');
        Route::get('/house-types', [ModernSettingsController::class, 'houseTypes'])->name('house-types');
        Route::get('/amenities', [ModernSettingsController::class, 'amenities'])->name('amenities');
        
        // Financial Settings
        Route::get('/payment-methods', [ModernSettingsController::class, 'paymentMethods'])->name('payment-methods');
        Route::get('/expense-types', [ModernSettingsController::class, 'expenseTypes'])->name('expense-types');
        Route::get('/currency', [ModernSettingsController::class, 'currency'])->name('currency');
        
        // User & Security Settings
        Route::get('/security', [ModernSettingsController::class, 'security'])->name('security');
        
        // System & Integration Settings
        Route::get('/integrations', [ModernSettingsController::class, 'integrations'])->name('integrations');
        Route::get('/backup', [ModernSettingsController::class, 'backup'])->name('backup');
        
        // Analytics & Reports Settings
        Route::get('/reports', [ModernSettingsController::class, 'reports'])->name('reports');
        
        // API Routes for Settings Management
        Route::post('/update-setting', [ModernSettingsController::class, 'updateSetting'])->name('update-setting');
        Route::post('/update-multiple', [ModernSettingsController::class, 'updateMultiple'])->name('update-multiple');
        Route::post('/clear-cache', [ModernSettingsController::class, 'clearCache'])->name('clear-cache');
        Route::get('/export', [ModernSettingsController::class, 'export'])->name('export');
        Route::post('/import', [ModernSettingsController::class, 'import'])->name('import');
        
        // Legacy Route Compatibility (redirect to new routes)
        Route::get('/dashboard', function () {
            return redirect()->route('admin.settings.index');
        })->name('dashboard');
        
        Route::get('/financial', function () {
            return redirect()->route('admin.settings.payment-methods');
        })->name('financial');
        
        Route::get('/system', function () {
            return redirect()->route('admin.settings.general');
        })->name('system');
    });
