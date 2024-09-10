<?php

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
    ->middleware(['auth', 'role_or_permission:admin|view_admin_portal'])
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
        Route::resource('landlord-remittance', LandlordRemittancesController::class);
        Route::resource('expenses', ExpensesController::class);
        Route::resource('payments-proof', PaymentProofController::class);
        Route::resource('deposits', DepositsController::class);
        Route::resource('support-tickets', SupportTicketController::class);
        Route::resource('backups', BackupController::class);
        Route::resource('users-management', UsersController::class);
        Route::resource('roles-management', RolesController::class);
        // ActivityLog invokable controller route
        Route::get('activity-logs', ActivityLogController::class)->name('activity-log.index');
        Route::resource('overpayments', OverpaymentsController::class)
            ->only('index', 'destroy');
        Route::get('payments/list', [PaymentsController::class, 'index'])->name('payments.list');
        Route::get('payments/outstanding', [PaymentsController::class, 'outstanding'])->name('payments.outstanding');
        Route::get('payments/in-arrears', [PaymentsController::class, 'in_arrears'])->name('payments.in_arrears');


        //settings
        Route::controller(SettingsController::class)->group(function () {
            Route::get('settings', 'index')->name('settings.index');
            Route::get('settings/appearance', 'appearance')->name('settings.appearance');
            Route::get('settings/house-types', 'house_types')->name('settings.house_types');
            Route::get('settings/property-types', 'property_types')->name('settings.property_types');
            Route::get('settings/payment-methods', 'payment_methods')->name('settings.payment_methods');
            Route::get('settings/company-details', 'company_settings')->name('settings.company_details');
            Route::get('settings/expense-types', 'expense_types')->name('settings.expense_types');

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


