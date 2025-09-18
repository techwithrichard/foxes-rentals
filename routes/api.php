<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserManagementApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User Management API Routes
Route::middleware(['auth:sanctum', 'role_or_permission:super_admin|admin'])
    ->prefix('users')
    ->name('api.users.')
    ->group(function () {
        
        // Basic CRUD operations
        Route::get('/', [UserManagementApiController::class, 'index'])->name('index');
        Route::post('/', [UserManagementApiController::class, 'store'])->name('store');
        Route::get('/{id}', [UserManagementApiController::class, 'show'])->name('show');
        Route::put('/{id}', [UserManagementApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [UserManagementApiController::class, 'destroy'])->name('destroy');
        
        // User management operations
        Route::post('/{id}/toggle-status', [UserManagementApiController::class, 'toggleStatus'])->name('toggle-status');
        Route::post('/{id}/reset-password', [UserManagementApiController::class, 'resetPassword'])->name('reset-password');
        
        // Permission management
        Route::get('/{id}/permissions', [UserManagementApiController::class, 'permissions'])->name('permissions');
        Route::post('/{id}/permissions', [UserManagementApiController::class, 'assignPermission'])->name('assign-permission');
        Route::delete('/{id}/permissions', [UserManagementApiController::class, 'removePermission'])->name('remove-permission');
        
        // Role management
        Route::post('/{id}/roles', [UserManagementApiController::class, 'assignRole'])->name('assign-role');
        Route::delete('/{id}/roles', [UserManagementApiController::class, 'removeRole'])->name('remove-role');
        
        // Activity tracking
        Route::get('/{id}/activities', [UserManagementApiController::class, 'activities'])->name('activities');
        
        // Bulk operations
        Route::post('/bulk-action', [UserManagementApiController::class, 'bulkAction'])->name('bulk-action');
        
        // Data export
        Route::get('/export/data', [UserManagementApiController::class, 'export'])->name('export');
        
        // Statistics
        Route::get('/statistics/overview', [UserManagementApiController::class, 'statistics'])->name('statistics');
        Route::get('/statistics/password-security', [UserManagementApiController::class, 'passwordSecurity'])->name('password-security');
        Route::get('/statistics/expired-passwords', [UserManagementApiController::class, 'expiredPasswords'])->name('expired-passwords');
        Route::get('/statistics/expiring-passwords', [UserManagementApiController::class, 'expiringPasswords'])->name('expiring-passwords');
    });

// Public API routes (no authentication required)
Route::prefix('public')
    ->name('api.public.')
    ->group(function () {
        // Health check
        Route::get('/health', function () {
            return response()->json([
                'status' => 'healthy',
                'timestamp' => now()->toISOString(),
                'version' => config('app.version', '1.0.0'),
            ]);
        })->name('health');
        
        // System information
        Route::get('/info', function () {
            return response()->json([
                'name' => config('app.name'),
                'version' => config('app.version', '1.0.0'),
                'environment' => config('app.env'),
                'debug' => config('app.debug'),
            ]);
        })->name('info');
    });