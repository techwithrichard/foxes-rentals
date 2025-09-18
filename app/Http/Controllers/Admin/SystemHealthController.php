<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemMetric;
use App\Models\SystemAlert;
use App\Services\SystemHealthMonitoringService;
use App\Services\PerformanceOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class SystemHealthController extends Controller
{
    protected $healthService;
    protected $performanceService;

    public function __construct(
        SystemHealthMonitoringService $healthService,
        PerformanceOptimizationService $performanceService
    ) {
        $this->middleware('permission:view_system_health');
        $this->healthService = $healthService;
        $this->performanceService = $performanceService;
    }

    /**
     * Display the system health dashboard
     */
    public function index()
    {
        try {
            $systemHealth = $this->healthService->getSystemHealth();
            $performanceMetrics = $this->performanceService->getPerformanceMetrics();
            
            // Get recent alerts
            $recentAlerts = SystemAlert::active()
                ->with(['creator', 'acknowledger', 'resolver'])
                ->latest()
                ->limit(10)
                ->get();

            // Get system metrics for charts
            $metricsData = $this->getMetricsData();

            return view('admin.settings.system-health.index', compact(
                'systemHealth',
                'performanceMetrics',
                'recentAlerts',
                'metricsData'
            ));

        } catch (\Exception $e) {
            Log::error("Error loading system health dashboard: " . $e->getMessage());
            
            return view('admin.settings.system-health.index')->with('error', 'Failed to load system health dashboard.');
        }
    }

    /**
     * Get system health data via API
     */
    public function getSystemHealth(): JsonResponse
    {
        try {
            $systemHealth = $this->healthService->getSystemHealth();
            
            return response()->json([
                'success' => true,
                'data' => $systemHealth
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting system health: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system health data'
            ], 500);
        }
    }

    /**
     * Get performance metrics
     */
    public function getPerformanceMetrics(): JsonResponse
    {
        try {
            $metrics = $this->performanceService->getPerformanceMetrics();
            
            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting performance metrics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get performance metrics'
            ], 500);
        }
    }

    /**
     * Get system alerts
     */
    public function getAlerts(Request $request): JsonResponse
    {
        try {
            $query = SystemAlert::with(['creator', 'acknowledger', 'resolver']);

            // Apply filters
            if ($request->filled('status')) {
                $query->where('status', $request->status);
            }

            if ($request->filled('severity')) {
                $query->where('severity', $request->severity);
            }

            if ($request->filled('type')) {
                $query->where('alert_type', $request->type);
            }

            if ($request->filled('date_from')) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->filled('date_to')) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            $alerts = $query->latest()->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $alerts
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting system alerts: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system alerts'
            ], 500);
        }
    }

    /**
     * Get system metrics
     */
    public function getMetrics(Request $request): JsonResponse
    {
        try {
            $query = SystemMetric::query();

            // Apply filters
            if ($request->filled('type')) {
                $query->where('metric_type', $request->type);
            }

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }

            if ($request->filled('hours')) {
                $query->where('timestamp', '>=', now()->subHours($request->hours));
            }

            $metrics = $query->orderBy('timestamp', 'desc')->paginate(50);

            return response()->json([
                'success' => true,
                'data' => $metrics
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting system metrics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system metrics'
            ], 500);
        }
    }

    /**
     * Acknowledge an alert
     */
    public function acknowledgeAlert(SystemAlert $alert): JsonResponse
    {
        try {
            if ($alert->acknowledge()) {
                Log::info("Alert acknowledged: {$alert->id} by user " . auth()->id());
                
                return response()->json([
                    'success' => true,
                    'message' => 'Alert acknowledged successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Alert cannot be acknowledged'
            ], 400);

        } catch (\Exception $e) {
            Log::error("Error acknowledging alert: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to acknowledge alert'
            ], 500);
        }
    }

    /**
     * Resolve an alert
     */
    public function resolveAlert(SystemAlert $alert): JsonResponse
    {
        try {
            if ($alert->resolve()) {
                Log::info("Alert resolved: {$alert->id} by user " . auth()->id());
                
                return response()->json([
                    'success' => true,
                    'message' => 'Alert resolved successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Alert cannot be resolved'
            ], 400);

        } catch (\Exception $e) {
            Log::error("Error resolving alert: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve alert'
            ], 500);
        }
    }

    /**
     * Suppress an alert
     */
    public function suppressAlert(SystemAlert $alert): JsonResponse
    {
        try {
            if ($alert->suppress()) {
                Log::info("Alert suppressed: {$alert->id} by user " . auth()->id());
                
                return response()->json([
                    'success' => true,
                    'message' => 'Alert suppressed successfully'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Alert cannot be suppressed'
            ], 400);

        } catch (\Exception $e) {
            Log::error("Error suppressing alert: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to suppress alert'
            ], 500);
        }
    }

    /**
     * Bulk acknowledge alerts
     */
    public function bulkAcknowledgeAlerts(Request $request): JsonResponse
    {
        try {
            $alertIds = $request->input('alert_ids', []);
            
            if (empty($alertIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No alerts selected'
                ], 400);
            }

            $acknowledgedCount = 0;
            $alerts = SystemAlert::whereIn('id', $alertIds)->get();

            foreach ($alerts as $alert) {
                if ($alert->acknowledge()) {
                    $acknowledgedCount++;
                }
            }

            Log::info("Bulk acknowledged {$acknowledgedCount} alerts by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => "{$acknowledgedCount} alerts acknowledged successfully"
            ]);

        } catch (\Exception $e) {
            Log::error("Error bulk acknowledging alerts: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to acknowledge alerts'
            ], 500);
        }
    }

    /**
     * Bulk resolve alerts
     */
    public function bulkResolveAlerts(Request $request): JsonResponse
    {
        try {
            $alertIds = $request->input('alert_ids', []);
            
            if (empty($alertIds)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No alerts selected'
                ], 400);
            }

            $resolvedCount = 0;
            $alerts = SystemAlert::whereIn('id', $alertIds)->get();

            foreach ($alerts as $alert) {
                if ($alert->resolve()) {
                    $resolvedCount++;
                }
            }

            Log::info("Bulk resolved {$resolvedCount} alerts by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => "{$resolvedCount} alerts resolved successfully"
            ]);

        } catch (\Exception $e) {
            Log::error("Error bulk resolving alerts: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to resolve alerts'
            ], 500);
        }
    }

    /**
     * Run system optimization
     */
    public function runOptimization(Request $request): JsonResponse
    {
        try {
            $optimizationType = $request->input('type', 'comprehensive');
            $results = [];

            switch ($optimizationType) {
                case 'database':
                    $results = $this->performanceService->optimizeDatabase();
                    break;
                case 'cache':
                    $results = $this->performanceService->optimizeCache();
                    break;
                case 'storage':
                    $results = $this->performanceService->optimizeStorage();
                    break;
                case 'comprehensive':
                default:
                    $results = $this->performanceService->runComprehensiveOptimization();
                    break;
            }

            Log::info("System optimization completed: {$optimizationType} by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'System optimization completed successfully',
                'results' => $results
            ]);

        } catch (\Exception $e) {
            Log::error("Error running system optimization: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to run system optimization'
            ], 500);
        }
    }

    /**
     * Clear system health cache
     */
    public function clearCache(): JsonResponse
    {
        try {
            $this->healthService->clearCache();
            $this->performanceService->clearAllCaches();

            Log::info("System health cache cleared by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'System health cache cleared successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Error clearing system health cache: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to clear system health cache'
            ], 500);
        }
    }

    /**
     * Get system statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = [
                'alerts' => [
                    'total' => SystemAlert::count(),
                    'active' => SystemAlert::active()->count(),
                    'acknowledged' => SystemAlert::acknowledged()->count(),
                    'resolved' => SystemAlert::resolved()->count(),
                    'critical' => SystemAlert::critical()->count()
                ],
                'metrics' => [
                    'total' => SystemMetric::count(),
                    'today' => SystemMetric::whereDate('timestamp', today())->count(),
                    'this_week' => SystemMetric::where('timestamp', '>=', now()->subWeek())->count(),
                    'by_category' => SystemMetric::selectRaw('category, COUNT(*) as count')
                        ->groupBy('category')
                        ->pluck('count', 'category')
                        ->toArray()
                ],
                'system_health' => $this->healthService->getSystemHealth()['overall_status']
            ];

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting system statistics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get system statistics'
            ], 500);
        }
    }

    /**
     * Get metrics data for charts
     */
    protected function getMetricsData(): array
    {
        try {
            $last24Hours = now()->subHours(24);
            
            $metrics = SystemMetric::where('timestamp', '>=', $last24Hours)
                ->selectRaw('metric_type, AVG(value) as avg_value, DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00") as hour')
                ->groupBy('metric_type', 'hour')
                ->orderBy('hour')
                ->get();

            $chartData = [];
            foreach ($metrics as $metric) {
                if (!isset($chartData[$metric->metric_type])) {
                    $chartData[$metric->metric_type] = [
                        'labels' => [],
                        'data' => []
                    ];
                }
                
                $chartData[$metric->metric_type]['labels'][] = $metric->hour;
                $chartData[$metric->metric_type]['data'][] = round($metric->avg_value, 2);
            }

            return $chartData;

        } catch (\Exception $e) {
            Log::error("Error getting metrics data: " . $e->getMessage());
            return [];
        }
    }
}
