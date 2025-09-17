<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LandlordPortalController;
use App\Http\Controllers\TenantPortalController;
use App\Http\Controllers\MaintainerPortalController;
use App\Http\Controllers\AccountantPortalController;

/*
|--------------------------------------------------------------------------
| Portal Routes
|--------------------------------------------------------------------------
|
| These routes handle different user portals based on their roles
|
*/

// Main dashboard redirect
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

// Landlord Portal Routes
Route::middleware(['auth', 'role:landlord'])->prefix('landlord')->name('landlord.')->group(function () {
    Route::get('/', [LandlordPortalController::class, 'index'])->name('dashboard');
    Route::get('/properties', [LandlordPortalController::class, 'properties'])->name('properties');
    Route::get('/tenants', [LandlordPortalController::class, 'tenants'])->name('tenants');
    Route::get('/payments', [LandlordPortalController::class, 'payments'])->name('payments');
    Route::get('/reports', [LandlordPortalController::class, 'reports'])->name('reports');
});

// Tenant Portal Routes
Route::middleware(['auth', 'role:tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::get('/', [TenantPortalController::class, 'index'])->name('dashboard');
    Route::get('/lease', [TenantPortalController::class, 'lease'])->name('lease');
    Route::get('/payments', [TenantPortalController::class, 'payments'])->name('payments');
    Route::get('/property', [TenantPortalController::class, 'property'])->name('property');
    Route::get('/maintenance', [TenantPortalController::class, 'maintenance'])->name('maintenance');
    Route::get('/profile', [TenantPortalController::class, 'profile'])->name('profile');
    Route::put('/profile', [TenantPortalController::class, 'updateProfile'])->name('profile.update');
});

// Maintainer Portal Routes
Route::middleware(['auth', 'role:maintainer'])->prefix('maintainer')->name('maintainer.')->group(function () {
    Route::get('/', [MaintainerPortalController::class, 'index'])->name('dashboard');
    Route::get('/requests', [MaintainerPortalController::class, 'requests'])->name('requests');
    Route::get('/assigned', [MaintainerPortalController::class, 'assigned'])->name('assigned');
    Route::get('/requests/{request}', [MaintainerPortalController::class, 'show'])->name('requests.show');
    Route::put('/requests/{request}/status', [MaintainerPortalController::class, 'updateStatus'])->name('requests.status');
    Route::post('/requests/{request}/assign', [MaintainerPortalController::class, 'assignToSelf'])->name('requests.assign');
    Route::get('/schedule', [MaintainerPortalController::class, 'schedule'])->name('schedule');
    Route::get('/profile', [MaintainerPortalController::class, 'profile'])->name('profile');
    Route::put('/profile', [MaintainerPortalController::class, 'updateProfile'])->name('profile.update');
});

// Accountant Portal Routes
Route::middleware(['auth', 'role:accountant'])->prefix('accountant')->name('accountant.')->group(function () {
    Route::get('/', [AccountantPortalController::class, 'index'])->name('dashboard');
    Route::get('/payments', [AccountantPortalController::class, 'payments'])->name('payments');
    Route::get('/invoices', [AccountantPortalController::class, 'invoices'])->name('invoices');
    Route::get('/expenses', [AccountantPortalController::class, 'expenses'])->name('expenses');
    Route::get('/reports', [AccountantPortalController::class, 'reports'])->name('reports');
    Route::get('/reconciliation', [AccountantPortalController::class, 'reconciliation'])->name('reconciliation');
    Route::put('/payments/{payment}/reconcile', [AccountantPortalController::class, 'reconcile'])->name('payments.reconcile');
    Route::get('/profile', [AccountantPortalController::class, 'profile'])->name('profile');
    Route::put('/profile', [AccountantPortalController::class, 'updateProfile'])->name('profile.update');
});

// Property Manager Portal Routes (can be extended)
Route::middleware(['auth', 'role:property_manager'])->prefix('property-manager')->name('property-manager.')->group(function () {
    Route::get('/', function () {
        return view('portals.property-manager.dashboard');
    })->name('dashboard');
});

// Agent Portal Routes (can be extended)
Route::middleware(['auth', 'role:agent'])->prefix('agent')->name('agent.')->group(function () {
    Route::get('/', function () {
        return view('portals.agent.dashboard');
    })->name('dashboard');
});
