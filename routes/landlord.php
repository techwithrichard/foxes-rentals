<?php

use App\Http\Controllers\Landlord\ExpensesController;
use App\Http\Controllers\Landlord\HomeController;
use App\Http\Controllers\Landlord\HousesController;
use App\Http\Controllers\Landlord\InvoicesController;
use App\Http\Controllers\Landlord\PayOutsController;
use App\Http\Controllers\Landlord\ProfileController;
use App\Http\Controllers\Landlord\PropertiesController;
use App\Http\Controllers\Landlord\VouchersController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->prefix('landlord')
    ->middleware(['auth', 'role:landlord'])
    ->name('landlord.')->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('home');
        Route::resource('properties', PropertiesController::class);
        Route::resource('houses', HousesController::class);
        Route::resource('payouts', PayOutsController::class);
        Route::resource('expenses', ExpensesController::class);

        Route::controller(InvoicesController::class)->group(function () {
            Route::get('invoices', 'index')->name('invoices.index');
            Route::get('invoices/{id}/pdf', 'print')->name('invoices.print');
            Route::get('invoices/{id}', 'show')->name('invoices.show');
        });

        Route::controller(VouchersController::class)->group(function () {
            Route::get('vouchers', 'index')->name('vouchers.index');
            Route::get('vouchers/{id}/pdf', 'print')->name('vouchers.print');
            Route::get('vouchers/{id}', 'show')->name('vouchers.show');
        });

        Route::get('/notifications', [HomeController::class, 'notifications'])
            ->name('notifications');

        //group route according to controller ProfileController
        Route::controller(ProfileController::class)->group(function () {
            Route::get('/user-profile', 'index')
                ->name('profile');
            Route::get('/user-profile-activity', 'login_activities')
                ->name('login_activities');
            Route::get('/user-profile-settings', 'security_settings')
                ->name('security_settings');
        });

    });


