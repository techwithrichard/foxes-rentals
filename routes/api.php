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

// Property Management API Routes
Route::middleware(['auth:sanctum'])
    ->prefix('properties')
    ->name('api.properties.')
    ->group(function () {
        Route::get('/', [App\Http\Controllers\Api\PropertyApiController::class, 'index'])->name('index');
        Route::post('/', [App\Http\Controllers\Api\PropertyApiController::class, 'store'])->name('store');
        Route::get('/statistics', [App\Http\Controllers\Api\PropertyApiController::class, 'statistics'])->name('statistics');
        Route::get('/{id}', [App\Http\Controllers\Api\PropertyApiController::class, 'show'])->name('show');
        Route::put('/{id}', [App\Http\Controllers\Api\PropertyApiController::class, 'update'])->name('update');
        Route::delete('/{id}', [App\Http\Controllers\Api\PropertyApiController::class, 'destroy'])->name('destroy');
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

// Mobile API Routes
Route::prefix('mobile')
    ->name('api.mobile.')
    ->group(function () {
        // Authentication
        Route::post('/auth/login', function () {
            return response()->json(['message' => 'Mobile login endpoint']);
        })->name('auth.login');
        
        Route::post('/auth/logout', function () {
            return response()->json(['message' => 'Mobile logout endpoint']);
        })->name('auth.logout');
        
        // Protected mobile routes
        Route::middleware('auth:sanctum')->group(function () {
            // Dashboard
            Route::get('/dashboard', function () {
                return response()->json(['message' => 'Mobile dashboard endpoint']);
            })->name('dashboard');
            
            // Properties
            Route::get('/properties', function () {
                return response()->json(['message' => 'Mobile properties endpoint']);
            })->name('properties.index');
            
            Route::get('/properties/{id}', function ($id) {
                return response()->json(['message' => "Mobile property {$id} endpoint"]);
            })->name('properties.show');
            
            // Payments
            Route::get('/payments', function () {
                return response()->json(['message' => 'Mobile payments endpoint']);
            })->name('payments.index');
            
            Route::post('/payments', function () {
                return response()->json(['message' => 'Mobile payment creation endpoint']);
            })->name('payments.store');
            
            // Profile
            Route::get('/profile', function () {
                return response()->json(['message' => 'Mobile profile endpoint']);
            })->name('profile.show');
            
            Route::put('/profile', function () {
                return response()->json(['message' => 'Mobile profile update endpoint']);
            })->name('profile.update');
            
            // Notifications
            Route::get('/notifications', function () {
                return response()->json(['message' => 'Mobile notifications endpoint']);
            })->name('notifications.index');
        });
        
        // App settings
        Route::get('/settings', function () {
            return response()->json([
                'app_version' => '1.0.0',
                'min_android_version' => '7.0',
                'min_ios_version' => '12.0',
                'features' => [
                    'property_search' => true,
                    'payment_processing' => true,
                    'document_upload' => true,
                    'push_notifications' => true
                ]
            ]);
        })->name('settings');
    });