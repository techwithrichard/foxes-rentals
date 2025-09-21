<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class PerformanceMonitoringService
{
    /**
     * Monitor database performance
     */
    public function monitorDatabasePerformance(): array
    {
        $metrics = [];

        // Connection time
        $start = microtime(true);
        DB::connection()->getPdo();
        $metrics['connection_time'] = round((microtime(true) - $start) * 1000, 2);

        // Query performance
        $start = microtime(true);
        DB::select('SELECT 1');
        $metrics['query_time'] = round((microtime(true) - $start) * 1000, 2);

        // Table sizes
        $metrics['table_sizes'] = $this->getTableSizes();

        // Index usage
        $metrics['index_usage'] = $this->getIndexUsage();

        // Slow queries
        $metrics['slow_queries'] = $this->getSlowQueries();

        return $metrics;
    }

    /**
     * Monitor cache performance
     */
    public function monitorCachePerformance(): array
    {
        $metrics = [];

        // Cache write performance
        $start = microtime(true);
        Cache::put('perf_test', 'test_value', 60);
        $metrics['write_time'] = round((microtime(true) - $start) * 1000, 2);

        // Cache read performance
        $start = microtime(true);
        Cache::get('perf_test');
        $metrics['read_time'] = round((microtime(true) - $start) * 1000, 2);

        // Cache hit rate (if using Redis)
        if (config('cache.default') === 'redis') {
            $metrics['hit_rate'] = $this->getCacheHitRate();
        }

        // Cache size
        $metrics['cache_size'] = $this->getCacheSize();

        return $metrics;
    }

    /**
     * Monitor memory usage
     */
    public function monitorMemoryUsage(): array
    {
        return [
            'current_memory' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'memory_limit' => ini_get('memory_limit'),
            'memory_usage_percentage' => $this->getMemoryUsagePercentage(),
        ];
    }

    /**
     * Monitor storage performance
     */
    public function monitorStoragePerformance(): array
    {
        $metrics = [];

        // Write performance
        $start = microtime(true);
        Storage::put('perf_test.txt', 'test content');
        $metrics['write_time'] = round((microtime(true) - $start) * 1000, 2);

        // Read performance
        $start = microtime(true);
        Storage::get('perf_test.txt');
        $metrics['read_time'] = round((microtime(true) - $start) * 1000, 2);

        // Clean up test file
        Storage::delete('perf_test.txt');

        // Storage space
        $metrics['storage_space'] = $this->getStorageSpace();

        return $metrics;
    }

    /**
     * Monitor queue performance
     */
    public function monitorQueuePerformance(): array
    {
        $metrics = [];

        try {
            $queue = app('queue');
            $metrics['queue_driver'] = config('queue.default');
            $metrics['queue_status'] = 'available';

            // Get queue size (if supported)
            if (method_exists($queue, 'size')) {
                $metrics['queue_size'] = $queue->size();
            }

        } catch (\Exception $e) {
            $metrics['queue_status'] = 'unavailable';
            $metrics['queue_error'] = $e->getMessage();
        }

        return $metrics;
    }

    /**
     * Get comprehensive performance report
     */
    public function getPerformanceReport(): array
    {
        $start = microtime(true);

        $report = [
            'timestamp' => now()->toISOString(),
            'database' => $this->monitorDatabasePerformance(),
            'cache' => $this->monitorCachePerformance(),
            'memory' => $this->monitorMemoryUsage(),
            'storage' => $this->monitorStoragePerformance(),
            'queue' => $this->monitorQueuePerformance(),
            'system' => $this->getSystemMetrics(),
            'generation_time' => round((microtime(true) - $start) * 1000, 2),
        ];

        // Log performance metrics
        Log::info('Performance report generated', $report);

        return $report;
    }

    /**
     * Get table sizes
     */
    private function getTableSizes(): array
    {
        try {
            $tables = DB::select("
                SELECT 
                    table_name,
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS size_mb
                FROM information_schema.tables 
                WHERE table_schema = DATABASE()
                ORDER BY (data_length + index_length) DESC
                LIMIT 10
            ");

            return array_map(function ($table) {
                return [
                    'name' => $table->table_name,
                    'size_mb' => $table->size_mb
                ];
            }, $tables);

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get index usage statistics
     */
    private function getIndexUsage(): array
    {
        try {
            $indexes = DB::select("
                SELECT 
                    table_name,
                    index_name,
                    cardinality
                FROM information_schema.statistics 
                WHERE table_schema = DATABASE()
                ORDER BY cardinality DESC
                LIMIT 10
            ");

            return array_map(function ($index) {
                return [
                    'table' => $index->table_name,
                    'index' => $index->index_name,
                    'cardinality' => $index->cardinality
                ];
            }, $indexes);

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get slow queries
     */
    private function getSlowQueries(): array
    {
        try {
            $queries = DB::select("
                SELECT 
                    query_time,
                    lock_time,
                    rows_sent,
                    rows_examined,
                    LEFT(sql_text, 100) as sql_preview
                FROM mysql.slow_log 
                WHERE start_time >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
                ORDER BY query_time DESC
                LIMIT 5
            ");

            return array_map(function ($query) {
                return [
                    'query_time' => $query->query_time,
                    'lock_time' => $query->lock_time,
                    'rows_sent' => $query->rows_sent,
                    'rows_examined' => $query->rows_examined,
                    'sql_preview' => $query->sql_preview
                ];
            }, $queries);

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get cache hit rate
     */
    private function getCacheHitRate(): float
    {
        try {
            $stats = DB::select("SHOW STATUS LIKE 'Qcache%'");
            $hits = 0;
            $misses = 0;

            foreach ($stats as $stat) {
                if ($stat->Variable_name === 'Qcache_hits') {
                    $hits = $stat->Value;
                } elseif ($stat->Variable_name === 'Qcache_inserts') {
                    $misses = $stat->Value;
                }
            }

            $total = $hits + $misses;
            return $total > 0 ? round(($hits / $total) * 100, 2) : 0;

        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Get cache size
     */
    private function getCacheSize(): array
    {
        try {
            if (config('cache.default') === 'redis' && class_exists('Redis')) {
                try {
                    $redis = app('redis');
                    $info = $redis->info('memory');
                    return [
                        'used_memory' => $info['used_memory_human'] ?? 'N/A',
                        'max_memory' => $info['maxmemory_human'] ?? 'N/A',
                    ];
                } catch (\Exception $e) {
                    return ['driver' => config('cache.default'), 'error' => $e->getMessage()];
                }
            }

            return ['driver' => config('cache.default')];

        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Get memory usage percentage
     */
    private function getMemoryUsagePercentage(): float
    {
        $limit = ini_get('memory_limit');
        $limitBytes = $this->convertToBytes($limit);
        $currentBytes = memory_get_usage(true);

        return round(($currentBytes / $limitBytes) * 100, 2);
    }

    /**
     * Get storage space
     */
    private function getStorageSpace(): array
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
     * Get system metrics
     */
    private function getSystemMetrics(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'server_time' => now()->toISOString(),
            'timezone' => config('app.timezone'),
            'environment' => config('app.env'),
            'debug_mode' => config('app.debug'),
            'load_average' => function_exists('sys_getloadavg') ? sys_getloadavg() : null,
        ];
    }

    /**
     * Convert memory limit to bytes
     */
    private function convertToBytes(string $value): int
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

    /**
     * Log performance alert if thresholds exceeded
     */
    public function checkPerformanceThresholds(array $metrics): void
    {
        $thresholds = [
            'database_connection_time' => 100, // ms
            'database_query_time' => 50, // ms
            'cache_write_time' => 10, // ms
            'cache_read_time' => 5, // ms
            'memory_usage_percentage' => 80, // %
        ];

        foreach ($thresholds as $metric => $threshold) {
            if (isset($metrics[$metric]) && $metrics[$metric] > $threshold) {
                Log::warning("Performance threshold exceeded", [
                    'metric' => $metric,
                    'value' => $metrics[$metric],
                    'threshold' => $threshold
                ]);
            }
        }
    }
}
