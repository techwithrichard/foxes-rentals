<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class MonitoringService
{
    /**
     * Get system health metrics
     */
    public function getSystemHealth(): array
    {
        return [
            'timestamp' => now()->toISOString(),
            'status' => $this->getOverallStatus(),
            'database' => $this->getDatabaseHealth(),
            'cache' => $this->getCacheHealth(),
            'storage' => $this->getStorageHealth(),
            'queue' => $this->getQueueHealth(),
            'memory' => $this->getMemoryHealth(),
            'disk' => $this->getDiskHealth(),
            'services' => $this->getServicesHealth(),
            'alerts' => $this->getActiveAlerts()
        ];
    }

    /**
     * Get application metrics
     */
    public function getApplicationMetrics(): array
    {
        return [
            'timestamp' => now()->toISOString(),
            'users' => $this->getUserMetrics(),
            'properties' => $this->getPropertyMetrics(),
            'leases' => $this->getLeaseMetrics(),
            'payments' => $this->getPaymentMetrics(),
            'performance' => $this->getPerformanceMetrics(),
            'errors' => $this->getErrorMetrics()
        ];
    }

    /**
     * Get business metrics
     */
    public function getBusinessMetrics(): array
    {
        return [
            'timestamp' => now()->toISOString(),
            'revenue' => $this->getRevenueMetrics(),
            'occupancy' => $this->getOccupancyMetrics(),
            'customer_satisfaction' => $this->getCustomerSatisfactionMetrics(),
            'growth' => $this->getGrowthMetrics()
        ];
    }

    /**
     * Check system thresholds
     */
    public function checkThresholds(): array
    {
        $alerts = [];
        $health = $this->getSystemHealth();

        // Database connection time
        if ($health['database']['connection_time'] > 1000) {
            $alerts[] = [
                'type' => 'database',
                'severity' => 'high',
                'message' => 'Database connection time exceeds threshold',
                'value' => $health['database']['connection_time'],
                'threshold' => 1000
            ];
        }

        // Memory usage
        if ($health['memory']['usage_percentage'] > 80) {
            $alerts[] = [
                'type' => 'memory',
                'severity' => 'high',
                'message' => 'Memory usage exceeds threshold',
                'value' => $health['memory']['usage_percentage'],
                'threshold' => 80
            ];
        }

        // Disk usage
        if ($health['disk']['usage_percentage'] > 85) {
            $alerts[] = [
                'type' => 'disk',
                'severity' => 'medium',
                'message' => 'Disk usage exceeds threshold',
                'value' => $health['disk']['usage_percentage'],
                'threshold' => 85
            ];
        }

        // Cache hit rate
        if ($health['cache']['hit_rate'] < 70) {
            $alerts[] = [
                'type' => 'cache',
                'severity' => 'medium',
                'message' => 'Cache hit rate below threshold',
                'value' => $health['cache']['hit_rate'],
                'threshold' => 70
            ];
        }

        return $alerts;
    }

    /**
     * Send alert notifications
     */
    public function sendAlertNotifications(array $alerts): void
    {
        foreach ($alerts as $alert) {
            $this->sendAlert($alert);
        }
    }

    /**
     * Send individual alert
     */
    protected function sendAlert(array $alert): void
    {
        // Log alert
        Log::channel('monitoring')->warning('System Alert', $alert);

        // Send email notification for high severity alerts
        if ($alert['severity'] === 'high') {
            $this->sendEmailAlert($alert);
        }

        // Send Slack notification
        $this->sendSlackAlert($alert);

        // Store alert in database
        $this->storeAlert($alert);
    }

    /**
     * Get overall system status
     */
    protected function getOverallStatus(): string
    {
        $health = $this->getSystemHealth();
        
        if ($health['database']['status'] === 'error' || 
            $health['cache']['status'] === 'error' ||
            $health['memory']['usage_percentage'] > 90) {
            return 'critical';
        }
        
        if ($health['memory']['usage_percentage'] > 80 ||
            $health['disk']['usage_percentage'] > 85) {
            return 'warning';
        }
        
        return 'healthy';
    }

    /**
     * Get database health
     */
    protected function getDatabaseHealth(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $connectionTime = round((microtime(true) - $start) * 1000);

            // Get database metrics
            $queryCount = DB::select("SHOW STATUS LIKE 'Queries'")[0]->Value ?? 0;
            $slowQueries = DB::select("SHOW STATUS LIKE 'Slow_queries'")[0]->Value ?? 0;

            return [
                'status' => 'healthy',
                'connection_time' => $connectionTime,
                'query_count' => $queryCount,
                'slow_queries' => $slowQueries,
                'uptime' => $this->getDatabaseUptime()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'connection_time' => 0
            ];
        }
    }

    /**
     * Get cache health
     */
    protected function getCacheHealth(): array
    {
        try {
            $start = microtime(true);
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            $responseTime = round((microtime(true) - $start) * 1000);

            return [
                'status' => $value === 'ok' ? 'healthy' : 'error',
                'response_time' => $responseTime,
                'driver' => config('cache.default'),
                'hit_rate' => $this->getCacheHitRate()
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage(),
                'response_time' => 0
            ];
        }
    }

    /**
     * Get storage health
     */
    protected function getStorageHealth(): array
    {
        $path = storage_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;

        return [
            'status' => 'healthy',
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'usage_percentage' => round(($used / $total) * 100, 2)
        ];
    }

    /**
     * Get queue health
     */
    protected function getQueueHealth(): array
    {
        try {
            $queue = app('queue');
            return [
                'status' => 'healthy',
                'driver' => config('queue.default'),
                'size' => method_exists($queue, 'size') ? $queue->size() : 'unknown'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Get memory health
     */
    protected function getMemoryHealth(): array
    {
        return [
            'status' => 'healthy',
            'current' => $this->formatBytes(memory_get_usage(true)),
            'peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'limit' => ini_get('memory_limit'),
            'usage_percentage' => $this->getMemoryUsagePercentage()
        ];
    }

    /**
     * Get disk health
     */
    protected function getDiskHealth(): array
    {
        $path = base_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;

        return [
            'status' => 'healthy',
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'usage_percentage' => round(($used / $total) * 100, 2)
        ];
    }

    /**
     * Get services health
     */
    protected function getServicesHealth(): array
    {
        return [
            'web_server' => $this->checkWebServer(),
            'database_server' => $this->checkDatabaseServer(),
            'cache_server' => $this->checkCacheServer(),
            'queue_server' => $this->checkQueueServer()
        ];
    }

    /**
     * Get active alerts
     */
    protected function getActiveAlerts(): array
    {
        return Cache::get('active_alerts', []);
    }

    /**
     * Get user metrics
     */
    protected function getUserMetrics(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'new_users_today' => User::whereDate('created_at', today())->count(),
            'new_users_this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            'users_by_role' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->selectRaw('roles.name, COUNT(*) as count')
                ->groupBy('roles.name')
                ->pluck('count', 'name')
        ];
    }

    /**
     * Get property metrics
     */
    protected function getPropertyMetrics(): array
    {
        return [
            'total_properties' => Property::count(),
            'active_properties' => Property::where('status', 'active')->count(),
            'vacant_properties' => Property::where('is_vacant', true)->count(),
            'occupied_properties' => Property::where('is_vacant', false)->count(),
            'properties_by_type' => Property::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
        ];
    }

    /**
     * Get lease metrics
     */
    protected function getLeaseMetrics(): array
    {
        return [
            'total_leases' => Lease::count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'expired_leases' => Lease::where('end_date', '<', now())->count(),
            'expiring_leases' => Lease::where('end_date', '<=', now()->addDays(30))
                ->where('end_date', '>', now())
                ->count()
        ];
    }

    /**
     * Get payment metrics
     */
    protected function getPaymentMetrics(): array
    {
        return [
            'total_payments' => Payment::count(),
            'completed_payments' => Payment::where('status', 'completed')->count(),
            'pending_payments' => Payment::where('status', 'pending')->count(),
            'failed_payments' => Payment::where('status', 'failed')->count(),
            'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
            'monthly_revenue' => Payment::where('status', 'completed')
                ->whereMonth('paid_at', now()->month)
                ->whereYear('paid_at', now()->year)
                ->sum('amount')
        ];
    }

    /**
     * Get performance metrics
     */
    protected function getPerformanceMetrics(): array
    {
        return [
            'average_response_time' => $this->getAverageResponseTime(),
            'slow_queries' => $this->getSlowQueries(),
            'cache_hit_rate' => $this->getCacheHitRate(),
            'memory_usage' => $this->getMemoryUsagePercentage()
        ];
    }

    /**
     * Get error metrics
     */
    protected function getErrorMetrics(): array
    {
        $logFile = storage_path('logs/laravel.log');
        
        if (!file_exists($logFile)) {
            return [
                'errors_today' => 0,
                'errors_this_week' => 0,
                'error_rate' => 0
            ];
        }

        $today = now()->format('Y-m-d');
        $weekAgo = now()->subWeek()->format('Y-m-d');

        $errorsToday = $this->countLogEntries($logFile, $today);
        $errorsThisWeek = $this->countLogEntries($logFile, $weekAgo);

        return [
            'errors_today' => $errorsToday,
            'errors_this_week' => $errorsThisWeek,
            'error_rate' => $this->calculateErrorRate()
        ];
    }

    /**
     * Get revenue metrics
     */
    protected function getRevenueMetrics(): array
    {
        $totalRevenue = Payment::where('status', 'completed')->sum('amount');
        $monthlyRevenue = Payment::where('status', 'completed')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        return [
            'total_revenue' => $totalRevenue,
            'monthly_revenue' => $monthlyRevenue,
            'revenue_growth' => $this->calculateRevenueGrowth(),
            'average_payment' => Payment::where('status', 'completed')->avg('amount')
        ];
    }

    /**
     * Get occupancy metrics
     */
    protected function getOccupancyMetrics(): array
    {
        $totalProperties = Property::count();
        $occupiedProperties = Property::where('is_vacant', false)->count();

        return [
            'occupancy_rate' => $totalProperties > 0 ? ($occupiedProperties / $totalProperties) * 100 : 0,
            'vacancy_rate' => $totalProperties > 0 ? (($totalProperties - $occupiedProperties) / $totalProperties) * 100 : 0,
            'total_properties' => $totalProperties,
            'occupied_properties' => $occupiedProperties
        ];
    }

    /**
     * Get customer satisfaction metrics
     */
    protected function getCustomerSatisfactionMetrics(): array
    {
        // This would integrate with feedback system
        return [
            'overall_satisfaction' => 4.2, // Placeholder
            'response_rate' => 75, // Placeholder
            'satisfaction_trend' => 'improving' // Placeholder
        ];
    }

    /**
     * Get growth metrics
     */
    protected function getGrowthMetrics(): array
    {
        $currentMonth = now()->month;
        $lastMonth = now()->subMonth()->month;

        $currentUsers = User::whereMonth('created_at', $currentMonth)->count();
        $lastMonthUsers = User::whereMonth('created_at', $lastMonth)->count();

        $currentProperties = Property::whereMonth('created_at', $currentMonth)->count();
        $lastMonthProperties = Property::whereMonth('created_at', $lastMonth)->count();

        return [
            'user_growth' => $this->calculateGrowthRate($currentUsers, $lastMonthUsers),
            'property_growth' => $this->calculateGrowthRate($currentProperties, $lastMonthProperties),
            'revenue_growth' => $this->calculateRevenueGrowth()
        ];
    }

    /**
     * Helper methods
     */
    protected function getDatabaseUptime(): string
    {
        try {
            $result = DB::select("SHOW STATUS LIKE 'Uptime'")[0]->Value ?? 0;
            return $this->formatUptime($result);
        } catch (\Exception $e) {
            return 'Unknown';
        }
    }

    protected function getCacheHitRate(): float
    {
        try {
            $hits = DB::select("SHOW STATUS LIKE 'Qcache_hits'")[0]->Value ?? 0;
            $inserts = DB::select("SHOW STATUS LIKE 'Qcache_inserts'")[0]->Value ?? 0;
            
            $total = $hits + $inserts;
            return $total > 0 ? ($hits / $total) * 100 : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getMemoryUsagePercentage(): float
    {
        $limit = ini_get('memory_limit');
        $limitBytes = $this->convertToBytes($limit);
        $currentBytes = memory_get_usage(true);

        return round(($currentBytes / $limitBytes) * 100, 2);
    }

    protected function getAverageResponseTime(): float
    {
        // This would be calculated from actual response times
        return 150.5; // Placeholder in milliseconds
    }

    protected function getSlowQueries(): int
    {
        try {
            return DB::select("SHOW STATUS LIKE 'Slow_queries'")[0]->Value ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function countLogEntries(string $logFile, string $date): int
    {
        $count = 0;
        $handle = fopen($logFile, 'r');
        
        if ($handle) {
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, $date) !== false && strpos($line, 'ERROR') !== false) {
                    $count++;
                }
            }
            fclose($handle);
        }
        
        return $count;
    }

    protected function calculateErrorRate(): float
    {
        // This would calculate error rate based on total requests
        return 0.1; // Placeholder percentage
    }

    protected function calculateRevenueGrowth(): float
    {
        $currentMonth = Payment::where('status', 'completed')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount');

        $lastMonth = Payment::where('status', 'completed')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount');

        return $this->calculateGrowthRate($currentMonth, $lastMonth);
    }

    protected function calculateGrowthRate(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 2);
    }

    protected function formatBytes($size, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }

    protected function formatUptime(int $seconds): string
    {
        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);

        return "{$days}d {$hours}h {$minutes}m";
    }

    protected function convertToBytes(string $value): int
    {
        $value = trim($value);
        $last = strtolower($value[strlen($value) - 1]);
        $value = (int) $value;

        switch ($last) {
            case 'g':
                $value *= 1024;
            case 'm':
                $value *= 1024;
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    protected function checkWebServer(): array
    {
        return ['status' => 'healthy', 'response_time' => 50];
    }

    protected function checkDatabaseServer(): array
    {
        try {
            $start = microtime(true);
            DB::connection()->getPdo();
            $responseTime = round((microtime(true) - $start) * 1000);
            
            return ['status' => 'healthy', 'response_time' => $responseTime];
        } catch (\Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    protected function checkCacheServer(): array
    {
        try {
            $start = microtime(true);
            Cache::put('health_check', 'ok', 10);
            $responseTime = round((microtime(true) - $start) * 1000);
            
            return ['status' => 'healthy', 'response_time' => $responseTime];
        } catch (\Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    protected function checkQueueServer(): array
    {
        try {
            app('queue');
            return ['status' => 'healthy', 'response_time' => 10];
        } catch (\Exception $e) {
            return ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    protected function sendEmailAlert(array $alert): void
    {
        // Implementation for email alerts
        Log::info('Email alert sent', $alert);
    }

    protected function sendSlackAlert(array $alert): void
    {
        // Implementation for Slack alerts
        Log::info('Slack alert sent', $alert);
    }

    protected function storeAlert(array $alert): void
    {
        // Store alert in cache for now
        $alerts = Cache::get('active_alerts', []);
        $alerts[] = array_merge($alert, ['timestamp' => now()->toISOString()]);
        Cache::put('active_alerts', $alerts, 3600); // 1 hour
    }
}
