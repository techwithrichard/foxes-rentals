<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyApiController;
use App\Http\Controllers\Api\TenantPortalController;
use App\Http\Controllers\Api\MaintenanceApiController;
use App\Http\Controllers\Api\CommunicationApiController;
use App\Http\Controllers\Api\WorkflowApiController;
use App\Http\Controllers\Api\BusinessIntelligenceApiController;

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

    // Maintenance Management Routes
    Route::prefix('maintenance')->group(function () {
        Route::post('/schedule-preventive/{propertyId}', [MaintenanceApiController::class, 'schedulePreventiveMaintenance'])
            ->name('api.maintenance.schedule-preventive');
        Route::post('/assign-vendor/{requestId}', [MaintenanceApiController::class, 'assignVendor'])
            ->name('api.maintenance.assign-vendor');
        Route::get('/costs/{propertyId}', [MaintenanceApiController::class, 'getMaintenanceCosts'])
            ->name('api.maintenance.costs');
        Route::get('/reports/{propertyId}', [MaintenanceApiController::class, 'generateReports'])
            ->name('api.maintenance.reports');
        Route::get('/vendor-performance/{propertyId}', [MaintenanceApiController::class, 'getVendorPerformance'])
            ->name('api.maintenance.vendor-performance');
        Route::get('/trends/{propertyId}', [MaintenanceApiController::class, 'getMaintenanceTrends'])
            ->name('api.maintenance.trends');
        Route::get('/upcoming/{propertyId}', [MaintenanceApiController::class, 'getUpcomingMaintenance'])
            ->name('api.maintenance.upcoming');
        Route::get('/efficiency/{propertyId}', [MaintenanceApiController::class, 'getEfficiencyMetrics'])
            ->name('api.maintenance.efficiency');
    });

    // Communication Routes
    Route::prefix('communication')->group(function () {
        Route::post('/send-notification', [CommunicationApiController::class, 'sendNotification'])
            ->name('api.communication.send-notification');
        Route::post('/payment-reminders', [CommunicationApiController::class, 'sendPaymentReminders'])
            ->name('api.communication.payment-reminders');
        Route::post('/lease-renewal-notifications', [CommunicationApiController::class, 'sendLeaseRenewalNotifications'])
            ->name('api.communication.lease-renewal-notifications');
        Route::post('/maintenance-updates/{maintenanceRequestId}', [CommunicationApiController::class, 'sendMaintenanceUpdates'])
            ->name('api.communication.maintenance-updates');
        Route::post('/property-announcement/{propertyId}', [CommunicationApiController::class, 'sendPropertyAnnouncement'])
            ->name('api.communication.property-announcement');
        Route::get('/analytics', [CommunicationApiController::class, 'getCommunicationAnalytics'])
            ->name('api.communication.analytics');
    });

    // Workflow Automation Routes
    Route::prefix('workflow')->group(function () {
        Route::post('/lease-renewal/{leaseId}', [WorkflowApiController::class, 'processLeaseRenewal'])
            ->name('api.workflow.lease-renewal');
        Route::post('/payment-reminders', [WorkflowApiController::class, 'handlePaymentReminders'])
            ->name('api.workflow.payment-reminders');
        Route::post('/maintenance-requests', [WorkflowApiController::class, 'processMaintenanceRequests'])
            ->name('api.workflow.maintenance-requests');
        Route::post('/monthly-reports', [WorkflowApiController::class, 'generateMonthlyReports'])
            ->name('api.workflow.monthly-reports');
    });

    // Business Intelligence Routes
    Route::prefix('business-intelligence')->group(function () {
        Route::get('/dashboard', [BusinessIntelligenceApiController::class, 'getDashboardData'])
            ->name('api.bi.dashboard');
        Route::get('/overview', [BusinessIntelligenceApiController::class, 'getOverviewMetrics'])
            ->name('api.bi.overview');
        Route::get('/financial-performance', [BusinessIntelligenceApiController::class, 'getFinancialPerformance'])
            ->name('api.bi.financial-performance');
        Route::get('/property-performance', [BusinessIntelligenceApiController::class, 'getPropertyPerformance'])
            ->name('api.bi.property-performance');
        Route::get('/tenant-analytics', [BusinessIntelligenceApiController::class, 'getTenantAnalytics'])
            ->name('api.bi.tenant-analytics');
        Route::get('/maintenance-insights', [BusinessIntelligenceApiController::class, 'getMaintenanceInsights'])
            ->name('api.bi.maintenance-insights');
        Route::get('/market-analysis', [BusinessIntelligenceApiController::class, 'getMarketAnalysis'])
            ->name('api.bi.market-analysis');
        Route::get('/predictive-analytics', [BusinessIntelligenceApiController::class, 'getPredictiveAnalytics'])
            ->name('api.bi.predictive-analytics');
        Route::get('/recommendations', [BusinessIntelligenceApiController::class, 'getRecommendations'])
            ->name('api.bi.recommendations');
    });

    // Security and Audit Routes
    Route::prefix('security')->group(function () {
        Route::get('/audit-report', [PropertyApiController::class, 'securityAuditReport'])
            ->name('api.security.audit-report');
        Route::get('/suspicious-activity/{userId}', [PropertyApiController::class, 'suspiciousActivity'])
            ->name('api.security.suspicious-activity');
    });
});
