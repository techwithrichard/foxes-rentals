<?php

use App\Http\Controllers\MpesaPaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::any('/callback/confirmation', [MpesaPaymentController::class, 'confirmation']);
Route::any('/callback/validation', [MpesaPaymentController::class, 'validation']);
Route::any('/callback/stk_callback', [MpesaPaymentController::class, 'stk'])->name('stk_callback');
