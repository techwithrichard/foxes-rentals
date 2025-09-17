<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PropertyTypeController;
use App\Http\Controllers\Admin\RentalPropertyController;
use App\Http\Controllers\Admin\SalePropertyController;
use App\Http\Controllers\Admin\LeasePropertyController;
use App\Http\Controllers\Admin\RentalUnitController;
use App\Http\Controllers\Admin\PropertyInquiryController;
// use App\Http\Controllers\Admin\PropertyApplicationController;
// use App\Http\Controllers\Admin\PropertyOfferController;
// use App\Http\Controllers\Admin\LeaseAgreementController;
// use App\Http\Controllers\Admin\MaintenanceRequestController;

/*
|--------------------------------------------------------------------------
| Enhanced Admin Routes
|--------------------------------------------------------------------------
|
| These routes are for the enhanced property management system
| following SOLID principles and comprehensive real estate management.
|
*/

Route::middleware(['auth', 'verified', 'role_or_permission:super_admin|admin'])->prefix('admin')->name('admin.')->group(function () {
    
    // Property Types Management
    Route::resource('property-types', PropertyTypeController::class);
    Route::post('property-types/{propertyType}/toggle-status', [PropertyTypeController::class, 'toggleStatus'])
        ->name('property-types.toggle-status');

    // Rental Properties Management
    Route::prefix('rental-properties')->name('rental-properties.')->group(function () {
        Route::get('/', [RentalPropertyController::class, 'index'])->name('index');
        Route::get('/all', [RentalPropertyController::class, 'all'])->name('all');
        Route::get('/vacant', [RentalPropertyController::class, 'vacant'])->name('vacant');
        Route::get('/occupied', [RentalPropertyController::class, 'occupied'])->name('occupied');
        Route::get('/featured', [RentalPropertyController::class, 'featured'])->name('featured');
        Route::get('/create', [RentalPropertyController::class, 'create'])->name('create');
        Route::post('/', [RentalPropertyController::class, 'store'])->name('store');
        Route::get('/{rentalProperty}', [RentalPropertyController::class, 'show'])->name('show');
        Route::get('/{rentalProperty}/edit', [RentalPropertyController::class, 'edit'])->name('edit');
        Route::put('/{rentalProperty}', [RentalPropertyController::class, 'update'])->name('update');
        Route::delete('/{rentalProperty}', [RentalPropertyController::class, 'destroy'])->name('destroy');
        Route::post('/{rentalProperty}/toggle-featured', [RentalPropertyController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{rentalProperty}/toggle-published', [RentalPropertyController::class, 'togglePublished'])->name('toggle-published');
    });

    // Sale Properties Management
    Route::prefix('sale-properties')->name('sale-properties.')->group(function () {
        Route::get('/', [SalePropertyController::class, 'index'])->name('index');
        Route::get('/all', [SalePropertyController::class, 'all'])->name('all');
        Route::get('/available', [SalePropertyController::class, 'available'])->name('available');
        Route::get('/featured', [SalePropertyController::class, 'featured'])->name('featured');
        Route::get('/create', [SalePropertyController::class, 'create'])->name('create');
        Route::post('/', [SalePropertyController::class, 'store'])->name('store');
        Route::get('/{saleProperty}', [SalePropertyController::class, 'show'])->name('show');
        Route::get('/{saleProperty}/edit', [SalePropertyController::class, 'edit'])->name('edit');
        Route::put('/{saleProperty}', [SalePropertyController::class, 'update'])->name('update');
        Route::delete('/{saleProperty}', [SalePropertyController::class, 'destroy'])->name('destroy');
        Route::post('/{saleProperty}/toggle-featured', [SalePropertyController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{saleProperty}/toggle-published', [SalePropertyController::class, 'togglePublished'])->name('toggle-published');
    });

    // Lease Properties Management
    Route::prefix('lease-properties')->name('lease-properties.')->group(function () {
        Route::get('/', [LeasePropertyController::class, 'index'])->name('index');
        Route::get('/all', [LeasePropertyController::class, 'all'])->name('all');
        Route::get('/available', [LeasePropertyController::class, 'available'])->name('available');
        Route::get('/create', [LeasePropertyController::class, 'create'])->name('create');
        Route::post('/', [LeasePropertyController::class, 'store'])->name('store');
        Route::get('/{leaseProperty}', [LeasePropertyController::class, 'show'])->name('show');
        Route::get('/{leaseProperty}/edit', [LeasePropertyController::class, 'edit'])->name('edit');
        Route::put('/{leaseProperty}', [LeasePropertyController::class, 'update'])->name('update');
        Route::delete('/{leaseProperty}', [LeasePropertyController::class, 'destroy'])->name('destroy');
        Route::post('/{leaseProperty}/toggle-featured', [LeasePropertyController::class, 'toggleFeatured'])->name('toggle-featured');
        Route::post('/{leaseProperty}/toggle-published', [LeasePropertyController::class, 'togglePublished'])->name('toggle-published');
    });

    // Rental Units Management
    Route::resource('rental-units', RentalUnitController::class);
    Route::post('rental-units/{rentalUnit}/toggle-status', [RentalUnitController::class, 'toggleStatus'])
        ->name('rental-units.toggle-status');

    // Property Inquiries Management
    Route::resource('property-inquiries', PropertyInquiryController::class);
    Route::post('property-inquiries/{inquiry}/assign', [PropertyInquiryController::class, 'assign'])
        ->name('property-inquiries.assign');
    Route::post('property-inquiries/{inquiry}/qualify', [PropertyInquiryController::class, 'qualify'])
        ->name('property-inquiries.qualify');

    // Property Applications Management
    // Route::resource('property-applications', PropertyApplicationController::class);
    // Route::post('property-applications/{application}/approve', [PropertyApplicationController::class, 'approve'])
    //     ->name('property-applications.approve');
    // Route::post('property-applications/{application}/reject', [PropertyApplicationController::class, 'reject'])
    //     ->name('property-applications.reject');

    // Property Offers Management
    // Route::resource('property-offers', PropertyOfferController::class);
    // Route::post('property-offers/{offer}/accept', [PropertyOfferController::class, 'accept'])
    //     ->name('property-offers.accept');
    // Route::post('property-offers/{offer}/reject', [PropertyOfferController::class, 'reject'])
    //     ->name('property-offers.reject');
    // Route::post('property-offers/{offer}/counter', [PropertyOfferController::class, 'counter'])
    //     ->name('property-offers.counter');

    // Lease Agreements Management
    // Route::resource('lease-agreements', LeaseAgreementController::class);
    // Route::post('lease-agreements/{agreement}/renew', [LeaseAgreementController::class, 'renew'])
    //     ->name('lease-agreements.renew');
    // Route::post('lease-agreements/{agreement}/terminate', [LeaseAgreementController::class, 'terminate'])
    //     ->name('lease-agreements.terminate');

    // Maintenance Requests Management
    // Route::prefix('maintenance')->name('maintenance.')->group(function () {
    //     Route::get('/requests', [MaintenanceRequestController::class, 'index'])->name('requests');
    //     Route::get('/schedule', [MaintenanceRequestController::class, 'schedule'])->name('schedule');
    //     Route::get('/history', [MaintenanceRequestController::class, 'history'])->name('history');
    //     Route::get('/requests/create', [MaintenanceRequestController::class, 'create'])->name('create');
    //     Route::post('/requests', [MaintenanceRequestController::class, 'store'])->name('store');
    //     Route::get('/requests/{request}', [MaintenanceRequestController::class, 'show'])->name('show');
    //     Route::get('/requests/{request}/edit', [MaintenanceRequestController::class, 'edit'])->name('edit');
    //     Route::put('/requests/{request}', [MaintenanceRequestController::class, 'update'])->name('update');
    //     Route::delete('/requests/{request}', [MaintenanceRequestController::class, 'destroy'])->name('destroy');
    //     Route::post('/requests/{request}/assign', [MaintenanceRequestController::class, 'assign'])->name('assign');
    //     Route::post('/requests/{request}/complete', [MaintenanceRequestController::class, 'complete'])->name('complete');
    // });

    // Enhanced User Management Routes
    Route::prefix('users-management')->name('users-management.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UsersController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\UsersController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\UsersController::class, 'store'])->name('store');
        Route::get('/{user}', [App\Http\Controllers\Admin\UsersController::class, 'show'])->name('show');
        Route::get('/{user}/edit', [App\Http\Controllers\Admin\UsersController::class, 'edit'])->name('edit');
        Route::put('/{user}', [App\Http\Controllers\Admin\UsersController::class, 'update'])->name('update');
        Route::delete('/{user}', [App\Http\Controllers\Admin\UsersController::class, 'destroy'])->name('destroy');
        Route::post('/{user}/toggle-status', [App\Http\Controllers\Admin\UsersController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{user}/assign-role', [App\Http\Controllers\Admin\UsersController::class, 'assignRole'])->name('assign-role');
        Route::delete('/{user}/remove-role/{role}', [App\Http\Controllers\Admin\UsersController::class, 'removeRole'])->name('remove-role');
    });

    // User Activity Routes
    Route::prefix('user-activity')->name('user-activity.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\ActivityLogController::class, 'index'])->name('index');
        Route::get('/{user}', [App\Http\Controllers\Admin\ActivityLogController::class, 'show'])->name('show');
    });

    // Role Management Routes
    Route::prefix('user-roles')->name('user-roles.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\UserRoleController::class, 'index'])->name('index');
        Route::post('/assign', [App\Http\Controllers\Admin\UserRoleController::class, 'assign'])->name('assign');
        Route::delete('/remove', [App\Http\Controllers\Admin\UserRoleController::class, 'remove'])->name('remove');
    });
});
