<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyApiController;
use App\Http\Controllers\Api\TenantPortalController;

/*
|--------------------------------------------------------------------------
| Enhanced API Routes
|--------------------------------------------------------------------------
|
| These routes provide enhanced functionality for the property management
| system including financial analytics, tenant portal, and security features.
|
*/

Route::middleware(['auth:sanctum'])->group(function () {
    
    // Enhanced Property API Routes
    Route::prefix('properties')->group(function () {
        
        // Financial Analytics Routes
        Route::get('/{propertyId}/financial-analytics', [PropertyApiController::class, 'financialAnalytics'])
            ->name('api.properties.financial-analytics');
        Route::get('/{propertyId}/roi-analysis', [PropertyApiController::class, 'roiAnalysis'])
            ->name('api.properties.roi-analysis');
        Route::get('/{propertyId}/occupancy-analytics', [PropertyApiController::class, 'occupancyAnalytics'])
            ->name('api.properties.occupancy-analytics');
        Route::get('/{propertyId}/maintenance-costs', [PropertyApiController::class, 'maintenanceCosts'])
            ->name('api.properties.maintenance-costs');
        Route::get('/{propertyId}/market-comparison', [PropertyApiController::class, 'marketComparison'])
            ->name('api.properties.market-comparison');
        Route::get('/{propertyId}/revenue-trends', [PropertyApiController::class, 'revenueTrends'])
            ->name('api.properties.revenue-trends');
        Route::get('/{propertyId}/dashboard-data', [PropertyApiController::class, 'dashboardData'])
            ->name('api.properties.dashboard-data');
        
        // Portfolio Analytics
        Route::get('/portfolio/analytics', [PropertyApiController::class, 'portfolioAnalytics'])
            ->name('api.properties.portfolio-analytics');
    });

    // Tenant Portal Routes
    Route::prefix('tenant-portal')->group(function () {
        Route::get('/dashboard/{tenantId}', [TenantPortalController::class, 'dashboard'])
            ->name('api.tenant-portal.dashboard');
        Route::get('/leases/{tenantId}', [TenantPortalController::class, 'leases'])
            ->name('api.tenant-portal.leases');
        Route::get('/payments/{tenantId}', [TenantPortalController::class, 'payments'])
            ->name('api.tenant-portal.payments');
        Route::get('/maintenance-requests/{tenantId}', [TenantPortalController::class, 'maintenanceRequests'])
            ->name('api.tenant-portal.maintenance-requests');
        Route::post('/maintenance-requests/{tenantId}', [TenantPortalController::class, 'submitMaintenanceRequest'])
            ->name('api.tenant-portal.submit-maintenance-request');
        Route::get('/documents/{tenantId}', [TenantPortalController::class, 'documents'])
            ->name('api.tenant-portal.documents');
        Route::get('/notifications/{tenantId}', [TenantPortalController::class, 'notifications'])
            ->name('api.tenant-portal.notifications');
        Route::get('/financial-summary/{tenantId}', [TenantPortalController::class, 'financialSummary'])
            ->name('api.tenant-portal.financial-summary');
        Route::put('/profile/{tenantId}', [TenantPortalController::class, 'updateProfile'])
            ->name('api.tenant-portal.update-profile');
    });

    // Security and Audit Routes
    Route::prefix('security')->group(function () {
        Route::get('/audit-report', [PropertyApiController::class, 'securityAuditReport'])
            ->name('api.security.audit-report');
        Route::get('/suspicious-activity/{userId}', [PropertyApiController::class, 'suspiciousActivity'])
            ->name('api.security.suspicious-activity');
    });
});
