<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdvancedAnalyticsService;
use App\Services\ReportingService;
use App\Models\ReportTemplate;
use App\Models\ScheduledReport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class AnalyticsDashboardController extends Controller
{
    protected $analyticsService;
    protected $reportingService;

    public function __construct(
        AdvancedAnalyticsService $analyticsService,
        ReportingService $reportingService
    ) {
        $this->middleware('permission:view_analytics');
        $this->analyticsService = $analyticsService;
        $this->reportingService = $reportingService;
    }

    /**
     * Display the analytics dashboard
     */
    public function index()
    {
        try {
            $dashboardData = $this->analyticsService->getDashboardAnalytics();
            $reportTemplates = $this->reportingService->getReportTemplates();
            $scheduledReports = $this->reportingService->getScheduledReports();
            
            return view('admin.settings.analytics.index', compact(
                'dashboardData',
                'reportTemplates',
                'scheduledReports'
            ));

        } catch (\Exception $e) {
            Log::error("Error loading analytics dashboard: " . $e->getMessage());
            
            return view('admin.settings.analytics.index')->with('error', 'Failed to load analytics dashboard.');
        }
    }

    /**
     * Get dashboard analytics data
     */
    public function getDashboardData(): JsonResponse
    {
        try {
            $dashboardData = $this->analyticsService->getDashboardAnalytics();
            
            return response()->json([
                'success' => true,
                'data' => $dashboardData
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting dashboard data: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get dashboard data'
            ], 500);
        }
    }

    /**
     * Get property analytics
     */
    public function getPropertyAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'property_type', 'location']);
            $analytics = $this->analyticsService->getPropertyAnalytics();
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting property analytics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get property analytics'
            ], 500);
        }
    }

    /**
     * Get financial analytics
     */
    public function getFinancialAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'property_id']);
            $analytics = $this->analyticsService->getFinancialAnalytics();
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting financial analytics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get financial analytics'
            ], 500);
        }
    }

    /**
     * Get tenant analytics
     */
    public function getTenantAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'tenant_id']);
            $analytics = $this->analyticsService->getTenantAnalytics();
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting tenant analytics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get tenant analytics'
            ], 500);
        }
    }

    /**
     * Get maintenance analytics
     */
    public function getMaintenanceAnalytics(Request $request): JsonResponse
    {
        try {
            $filters = $request->only(['date_from', 'date_to', 'category', 'priority']);
            $analytics = $this->analyticsService->getMaintenanceAnalytics();
            
            return response()->json([
                'success' => true,
                'data' => $analytics
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting maintenance analytics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get maintenance analytics'
            ], 500);
        }
    }

    /**
     * Get trends data
     */
    public function getTrendsData(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', '12_months');
            $trends = $this->analyticsService->getTrendsData();
            
            return response()->json([
                'success' => true,
                'data' => $trends
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting trends data: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get trends data'
            ], 500);
        }
    }

    /**
     * Get comparative analytics
     */
    public function getComparativeAnalytics(Request $request): JsonResponse
    {
        try {
            $period = $request->get('period', 'month');
            $comparative = $this->analyticsService->getComparativeAnalytics($period);
            
            return response()->json([
                'success' => true,
                'data' => $comparative
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting comparative analytics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get comparative analytics'
            ], 500);
        }
    }

    /**
     * Get predictive analytics
     */
    public function getPredictiveAnalytics(): JsonResponse
    {
        try {
            $predictive = $this->analyticsService->getPredictiveAnalytics();
            
            return response()->json([
                'success' => true,
                'data' => $predictive
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting predictive analytics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get predictive analytics'
            ], 500);
        }
    }

    /**
     * Generate report
     */
    public function generateReport(Request $request): JsonResponse
    {
        try {
            $reportType = $request->input('report_type');
            $filters = $request->input('filters', []);
            $format = $request->input('format', 'json');
            
            $reportData = $this->reportingService->generateReportByType($reportType, $filters);
            
            if ($format !== 'json') {
                $filePath = $this->reportingService->exportReport($reportData, $format);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Report generated successfully',
                    'file_path' => $filePath,
                    'download_url' => route('admin.settings.analytics.download-report', ['path' => $filePath])
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => $reportData
            ]);

        } catch (\Exception $e) {
            Log::error("Error generating report: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate report'
            ], 500);
        }
    }

    /**
     * Generate custom report using template
     */
    public function generateCustomReport(Request $request, ReportTemplate $template): JsonResponse
    {
        try {
            $filters = $request->input('filters', []);
            $format = $request->input('format', 'json');
            
            $reportData = $this->reportingService->generateCustomReport($template, $filters);
            
            if ($format !== 'json') {
                $filePath = $this->reportingService->exportReport($reportData, $format);
                
                return response()->json([
                    'success' => true,
                    'message' => 'Custom report generated successfully',
                    'file_path' => $filePath,
                    'download_url' => route('admin.settings.analytics.download-report', ['path' => $filePath])
                ]);
            }
            
            return response()->json([
                'success' => true,
                'data' => $reportData
            ]);

        } catch (\Exception $e) {
            Log::error("Error generating custom report: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate custom report'
            ], 500);
        }
    }

    /**
     * Schedule report
     */
    public function scheduleReport(Request $request): JsonResponse
    {
        try {
            $reportConfig = $request->validate([
                'name' => 'required|string|max:255',
                'report_type' => 'required|string',
                'template_id' => 'nullable|exists:report_templates,id',
                'filters' => 'nullable|array',
                'frequency' => 'required|in:daily,weekly,monthly,quarterly,yearly',
                'time' => 'required|date_format:H:i',
                'recipients' => 'nullable|array',
                'format' => 'required|in:pdf,excel,csv,json'
            ]);
            
            $scheduledReport = $this->reportingService->scheduleReport($reportConfig);
            
            return response()->json([
                'success' => true,
                'message' => 'Report scheduled successfully',
                'data' => $scheduledReport
            ]);

        } catch (\Exception $e) {
            Log::error("Error scheduling report: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to schedule report'
            ], 500);
        }
    }

    /**
     * Get report templates
     */
    public function getReportTemplates(): JsonResponse
    {
        try {
            $templates = $this->reportingService->getReportTemplates();
            
            return response()->json([
                'success' => true,
                'data' => $templates
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting report templates: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get report templates'
            ], 500);
        }
    }

    /**
     * Create report template
     */
    public function createReportTemplate(Request $request): JsonResponse
    {
        try {
            $templateData = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'category' => 'required|string',
                'report_type' => 'required|string',
                'sections' => 'required|array',
                'filters' => 'nullable|array',
                'layout' => 'nullable|array',
                'is_public' => 'boolean'
            ]);
            
            $template = $this->reportingService->createReportTemplate($templateData);
            
            return response()->json([
                'success' => true,
                'message' => 'Report template created successfully',
                'data' => $template
            ]);

        } catch (\Exception $e) {
            Log::error("Error creating report template: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create report template'
            ], 500);
        }
    }

    /**
     * Get scheduled reports
     */
    public function getScheduledReports(): JsonResponse
    {
        try {
            $scheduledReports = $this->reportingService->getScheduledReports();
            
            return response()->json([
                'success' => true,
                'data' => $scheduledReports
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting scheduled reports: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get scheduled reports'
            ], 500);
        }
    }

    /**
     * Update scheduled report
     */
    public function updateScheduledReport(Request $request, ScheduledReport $scheduledReport): JsonResponse
    {
        try {
            $updateData = $request->validate([
                'name' => 'sometimes|string|max:255',
                'filters' => 'sometimes|array',
                'frequency' => 'sometimes|in:daily,weekly,monthly,quarterly,yearly',
                'time' => 'sometimes|date_format:H:i',
                'recipients' => 'sometimes|array',
                'format' => 'sometimes|in:pdf,excel,csv,json',
                'is_active' => 'sometimes|boolean'
            ]);
            
            $scheduledReport->update($updateData);
            
            // Update next run time if schedule changed
            if (isset($updateData['frequency']) || isset($updateData['time'])) {
                $scheduledReport->updateNextRunTime();
            }
            
            return response()->json([
                'success' => true,
                'message' => 'Scheduled report updated successfully',
                'data' => $scheduledReport
            ]);

        } catch (\Exception $e) {
            Log::error("Error updating scheduled report: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update scheduled report'
            ], 500);
        }
    }

    /**
     * Delete scheduled report
     */
    public function deleteScheduledReport(ScheduledReport $scheduledReport): JsonResponse
    {
        try {
            $scheduledReport->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Scheduled report deleted successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Error deleting scheduled report: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete scheduled report'
            ], 500);
        }
    }

    /**
     * Execute scheduled reports
     */
    public function executeScheduledReports(): JsonResponse
    {
        try {
            $results = $this->reportingService->executeScheduledReports();
            
            return response()->json([
                'success' => true,
                'message' => 'Scheduled reports executed',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error("Error executing scheduled reports: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to execute scheduled reports'
            ], 500);
        }
    }

    /**
     * Download generated report
     */
    public function downloadReport(string $path)
    {
        try {
            if (!\Storage::exists($path)) {
                abort(404, 'Report file not found');
            }
            
            return \Storage::download($path);
        } catch (\Exception $e) {
            Log::error("Error downloading report: " . $e->getMessage());
            abort(500, 'Failed to download report');
        }
    }

    /**
     * Clear analytics cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->analyticsService->clearCache();
            $this->reportingService->clearCache();
            
            return response()->json([
                'success' => true,
                'message' => 'Analytics cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Error clearing analytics cache: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear analytics cache'
            ], 500);
        }
    }

    /**
     * Generate report by type helper
     */
    protected function generateReportByType(string $type, array $filters): array
    {
        switch ($type) {
            case 'property':
                return $this->reportingService->generatePropertyReport($filters);
            case 'financial':
                return $this->reportingService->generateFinancialReport($filters);
            case 'tenant':
                return $this->reportingService->generateTenantReport($filters);
            case 'maintenance':
                return $this->reportingService->generateMaintenanceReport($filters);
            case 'occupancy':
                return $this->reportingService->generateOccupancyReport($filters);
            default:
                throw new \InvalidArgumentException("Unknown report type: {$type}");
        }
    }
}
