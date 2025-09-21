<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use App\Models\Property;
use App\Models\User;
use App\Models\Lease;
use App\Models\Payment;
use Carbon\Carbon;

class CacheService
{
    /**
     * Cache keys constants
     */
    const PROPERTY_STATS_KEY = 'property_stats';
    const USER_STATS_KEY = 'user_stats';
    const LEASE_STATS_KEY = 'lease_stats';
    const PAYMENT_STATS_KEY = 'payment_stats';
    const PROPERTIES_LIST_KEY = 'properties_list';
    const USERS_LIST_KEY = 'users_list';
    const LEASES_LIST_KEY = 'leases_list';
    const PAYMENTS_LIST_KEY = 'payments_list';
    const DASHBOARD_DATA_KEY = 'dashboard_data';
    const SYSTEM_HEALTH_KEY = 'system_health';

    /**
     * Cache TTL constants (in seconds)
     */
    const STATS_TTL = 3600; // 1 hour
    const LIST_TTL = 1800;  // 30 minutes
    const DASHBOARD_TTL = 900; // 15 minutes
    const HEALTH_TTL = 300; // 5 minutes

    /**
     * Get property statistics with caching
     */
    public function getPropertyStatistics(): array
    {
        return Cache::remember(self::PROPERTY_STATS_KEY, self::STATS_TTL, function () {
            return [
                'total_properties' => Property::count(),
                'active_properties' => Property::where('status', 'active')->count(),
                'vacant_properties' => Property::where('is_vacant', true)->count(),
                'occupied_properties' => Property::where('is_vacant', false)->count(),
                'properties_by_type' => Property::selectRaw('type, COUNT(*) as count')
                    ->groupBy('type')
                    ->pluck('count', 'type'),
                'properties_by_status' => Property::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'monthly_revenue' => $this->getMonthlyRevenue(),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get user statistics with caching
     */
    public function getUserStatistics(): array
    {
        return Cache::remember(self::USER_STATS_KEY, self::STATS_TTL, function () {
            return [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'inactive_users' => User::where('is_active', false)->count(),
                'users_by_role' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                    ->selectRaw('roles.name, COUNT(*) as count')
                    ->groupBy('roles.name')
                    ->pluck('count', 'name'),
                'recent_registrations' => User::where('created_at', '>=', now()->subDays(30))->count(),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get lease statistics with caching
     */
    public function getLeaseStatistics(): array
    {
        return Cache::remember(self::LEASE_STATS_KEY, self::STATS_TTL, function () {
            return [
                'total_leases' => Lease::count(),
                'active_leases' => Lease::where('status', 'active')->count(),
                'expired_leases' => Lease::where('end_date', '<', now())->count(),
                'expiring_leases' => Lease::where('end_date', '<=', now()->addDays(30))
                    ->where('end_date', '>', now())
                    ->count(),
                'leases_by_status' => Lease::selectRaw('status, COUNT(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status'),
                'average_lease_duration' => Lease::selectRaw('AVG(DATEDIFF(end_date, start_date)) as avg_days')
                    ->value('avg_days'),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get payment statistics with caching
     */
    public function getPaymentStatistics(): array
    {
        return Cache::remember(self::PAYMENT_STATS_KEY, self::STATS_TTL, function () {
            return [
                'total_payments' => Payment::count(),
                'completed_payments' => Payment::where('status', 'completed')->count(),
                'pending_payments' => Payment::where('status', 'pending')->count(),
                'failed_payments' => Payment::where('status', 'failed')->count(),
                'total_revenue' => Payment::where('status', 'completed')->sum('amount'),
                'monthly_revenue' => $this->getMonthlyRevenue(),
                'payments_by_method' => Payment::selectRaw('payment_method, COUNT(*) as count')
                    ->groupBy('payment_method')
                    ->pluck('count', 'payment_method'),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get optimized properties list with caching
     */
    public function getPropertiesList(array $filters = []): array
    {
        $cacheKey = self::PROPERTIES_LIST_KEY . '_' . md5(serialize($filters));
        
        return Cache::remember($cacheKey, self::LIST_TTL, function () use ($filters) {
            $query = Property::with([
                'landlord:id,name',
                'address:id,addressable_id,city,state',
                'lease.tenant:id,name',
                'houses:id,property_id,name'
            ])
            ->select('id', 'name', 'rent', 'status', 'is_vacant', 'landlord_id', 'type', 'created_at');

            // Apply filters
            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }
            if (isset($filters['landlord_id'])) {
                $query->where('landlord_id', $filters['landlord_id']);
            }
            if (isset($filters['is_vacant'])) {
                $query->where('is_vacant', $filters['is_vacant']);
            }
            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            return $query->latest()->get()->toArray();
        });
    }

    /**
     * Get dashboard data with caching
     */
    public function getDashboardData(): array
    {
        return Cache::remember(self::DASHBOARD_DATA_KEY, self::DASHBOARD_TTL, function () {
            return [
                'property_stats' => $this->getPropertyStatistics(),
                'user_stats' => $this->getUserStatistics(),
                'lease_stats' => $this->getLeaseStatistics(),
                'payment_stats' => $this->getPaymentStatistics(),
                'recent_activities' => $this->getRecentActivities(),
                'system_alerts' => $this->getSystemAlerts(),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Get system health with caching
     */
    public function getSystemHealth(): array
    {
        return Cache::remember(self::SYSTEM_HEALTH_KEY, self::HEALTH_TTL, function () {
            return [
                'database_status' => $this->checkDatabaseConnection(),
                'cache_status' => $this->checkCacheConnection(),
                'storage_status' => $this->checkStorageSpace(),
                'queue_status' => $this->checkQueueStatus(),
                'memory_usage' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
                'uptime' => $this->getSystemUptime(),
                'last_updated' => now()->toISOString(),
            ];
        });
    }

    /**
     * Clear all caches
     */
    public function clearAllCaches(): void
    {
        $keys = [
            self::PROPERTY_STATS_KEY,
            self::USER_STATS_KEY,
            self::LEASE_STATS_KEY,
            self::PAYMENT_STATS_KEY,
            self::DASHBOARD_DATA_KEY,
            self::SYSTEM_HEALTH_KEY,
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }

        // Clear pattern-based keys
        Cache::flush();
    }

    /**
     * Clear specific cache by pattern
     */
    public function clearCacheByPattern(string $pattern): void
    {
        if (config('cache.default') === 'redis' && class_exists('Redis')) {
            try {
                $keys = Redis::keys($pattern);
                if (!empty($keys)) {
                    Redis::del($keys);
                }
            } catch (\Exception $e) {
                // Fallback to flush all if Redis fails
                Cache::flush();
            }
        } else {
            // For file cache, we need to flush all as we can't pattern match
            Cache::flush();
        }
    }

    /**
     * Warm up caches
     */
    public function warmUpCaches(): void
    {
        $this->getPropertyStatistics();
        $this->getUserStatistics();
        $this->getLeaseStatistics();
        $this->getPaymentStatistics();
        $this->getDashboardData();
        $this->getSystemHealth();
    }

    /**
     * Get monthly revenue
     */
    private function getMonthlyRevenue(): float
    {
        return Payment::whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Get recent activities
     */
    private function getRecentActivities(): array
    {
        return [
            'recent_properties' => Property::latest()->limit(5)->get(['id', 'name', 'created_at']),
            'recent_users' => User::latest()->limit(5)->get(['id', 'name', 'created_at']),
            'recent_payments' => Payment::latest()->limit(5)->get(['id', 'amount', 'status', 'paid_at']),
        ];
    }

    /**
     * Get system alerts
     */
    private function getSystemAlerts(): array
    {
        $alerts = [];

        // Check for expired leases
        $expiredLeases = Lease::where('end_date', '<', now())->count();
        if ($expiredLeases > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$expiredLeases} leases have expired",
                'action' => 'admin.leases.expired',
            ];
        }

        // Check for pending payments
        $pendingPayments = Payment::where('status', 'pending')->count();
        if ($pendingPayments > 10) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingPayments} payments are pending",
                'action' => 'admin.payments.pending',
            ];
        }

        return $alerts;
    }

    /**
     * Check database connection
     */
    private function checkDatabaseConnection(): array
    {
        try {
            $start = microtime(true);
            \DB::connection()->getPdo();
            $duration = microtime(true) - $start;
            
            return [
                'status' => 'connected',
                'response_time' => round($duration * 1000, 2) . 'ms',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'disconnected',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check cache connection
     */
    private function checkCacheConnection(): array
    {
        try {
            $start = microtime(true);
            Cache::put('health_check', 'ok', 10);
            $value = Cache::get('health_check');
            $duration = microtime(true) - $start;
            
            return [
                'status' => $value === 'ok' ? 'connected' : 'error',
                'response_time' => round($duration * 1000, 2) . 'ms',
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'disconnected',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Check storage space
     */
    private function checkStorageSpace(): array
    {
        $path = storage_path();
        $total = disk_total_space($path);
        $free = disk_free_space($path);
        $used = $total - $free;
        
        return [
            'total' => $this->formatBytes($total),
            'used' => $this->formatBytes($used),
            'free' => $this->formatBytes($free),
            'percentage' => round(($used / $total) * 100, 2),
        ];
    }

    /**
     * Check queue status
     */
    private function checkQueueStatus(): array
    {
        try {
            $queue = app('queue');
            return [
                'status' => 'available',
                'driver' => config('queue.default'),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unavailable',
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get system uptime
     */
    private function getSystemUptime(): string
    {
        if (function_exists('sys_getloadavg')) {
            $load = sys_getloadavg();
            return "Load: " . implode(', ', array_map(function($load) {
                return round($load, 2);
            }, $load));
        }
        
        return 'N/A';
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($size, $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
            $size /= 1024;
        }
        return round($size, $precision) . ' ' . $units[$i];
    }
}
