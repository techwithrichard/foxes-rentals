<?php

use App\Http\Controllers\Tenant\HomeController;
use App\Http\Controllers\Tenant\InvoicesController;
use App\Http\Controllers\Tenant\PaymentsController;
use App\Http\Controllers\Tenant\PaypalPaymentController;
use App\Http\Controllers\Tenant\ProfileController;
use App\Http\Controllers\Tenant\SupportTicketController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])
    ->name('tenant.')
    ->prefix('portal')
    ->middleware(['auth', 'role:tenant'])
    ->group(function () {
        Route::get('/', [HomeController::class, 'index'])
            ->name('home');
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
        Route::resource('invoices', InvoicesController::class);
        Route::resource('payments', PaymentsController::class);
        Route::resource('support-tickets', SupportTicketController::class);

        //pay via mpesa route
        Route::get('/initiate_mpesa_payment/{id}', [HomeController::class, 'initiateMpesaPayment'])
            ->name('initiate_mpesa_payment');

    });

Route::get('/initiate_paypal_payment/{id}', [PaypalPaymentController::class, 'pay'])
    ->name('tenant.initiate_paypal_payment');

Route::get('/paypal_payment_success/{id}', [PaypalPaymentController::class, 'success'])
    ->name('tenant.paypal_payment_success');

Route::get('/paypal_payment_cancel', [PaypalPaymentController::class, 'cancelUrl'])
    ->name('tenant.paypal_payment_cancel');


