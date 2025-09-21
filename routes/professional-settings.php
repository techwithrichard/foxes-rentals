<?php

use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Professional Settings Routes
|--------------------------------------------------------------------------
|
| Clean, professional route structure without redundant "settings" prefixes
| Organized by logical business functions
|
*/

Route::middleware(['auth', 'role_or_permission:super_admin|admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        
        // ========================================
        // SYSTEM CONFIGURATION (Main Settings)
        // ========================================
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'index'])->name('index');
            Route::get('/general', [SettingsController::class, 'general'])->name('general');
            Route::get('/company', [SettingsController::class, 'company_settings'])->name('company');
            Route::get('/appearance', [SettingsController::class, 'appearance'])->name('appearance');
            Route::get('/localization', [SettingsController::class, 'localization'])->name('localization');
            
            // API Routes
            Route::post('/update-setting', [SettingsController::class, 'updateSetting'])->name('update-setting');
            Route::post('/update-multiple', [SettingsController::class, 'updateMultiple'])->name('update-multiple');
            Route::post('/clear-cache', [SettingsController::class, 'clearCache'])->name('clear-cache');
            Route::get('/export', [SettingsController::class, 'exportSettings'])->name('export');
            Route::post('/import', [SettingsController::class, 'importSettings'])->name('import');
        });
        
        // ========================================
        // PROPERTY MANAGEMENT
        // ========================================
        Route::prefix('property-types')->name('property-types.')->group(function () {
            Route::get('/', [PropertyTypeController::class, 'index'])->name('index');
            Route::get('/create', [PropertyTypeController::class, 'create'])->name('create');
            Route::post('/', [PropertyTypeController::class, 'store'])->name('store');
            Route::get('/{propertyType}', [PropertyTypeController::class, 'show'])->name('show');
            Route::get('/{propertyType}/edit', [PropertyTypeController::class, 'edit'])->name('edit');
            Route::put('/{propertyType}', [PropertyTypeController::class, 'update'])->name('update');
            Route::delete('/{propertyType}', [PropertyTypeController::class, 'destroy'])->name('destroy');
            Route::post('/{propertyType}/toggle-status', [PropertyTypeController::class, 'toggleStatus'])->name('toggle-status');
            Route::post('/bulk-action', [PropertyTypeController::class, 'bulkAction'])->name('bulk-action');
        });
        
        Route::prefix('house-types')->name('house-types.')->group(function () {
            Route::get('/', [SettingsController::class, 'house_types'])->name('index');
        });
        
        Route::prefix('amenities')->name('amenities.')->group(function () {
            Route::get('/', [SettingsController::class, 'amenities'])->name('index');
        });
        
        Route::prefix('pricing-rules')->name('pricing-rules.')->group(function () {
            Route::get('/', [SettingsController::class, 'pricing'])->name('index');
        });
        
        // ========================================
        // FINANCIAL CONFIGURATION
        // ========================================
        Route::prefix('payment-methods')->name('payment-methods.')->group(function () {
            Route::get('/', [SettingsController::class, 'payment_methods'])->name('index');
        });
        
        Route::prefix('expense-types')->name('expense-types.')->group(function () {
            Route::get('/', [SettingsController::class, 'expense_types'])->name('index');
        });
        
        Route::prefix('currency')->name('currency.')->group(function () {
            Route::get('/', [SettingsController::class, 'currency'])->name('index');
        });
        
        Route::prefix('tax-settings')->name('tax-settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'tax_settings'])->name('index');
        });
        
        // ========================================
        // USER MANAGEMENT
        // ========================================
        Route::prefix('users-management')->name('users-management.')->group(function () {
            Route::get('/', [UsersController::class, 'index'])->name('index');
            Route::get('/create', [UsersController::class, 'create'])->name('create');
            Route::post('/', [UsersController::class, 'store'])->name('store');
            Route::get('/{user}', [UsersController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UsersController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UsersController::class, 'update'])->name('update');
            Route::delete('/{user}', [UsersController::class, 'destroy'])->name('destroy');
        });
        
        Route::prefix('roles-management')->name('roles-management.')->group(function () {
            Route::get('/', [RolesController::class, 'index'])->name('index');
            Route::get('/create', [RolesController::class, 'create'])->name('create');
            Route::post('/', [RolesController::class, 'store'])->name('store');
            Route::get('/{role}', [RolesController::class, 'show'])->name('show');
            Route::get('/{role}/edit', [RolesController::class, 'edit'])->name('edit');
            Route::put('/{role}', [RolesController::class, 'update'])->name('update');
            Route::delete('/{role}', [RolesController::class, 'destroy'])->name('destroy');
        });
        
        Route::prefix('permissions')->name('permissions.')->group(function () {
            Route::get('/', [SettingsController::class, 'permissions'])->name('index');
        });
        
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/', [SettingsController::class, 'security'])->name('index');
        });
        
        // ========================================
        // SYSTEM & INTEGRATION
        // ========================================
        Route::prefix('api-keys')->name('api-keys.')->group(function () {
            Route::get('/', [SettingsController::class, 'api_keys'])->name('index');
        });
        
        Route::prefix('integrations')->name('integrations.')->group(function () {
            Route::get('/', [SettingsController::class, 'integrations'])->name('index');
        });
        
        Route::prefix('backups')->name('backups.')->group(function () {
            Route::get('/', [SettingsController::class, 'backups'])->name('index');
        });
        
        Route::prefix('system-health')->name('system-health.')->group(function () {
            Route::get('/', [SettingsController::class, 'system_health'])->name('index');
        });
        
        // ========================================
        // COMMUNICATION
        // ========================================
        Route::prefix('email-templates')->name('email-templates.')->group(function () {
            Route::get('/', [SettingsController::class, 'email_templates'])->name('index');
        });
        
        Route::prefix('notifications')->name('notifications.')->group(function () {
            Route::get('/', [SettingsController::class, 'notifications'])->name('index');
        });
        
        Route::prefix('sms-settings')->name('sms-settings.')->group(function () {
            Route::get('/', [SettingsController::class, 'sms_settings'])->name('index');
        });
        
        // ========================================
        // REPORTS & ANALYTICS
        // ========================================
        Route::prefix('analytics')->name('analytics.')->group(function () {
            Route::get('/', [SettingsController::class, 'analytics'])->name('index');
            Route::get('/dashboard', [SettingsController::class, 'analytics'])->name('dashboard');
        });
        
        Route::prefix('report-templates')->name('report-templates.')->group(function () {
            Route::get('/', [SettingsController::class, 'report_templates'])->name('index');
        });
        
        Route::prefix('scheduled-reports')->name('scheduled-reports.')->group(function () {
            Route::get('/', [SettingsController::class, 'scheduled_reports'])->name('index');
        });
        
    });