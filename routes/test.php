<?php

use App\Http\Controllers\Test\PropertyConsolidatedController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Test Routes
|--------------------------------------------------------------------------
|
| These routes are for testing the new consolidated property system
| during Phase 1 implementation. They should be removed after migration
| is complete and the system is fully switched to the new structure.
|
*/

Route::prefix('test')->name('test.')->group(function () {
    // Consolidated Property Test Routes
    Route::prefix('properties-consolidated')->name('properties-consolidated.')->group(function () {
        Route::get('/', [PropertyConsolidatedController::class, 'index'])->name('index');
        Route::get('/statistics', [PropertyConsolidatedController::class, 'statistics'])->name('statistics');
        Route::get('/{propertyConsolidated}', [PropertyConsolidatedController::class, 'show'])->name('show');
        Route::get('/{propertyConsolidated}/relationships', [PropertyConsolidatedController::class, 'testRelationships'])->name('relationships');
        Route::post('/', [PropertyConsolidatedController::class, 'store'])->name('store');
        Route::put('/{propertyConsolidated}', [PropertyConsolidatedController::class, 'update'])->name('update');
    });
});
