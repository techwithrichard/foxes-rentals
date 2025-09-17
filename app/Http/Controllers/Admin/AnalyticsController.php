<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\House;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role_or_permission:super_admin|admin']);
    }

    /**
     * Display the main analytics dashboard
     */
    public function dashboard()
    {
        abort_unless(auth()->user()->can('view reports'), 403);

        // Get comprehensive analytics data
        $analytics = [
            'user_growth' => $this->getUserGrowthData(),
            'financial_performance' => $this->getFinancialPerformanceData(),
            'property_performance' => $this->getPropertyPerformanceData(),
            'system_performance' => $this->getSystemPerformanceData(),
            'recent_activity' => $this->getRecentActivityData(),
        ];

        return view('admin.analytics.dashboard', compact('analytics'));
    }

    /**
     * Display user analytics
     */
    public function users()
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        // User growth over time
        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $userGrowth[] = [
                'month' => $date->format('M Y'),
                'users' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'landlords' => User::role('landlord')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'tenants' => User::role('tenant')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        // Role distribution
        $roleDistribution = DB::table('model_has_roles')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->select('roles.name', DB::raw('count(*) as count'))
            ->groupBy('roles.name')
            ->get();

        // User activity metrics
        $activityMetrics = [
            'total_users' => User::count(),
            'active_users' => User::whereHas('loginActivities', function($query) {
                $query->where('created_at', '>=', now()->subDays(30));
            })->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'pending_users' => User::whereNull('email_verified_at')->count(),
        ];

        return view('admin.analytics.users', compact('userGrowth', 'roleDistribution', 'activityMetrics'));
    }

    /**
     * Display financial analytics
     */
    public function financial()
    {
        abort_unless(auth()->user()->can('view financial reports'), 403);

        // Monthly revenue for the last 12 months
        $monthlyRevenue = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $revenue = Payment::where('status', 'completed')
                ->whereMonth('created_at', $date->month)
                ->whereYear('created_at', $date->year)
                ->sum('amount');
            
            $monthlyRevenue[] = [
                'month' => $date->format('M Y'),
                'revenue' => $revenue
            ];
        }

        // Financial statistics
        $financialStats = [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
            'overdue_payments' => Payment::where('status', 'overdue')->sum('amount'),
            'total_expenses' => Expense::sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
            'monthly_expenses' => Expense::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amount'),
        ];

        // Top performing properties
        $topProperties = Property::withSum('payments', 'amount')
            ->orderBy('payments_sum_amount', 'desc')
            ->limit(10)
            ->get();

        return view('admin.analytics.financial', compact('monthlyRevenue', 'financialStats', 'topProperties'));
    }

    /**
     * Display property analytics
     */
    public function properties()
    {
        abort_unless(auth()->user()->can('view property'), 403);

        // Property statistics
        $propertyStats = [
            'total_properties' => Property::count(),
            'occupied_properties' => Property::whereHas('leases', function($query) {
                $query->where('status', 'active');
            })->count(),
            'vacant_properties' => Property::whereDoesntHave('leases', function($query) {
                $query->where('status', 'active');
            })->count(),
            'total_units' => House::count(),
            'occupied_units' => House::whereHas('leases', function($query) {
                $query->where('status', 'active');
            })->count(),
            'vacant_units' => House::whereDoesntHave('leases', function($query) {
                $query->where('status', 'active');
            })->count(),
        ];

        // Occupancy rates over time
        $occupancyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $totalUnits = House::whereMonth('created_at', '<=', $date->month)
                ->whereYear('created_at', '<=', $date->year)
                ->count();
            $occupiedUnits = House::whereHas('leases', function($query) use ($date) {
                $query->where('status', 'active')
                    ->where('start_date', '<=', $date)
                    ->where(function($q) use ($date) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', $date);
                    });
            })->count();
            
            $occupancyData[] = [
                'month' => $date->format('M Y'),
                'occupancy_rate' => $totalUnits > 0 ? round(($occupiedUnits / $totalUnits) * 100, 2) : 0,
                'total_units' => $totalUnits,
                'occupied_units' => $occupiedUnits,
            ];
        }

        // Property performance by type
        $propertyPerformance = Property::withCount(['houses', 'leases'])
            ->withSum('payments', 'amount')
            ->get()
            ->groupBy('property_type_id')
            ->map(function($properties) {
                return [
                    'count' => $properties->count(),
                    'total_revenue' => $properties->sum('payments_sum_amount'),
                    'avg_revenue' => $properties->avg('payments_sum_amount'),
                ];
            });

        return view('admin.analytics.properties', compact('propertyStats', 'occupancyData', 'propertyPerformance'));
    }

    /**
     * Display performance analytics
     */
    public function performance()
    {
        abort_unless(auth()->user()->can('view reports'), 403);

        // System performance metrics
        $performanceMetrics = [
            'avg_response_time' => $this->getAverageResponseTime(),
            'database_performance' => $this->getDatabasePerformance(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'error_rate' => $this->getErrorRate(),
        ];

        // Application metrics
        $appMetrics = [
            'total_requests' => $this->getTotalRequests(),
            'active_sessions' => $this->getActiveSessions(),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
        ];

        // Performance trends
        $performanceTrends = $this->getPerformanceTrends();

        return view('admin.analytics.performance', compact('performanceMetrics', 'appMetrics', 'performanceTrends'));
    }

    /**
     * Display system analytics
     */
    public function system()
    {
        abort_unless(auth()->user()->can('view system logs'), 403);

        // System information
        $systemInfo = [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
            'database_driver' => config('database.default'),
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
        ];

        // Database statistics
        $dbStats = [
            'total_tables' => $this->getTotalTables(),
            'total_records' => $this->getTotalRecords(),
            'database_size' => $this->getDatabaseSize(),
        ];

        // System health
        $systemHealth = [
            'disk_usage' => $this->getDiskUsage(),
            'memory_usage' => $this->getMemoryUsage(),
            'cpu_usage' => $this->getCpuUsage(),
        ];

        return view('admin.analytics.system', compact('systemInfo', 'dbStats', 'systemHealth'));
    }

    /**
     * Export analytics data
     */
    public function export(Request $request)
    {
        abort_unless(auth()->user()->can('view reports'), 403);

        $type = $request->get('type', 'users');
        $format = $request->get('format', 'csv');

        // Implementation for exporting analytics data
        // This would generate and download the appropriate file

        return response()->json(['message' => 'Export functionality will be implemented']);
    }

    // Helper methods for data collection
    private function getUserGrowthData()
    {
        return [
            'total_users' => User::count(),
            'monthly_growth' => User::whereMonth('created_at', now()->month)->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
        ];
    }

    private function getFinancialPerformanceData()
    {
        return [
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('created_at', now()->month)
                ->sum('amount'),
            'pending_payments' => Payment::where('status', 'pending')->sum('amount'),
        ];
    }

    private function getPropertyPerformanceData()
    {
        return [
            'total_properties' => Property::count(),
            'occupied_properties' => Property::whereHas('leases', function($query) {
                $query->where('status', 'active');
            })->count(),
            'occupancy_rate' => Property::count() > 0 ? 
                round((Property::whereHas('leases', function($query) {
                    $query->where('status', 'active');
                })->count() / Property::count()) * 100, 2) : 0,
        ];
    }

    private function getSystemPerformanceData()
    {
        return [
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'execution_time' => microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],
        ];
    }

    private function getRecentActivityData()
    {
        return [
            'recent_users' => User::latest()->limit(5)->get(),
            'recent_payments' => Payment::latest()->limit(5)->get(),
            'recent_properties' => Property::latest()->limit(5)->get(),
        ];
    }

    private function getAverageResponseTime()
    {
        // This would typically come from monitoring tools
        return rand(100, 500); // Mock data
    }

    private function getDatabasePerformance()
    {
        // This would typically come from database monitoring
        return [
            'avg_query_time' => rand(10, 50),
            'slow_queries' => rand(0, 5),
        ];
    }

    private function getCacheHitRate()
    {
        // This would typically come from cache monitoring
        return rand(80, 95);
    }

    private function getErrorRate()
    {
        // This would typically come from error monitoring
        return rand(0, 2);
    }

    private function getTotalRequests()
    {
        // This would typically come from web server logs
        return rand(1000, 10000);
    }

    private function getActiveSessions()
    {
        // This would typically come from session monitoring
        return rand(50, 200);
    }

    private function getPerformanceTrends()
    {
        // Mock performance trends data
        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $trends[] = [
                'date' => $date->format('M d'),
                'response_time' => rand(100, 500),
                'memory_usage' => rand(50, 200),
                'cpu_usage' => rand(20, 80),
            ];
        }
        return $trends;
    }

    private function getTotalTables()
    {
        return DB::select("SHOW TABLES") ? count(DB::select("SHOW TABLES")) : 0;
    }

    private function getTotalRecords()
    {
        $tables = ['users', 'properties', 'houses', 'leases', 'payments'];
        $total = 0;
        foreach ($tables as $table) {
            try {
                $total += DB::table($table)->count();
            } catch (\Exception $e) {
                // Table might not exist
            }
        }
        return $total;
    }

    private function getDatabaseSize()
    {
        // This would typically query the database for size information
        return '1.5 MB'; // Mock data
    }

    private function getDiskUsage()
    {
        $bytes = disk_free_space('.');
        $total = disk_total_space('.');
        return [
            'free' => $bytes,
            'total' => $total,
            'used' => $total - $bytes,
            'percentage' => round((($total - $bytes) / $total) * 100, 2),
        ];
    }

    private function getMemoryUsage()
    {
        return [
            'current' => memory_get_usage(true),
            'peak' => memory_get_peak_usage(true),
            'limit' => ini_get('memory_limit'),
        ];
    }

    private function getCpuUsage()
    {
        // This would typically come from system monitoring
        return rand(20, 80); // Mock data
    }
}
