<?php

// Include enhanced admin routes
require_once __DIR__ . '/enhanced-admin.php';

// Include user management routes
require_once __DIR__ . '/user-management.php';

// Include analytics routes
require_once __DIR__ . '/analytics.php';

use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\ArchivedTenantsController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\BillsInvoiceController;
use App\Http\Controllers\Admin\CustomInvoiceController;
use App\Http\Controllers\Admin\DepositsController;
use App\Http\Controllers\Admin\ExpensesController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\HouseController;
use App\Http\Controllers\Admin\LandlordRemittancesController;
use App\Http\Controllers\Admin\LandlordsController;
use App\Http\Controllers\Admin\LeaseController;
use App\Http\Controllers\Admin\LeaseHistoryController;
use App\Http\Controllers\Admin\LeaseTerminationNoticeController;
use App\Http\Controllers\Admin\MpesaHistoryController;
use App\Http\Controllers\Admin\OverpaymentsController;
use App\Http\Controllers\Admin\PaymentProofController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\PaymentVerificationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\RentInvoiceController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SupportTicketController;
use App\Http\Controllers\Admin\TenantsController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\VouchersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('admin')
    ->middleware(['auth', 'role_or_permission:super_admin|admin'])
    ->name('admin.')->group(function () {
        Route::get('/', [HomeController::class, 'index'])
            ->name('home');
        Route::get('/notifications', [HomeController::class, 'notifications'])
            ->name('notifications');

        Route::get('tenants/export', [TenantsController::class, 'export'])
            ->name('tenants.export');
        Route::resource('tenants', TenantsController::class);
        Route::resource('landlords', LandlordsController::class);
        Route::controller(ArchivedTenantsController::class)->group(function () {
            Route::get('archived-tenants', 'index')->name('archived-tenants.index');
            Route::delete('archived-tenants/{id}/delete', 'destroy')->name('archived-tenants.destroy');
            Route::get('archived-tenants/{id}/restore', 'restore')->name('archived-tenants.restore');
        });
        Route::resource('properties', PropertyController::class);
        Route::resource('houses', HouseController::class);
        
        // Enhanced Property Management Routes
        Route::prefix('property-dashboard')->name('property-dashboard.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PropertyDashboardController::class, 'index'])->name('index');
            Route::get('/analytics', [App\Http\Controllers\Admin\PropertyDashboardController::class, 'analytics'])->name('analytics');
            Route::get('/metrics', [App\Http\Controllers\Admin\PropertyDashboardController::class, 'metrics'])->name('metrics');
        });
        
        Route::prefix('properties-for-rent')->name('properties-for-rent.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PropertiesForRentController::class, 'index'])->name('index');
            Route::get('/vacant', [App\Http\Controllers\Admin\PropertiesForRentController::class, 'vacant'])->name('vacant');
            Route::get('/applications', [App\Http\Controllers\Admin\PropertiesForRentController::class, 'applications'])->name('applications');
            Route::get('/active-rentals', [App\Http\Controllers\Admin\PropertiesForRentController::class, 'activeRentals'])->name('active-rentals');
        });
        
        Route::prefix('properties-for-sale')->name('properties-for-sale.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PropertiesForSaleController::class, 'index'])->name('index');
            Route::get('/active-listings', [App\Http\Controllers\Admin\PropertiesForSaleController::class, 'activeListings'])->name('active-listings');
            Route::get('/pending-sales', [App\Http\Controllers\Admin\PropertiesForSaleController::class, 'pendingSales'])->name('pending-sales');
            Route::get('/offers', [App\Http\Controllers\Admin\PropertiesForSaleController::class, 'offers'])->name('offers');
        });
        
        Route::prefix('properties-for-lease')->name('properties-for-lease.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PropertiesForLeaseController::class, 'index'])->name('index');
            Route::get('/active-leases', [App\Http\Controllers\Admin\PropertiesForLeaseController::class, 'activeLeases'])->name('active-leases');
            Route::get('/lease-applications', [App\Http\Controllers\Admin\PropertiesForLeaseController::class, 'leaseApplications'])->name('lease-applications');
            Route::get('/renewals', [App\Http\Controllers\Admin\PropertiesForLeaseController::class, 'renewals'])->name('renewals');
        });
        
        Route::prefix('property-settings')->name('property-settings.')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\PropertySettingsController::class, 'index'])->name('index');
            Route::get('/types', [App\Http\Controllers\Admin\PropertySettingsController::class, 'types'])->name('types');
            Route::get('/amenities', [App\Http\Controllers\Admin\PropertySettingsController::class, 'amenities'])->name('amenities');
            Route::get('/pricing', [App\Http\Controllers\Admin\PropertySettingsController::class, 'pricing'])->name('pricing');
        });
        Route::resource('leases', LeaseController::class);
        Route::resource('leases-history', LeaseHistoryController::class);
        Route::resource('leases-termination-notice', LeaseTerminationNoticeController::class)
            ->only('index');
        Route::get('custom-invoice/{id}/print', [CustomInvoiceController::class, 'print'])
            ->name('custom-invoice.print');
        Route::resource('custom-invoice', CustomInvoiceController::class);

        //Rent invoice controller

        Route::controller(RentInvoiceController::class)->group(function () {
            Route::get('rent-invoice', 'index')->name('rent-invoice.index');
            Route::get('rent-invoice/{id}/show', 'show')->name('rent-invoice.show');
            Route::get('rent-invoice/{id}/print', 'print')->name('rent-invoice.print');
            Route::get('rent-invoice/{id}/edit', 'edit')->name('rent-invoice.edit');
        });

        //Mpesa transactions
        Route::controller(MpesaHistoryController::class)->group(function () {
            Route::get('mpesa-stk-transactions', 'stkPushTransactions')->name('mpesa-stk-transactions');
            Route::get('mpesa-c2b-transactions', 'c2bTransactions')->name('mpesa-c2b-transactions');
            Route::delete('mpesa-c2b-transactions/{id}/destroy', 'deleteTransaction')->name('mpesa-c2b-transactions.destroy');
            Route::get('mpesa-c2b-transactions/{id}/reconcile', 'reconcileTransaction')->name('mpesa-c2b-transactions.reconcile');
        });
        Route::get('vouchers/{id}/print', [VouchersController::class, 'print'])
            ->name('vouchers.print');
        Route::resource('vouchers', VouchersController::class);

        // Deleted Records Management
        Route::controller(\App\Http\Controllers\Admin\DeletedRecordsController::class)->group(function () {
            Route::get('deleted-records', 'index')->name('deleted-records.index');
            Route::get('deleted-records/houses', 'houses')->name('deleted-records.houses');
            Route::get('deleted-records/leases', 'leases')->name('deleted-records.leases');
            Route::get('deleted-records/tenants', 'tenants')->name('deleted-records.tenants');
            
            // Restore routes
            Route::post('deleted-records/houses/{id}/restore', 'restoreHouse')->name('deleted-records.houses.restore');
            Route::post('deleted-records/leases/{id}/restore', 'restoreLease')->name('deleted-records.leases.restore');
            Route::post('deleted-records/tenants/{id}/restore', 'restoreTenant')->name('deleted-records.tenants.restore');
            
            // Permanent delete routes (admin only)
            Route::delete('deleted-records/houses/{id}/permanent-delete', 'permanentlyDeleteHouse')->name('deleted-records.houses.permanent-delete');
            Route::delete('deleted-records/leases/{id}/permanent-delete', 'permanentlyDeleteLease')->name('deleted-records.leases.permanent-delete');
            Route::delete('deleted-records/tenants/{id}/permanent-delete', 'permanentlyDeleteTenant')->name('deleted-records.tenants.permanent-delete');
        });
        Route::resource('landlord-remittance', LandlordRemittancesController::class);
        Route::resource('expenses', ExpensesController::class);
        Route::resource('payments-proof', PaymentProofController::class);
        Route::resource('deposits', DepositsController::class);
        Route::resource('support-tickets', SupportTicketController::class);
        Route::resource('backups', BackupController::class);
        Route::resource('users-management', UsersController::class);
        Route::resource('roles-management', RolesController::class);
        
        // Advanced User Management Routes
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('advanced', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'index'])->name('advanced.index');
            Route::get('advanced/create', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'create'])->name('advanced.create');
            Route::post('advanced', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'store'])->name('advanced.store');
            Route::get('advanced/{user}', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'show'])->name('advanced.show');
            Route::get('advanced/{user}/edit', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'edit'])->name('advanced.edit');
            Route::put('advanced/{user}', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'update'])->name('advanced.update');
            Route::delete('advanced/{user}', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'destroy'])->name('advanced.destroy');
            Route::post('advanced/{id}/restore', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'restore'])->name('advanced.restore');
            Route::delete('advanced/{id}/force-delete', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'forceDelete'])->name('advanced.force-delete');
            Route::post('advanced/bulk-action', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'bulkAction'])->name('advanced.bulk-action');
            Route::get('advanced/export', [App\Http\Controllers\Admin\AdvancedUserManagementController::class, 'export'])->name('advanced.export');
        });
        // ActivityLog invokable controller route
        Route::get('activity-logs', ActivityLogController::class)->name('activity-log.index');
        Route::resource('overpayments', OverpaymentsController::class)
            ->only('index', 'destroy');
        Route::get('payments/list', [PaymentsController::class, 'index'])->name('payments.list');
        Route::get('payments/outstanding', [PaymentsController::class, 'outstanding'])->name('payments.outstanding');
        Route::get('payments/in-arrears', [PaymentsController::class, 'in_arrears'])->name('payments.in_arrears');
        Route::delete('payments/{id}', [PaymentsController::class, 'destroy'])->name('payments.destroy');

        // Payment Verification System
        Route::controller(PaymentVerificationController::class)->group(function () {
            Route::get('payment-verification', 'index')->name('payment-verification.index');
            Route::get('payment-verification/unverified', 'unverifiedPayments')->name('payment-verification.unverified');
            Route::get('payment-verification/create', 'create')->name('payment-verification.create');
            Route::post('payment-verification', 'store')->name('payment-verification.store');
            Route::post('payment-verification/search', 'searchTransaction')->name('payment-verification.search');
            Route::post('payment-verification/{id}/verify', 'verifyPayment')->name('payment-verification.verify');
            Route::get('payment-verification/tenant-invoices', 'getTenantInvoices')->name('payment-verification.tenant-invoices');
            Route::get('payment-verification/statistics', 'statistics')->name('payment-verification.statistics');
        });


        //settings
        Route::controller(SettingsController::class)->group(function () {
            Route::get('settings', 'index')->name('settings.index');
            Route::get('settings/appearance', 'appearance')->name('settings.appearance');
            Route::get('settings/house-types', 'house_types')->name('settings.house_types');
            Route::get('settings/property-types', 'property_types')->name('settings.property_types');
            Route::get('settings/payment-methods', 'payment_methods')->name('settings.payment_methods');
            Route::get('settings/company-details', 'company_settings')->name('settings.company_details');
            Route::get('settings/expense-types', 'expense_types')->name('settings.expense_types');
            
            // Additional settings routes for sidebar compatibility
            Route::get('settings/general', 'index')->name('settings.general');
            Route::get('settings/financial', 'payment_methods')->name('settings.financial');
            Route::get('settings/system', 'appearance')->name('settings.system');
            
            // Enhanced Settings API Routes
            Route::post('settings/update-setting', 'updateSetting')->name('settings.update-setting');
            Route::post('settings/bulk-update', 'bulkUpdate')->name('settings.bulk-update');
            Route::get('settings/get-setting', 'getSetting')->name('settings.get-setting');
            Route::get('settings/history', 'getHistory')->name('settings.history');
            Route::post('settings/clear-cache', 'clearCache')->name('settings.clear-cache');
            Route::get('settings/export', 'exportSettings')->name('settings.export');
            Route::post('settings/import', 'importSettings')->name('settings.import');
        });

        // API Keys Management Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::resource('api-keys', App\Http\Controllers\Admin\ApiKeysController::class);
            Route::post('api-keys/{apiKey}/toggle-status', [App\Http\Controllers\Admin\ApiKeysController::class, 'toggleStatus'])->name('api-keys.toggle-status');
            Route::post('api-keys/{apiKey}/test-connection', [App\Http\Controllers\Admin\ApiKeysController::class, 'testConnection'])->name('api-keys.test-connection');
            Route::post('api-keys/{apiKey}/regenerate', [App\Http\Controllers\Admin\ApiKeysController::class, 'regenerate'])->name('api-keys.regenerate');
            Route::post('api-keys/bulk-action', [App\Http\Controllers\Admin\ApiKeysController::class, 'bulkAction'])->name('api-keys.bulk-action');
        });

        // Property Settings Management Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            // Property Settings Dashboard
            Route::get('property', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'index'])->name('property.index');
            
            // Property Types Management
            Route::get('property/types', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'propertyTypes'])->name('property.types');
            Route::get('property/types/create', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'createPropertyType'])->name('property.types.create');
            Route::post('property/types', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'storePropertyType'])->name('property.types.store');
            Route::get('property/types/{propertyType}/edit', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'editPropertyType'])->name('property.types.edit');
            Route::put('property/types/{propertyType}', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'updatePropertyType'])->name('property.types.update');
            Route::delete('property/types/{propertyType}', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'destroyPropertyType'])->name('property.types.destroy');
            
            // Amenities Management
            Route::get('property/amenities', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'amenities'])->name('property.amenities');
            Route::get('property/amenities/create', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'createAmenity'])->name('property.amenities.create');
            Route::post('property/amenities', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'storeAmenity'])->name('property.amenities.store');
            Route::get('property/amenities/{amenity}/edit', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'editAmenity'])->name('property.amenities.edit');
            Route::put('property/amenities/{amenity}', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'updateAmenity'])->name('property.amenities.update');
            Route::delete('property/amenities/{amenity}', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'destroyAmenity'])->name('property.amenities.destroy');
            
            // Pricing Rules Management
            Route::get('property/pricing', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'pricingRules'])->name('property.pricing');
            Route::get('property/pricing/create', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'createPricingRule'])->name('property.pricing.create');
            Route::post('property/pricing', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'storePricingRule'])->name('property.pricing.store');
            
            // Lease Templates Management
            Route::get('property/lease-templates', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'leaseTemplates'])->name('property.lease-templates');
            Route::get('property/lease-templates/create', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'createLeaseTemplate'])->name('property.lease-templates.create');
            Route::post('property/lease-templates', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'storeLeaseTemplate'])->name('property.lease-templates.store');
            
            // Bulk Operations
            Route::post('property/bulk-action', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'bulkAction'])->name('property.bulk-action');
            
            // API Routes
            Route::get('property/statistics', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'getStatistics'])->name('property.statistics');
            Route::get('property/export', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'exportSettings'])->name('property.export');
            Route::post('property/import', [App\Http\Controllers\Admin\Settings\PropertySettingsController::class, 'importSettings'])->name('property.import');
        });

        // Advanced Roles Management Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('roles', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'index'])->name('roles.index');
            Route::get('roles/create', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'create'])->name('roles.create');
            Route::post('roles', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'store'])->name('roles.store');
            Route::get('roles/{role}', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'show'])->name('roles.show');
            Route::get('roles/{role}/edit', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'edit'])->name('roles.edit');
            Route::put('roles/{role}', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'update'])->name('roles.update');
            Route::delete('roles/{role}', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'destroy'])->name('roles.destroy');
            Route::post('roles/{role}/toggle-status', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'toggleStatus'])->name('roles.toggle-status');
            Route::post('roles/{role}/duplicate', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'duplicate'])->name('roles.duplicate');
            Route::post('roles/bulk-action', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'bulkAction'])->name('roles.bulk-action');
            Route::get('roles/export', [App\Http\Controllers\Admin\AdvancedRolesController::class, 'export'])->name('roles.export');
        });

        // Advanced Permissions Management Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('permissions', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'index'])->name('permissions.index');
            Route::get('permissions/create', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'create'])->name('permissions.create');
            Route::post('permissions', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'store'])->name('permissions.store');
            Route::get('permissions/{permission}', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'show'])->name('permissions.show');
            Route::get('permissions/{permission}/edit', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'edit'])->name('permissions.edit');
            Route::put('permissions/{permission}', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'update'])->name('permissions.update');
            Route::delete('permissions/{permission}', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'destroy'])->name('permissions.destroy');
            Route::post('permissions/bulk-create', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'bulkCreate'])->name('permissions.bulk-create');
        });

        // Advanced Settings Management Routes
        Route::prefix('settings')->name('settings.')->group(function () {
            Route::get('advanced', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'index'])->name('advanced.index');
            Route::get('advanced/{categorySlug}', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'category'])->name('advanced.category');
            
            // API Routes for settings management
            Route::post('advanced/update-setting', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'updateSetting'])->name('advanced.update-setting');
            Route::post('advanced/update-multiple', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'updateMultipleSettings'])->name('advanced.update-multiple');
            Route::get('advanced/history', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'getHistory'])->name('advanced.history');
            Route::get('advanced/export', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'exportSettings'])->name('advanced.export');
            Route::post('advanced/import', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'importSettings'])->name('advanced.import');
            Route::post('advanced/clear-cache', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'clearCache'])->name('advanced.clear-cache');
            Route::get('advanced/get-setting', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'getSetting'])->name('advanced.get-setting');
            
            // CRUD Routes for settings management
            Route::post('advanced/categories', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'createCategory'])->name('advanced.create-category');
            Route::post('advanced/groups', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'createGroup'])->name('advanced.create-group');
            Route::post('advanced/settings', [App\Http\Controllers\Admin\AdvancedSettingsController::class, 'createSetting'])->name('advanced.create-setting');
            Route::post('permissions/bulk-action', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'bulkAction'])->name('permissions.bulk-action');
            Route::get('permissions/export', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'export'])->name('permissions.export');
            Route::get('permissions/statistics', [App\Http\Controllers\Admin\AdvancedPermissionsController::class, 'statistics'])->name('permissions.statistics');

            // User Settings Routes
            Route::prefix('users')->name('users.')->group(function () {
                Route::get('/', [App\Http\Controllers\Admin\UserSettingsController::class, 'index'])->name('index');
                Route::get('roles', [App\Http\Controllers\Admin\UserSettingsController::class, 'roles'])->name('roles');
                Route::get('permissions', [App\Http\Controllers\Admin\UserSettingsController::class, 'permissions'])->name('permissions');
                Route::get('profiles', [App\Http\Controllers\Admin\UserSettingsController::class, 'profiles'])->name('profiles');
                Route::get('security', [App\Http\Controllers\Admin\UserSettingsController::class, 'security'])->name('security');
                Route::get('registration', [App\Http\Controllers\Admin\UserSettingsController::class, 'registration'])->name('registration');
                Route::post('update-setting', [App\Http\Controllers\Admin\UserSettingsController::class, 'updateSetting'])->name('update-setting');
            });
        });


        //reports
        Route::controller(ReportsController::class)->group(function () {
            Route::get('reports/landlord-income', 'landlordIncome')->name('reports.landlord_income');
            Route::get('reports/property-income', 'propertyIncome')->name('reports.property_income');
            Route::get('reports/company-income', 'companyIncome')->name('reports.company_income');
            Route::get('reports/landlord-expenses', 'landlordExpenses')->name('reports.landlord_expenses');
            Route::get('reports/company-expenses', 'companyExpenses')->name('reports.company_expenses');
            Route::get('reports/outstanding-payments', 'outstandingPayments')->name('reports.outstanding_payments');
            Route::get('reports/expiring-leases', 'expiringLeases')->name('reports.expiring_leases');
            Route::get('reports/maintenance', 'maintenance')->name('reports.maintenance');
            Route::get('reports/occupancy', 'occupancy')->name('reports.occupancy');
        });

        Route::controller(ProfileController::class)->group(function () {
            Route::get('/user-profile', 'index')
                ->name('profile');
            Route::get('/user-profile-activity', 'login_activities')
                ->name('login_activities');
            Route::get('/user-profile-settings', 'security_settings')
                ->name('security_settings');
        });

    });


