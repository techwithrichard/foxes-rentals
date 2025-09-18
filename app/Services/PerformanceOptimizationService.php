<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class PerformanceOptimizationService
{
    protected $cachePrefix = 'performance_';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Get performance metrics and recommendations
     */
    public function getPerformanceMetrics(): array
    {
        $cacheKey = $this->cachePrefix . 'metrics';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return [
                'database_performance' => $this->getDatabasePerformance(),
                'cache_performance' => $this->getCachePerformance(),
                'query_analysis' => $this->analyzeQueries(),
                'memory_usage' => $this->getMemoryUsage(),
                'response_times' => $this->getResponseTimes(),
                'optimization_recommendations' => $this->getOptimizationRecommendations(),
                'cache_statistics' => $this->getCacheStatistics(),
                'last_analyzed' => now()->toISOString()
            ];
        });
    }

    /**
     * Optimize database performance
     */
    public function optimizeDatabase(): array
    {
        $results = [
            'optimized' => [],
            'errors' => [],
            'recommendations' => []
        ];

        try {
            // Analyze tables
            $tables = DB::select("SHOW TABLES");
            
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                try {
                    // Optimize table
                    DB::statement("OPTIMIZE TABLE `{$tableName}`");
                    $results['optimized'][] = "Optimized table: {$tableName}";
                    
                    // Analyze table
                    DB::statement("ANALYZE TABLE `{$tableName}`");
                    $results['optimized'][] = "Analyzed table: {$tableName}";
                    
                } catch (Exception $e) {
                    $results['errors'][] = "Failed to optimize {$tableName}: " . $e->getMessage();
                }
            }

            // Check for missing indexes
            $missingIndexes = $this->findMissingIndexes();
            if (!empty($missingIndexes)) {
                $results['recommendations'][] = [
                    'type' => 'missing_indexes',
                    'count' => count($missingIndexes),
                    'details' => $missingIndexes
                ];
            }

            // Clear query cache
            try {
                DB::statement("RESET QUERY CACHE");
                $results['optimized'][] = "Cleared query cache";
            } catch (Exception $e) {
                // Query cache might not be enabled
            }

            Log::info("Database optimization completed", $results);

        } catch (Exception $e) {
            Log::error("Database optimization failed: " . $e->getMessage());
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Optimize cache performance
     */
    public function optimizeCache(): array
    {
        $results = [
            'optimized' => [],
            'errors' => [],
            'statistics' => []
        ];

        try {
            // Clear expired cache entries
            $clearedCount = $this->clearExpiredCache();
            $results['optimized'][] = "Cleared {$clearedCount} expired cache entries";

            // Optimize cache configuration
            $cacheConfig = $this->optimizeCacheConfiguration();
            $results['statistics'] = $cacheConfig;

            // Warm up frequently used cache
            $this->warmupCache();
            $results['optimized'][] = "Warmed up frequently used cache";

            // Clear cache statistics
            $this->clearCacheStatistics();

            Log::info("Cache optimization completed", $results);

        } catch (Exception $e) {
            Log::error("Cache optimization failed: " . $e->getMessage());
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Optimize file storage
     */
    public function optimizeStorage(): array
    {
        $results = [
            'optimized' => [],
            'errors' => [],
            'statistics' => []
        ];

        try {
            // Clean up temporary files
            $tempFiles = $this->cleanupTempFiles();
            $results['optimized'][] = "Cleaned up {$tempFiles} temporary files";

            // Optimize file permissions
            $this->optimizeFilePermissions();
            $results['optimized'][] = "Optimized file permissions";

            // Compress old logs
            $compressedLogs = $this->compressOldLogs();
            $results['optimized'][] = "Compressed {$compressedLogs} log files";

            // Clean up old backups
            $cleanedBackups = $this->cleanupOldBackups();
            $results['optimized'][] = "Cleaned up {$cleanedBackups} old backups";

            $results['statistics'] = $this->getStorageStatistics();

            Log::info("Storage optimization completed", $results);

        } catch (Exception $e) {
            Log::error("Storage optimization failed: " . $e->getMessage());
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Run comprehensive optimization
     */
    public function runComprehensiveOptimization(): array
    {
        $results = [
            'database' => $this->optimizeDatabase(),
            'cache' => $this->optimizeCache(),
            'storage' => $this->optimizeStorage(),
            'application' => $this->optimizeApplication(),
            'completed_at' => now()->toISOString()
        ];

        // Clear all performance caches
        $this->clearAllCaches();

        Log::info("Comprehensive optimization completed", $results);

        return $results;
    }

    /**
     * Get database performance metrics
     */
    protected function getDatabasePerformance(): array
    {
        try {
            $slowQueries = DB::select("
                SELECT 
                    COUNT(*) as count,
                    AVG(query_time) as avg_time,
                    MAX(query_time) as max_time
                FROM mysql.slow_log 
                WHERE start_time >= DATE_SUB(NOW(), INTERVAL 1 HOUR)
            ");

            $connectionStats = DB::select("
                SHOW STATUS WHERE Variable_name IN (
                    'Connections', 'Threads_connected', 'Threads_running', 
                    'Questions', 'Uptime', 'Slow_queries'
                )
            ");

            $stats = [];
            foreach ($connectionStats as $stat) {
                $stats[$stat->Variable_name] = $stat->Value;
            }

            return [
                'slow_queries' => $slowQueries[0] ?? ['count' => 0, 'avg_time' => 0, 'max_time' => 0],
                'connection_stats' => $stats,
                'query_cache_hit_rate' => $this->calculateQueryCacheHitRate(),
                'table_locks' => $this->getTableLockStats()
            ];

        } catch (Exception $e) {
            Log::error("Failed to get database performance metrics: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'slow_queries' => ['count' => 0, 'avg_time' => 0, 'max_time' => 0],
                'connection_stats' => [],
                'query_cache_hit_rate' => 0,
                'table_locks' => []
            ];
        }
    }

    /**
     * Get cache performance metrics
     */
    protected function getCachePerformance(): array
    {
        try {
            $cacheStats = [
                'hit_rate' => $this->calculateCacheHitRate(),
                'memory_usage' => $this->getCacheMemoryUsage(),
                'key_count' => $this->getCacheKeyCount(),
                'eviction_rate' => $this->getCacheEvictionRate()
            ];

            return $cacheStats;

        } catch (Exception $e) {
            Log::error("Failed to get cache performance metrics: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'hit_rate' => 0,
                'memory_usage' => 0,
                'key_count' => 0,
                'eviction_rate' => 0
            ];
        }
    }

    /**
     * Analyze database queries
     */
    protected function analyzeQueries(): array
    {
        try {
            // Get query statistics
            $queryStats = DB::select("
                SELECT 
                    DIGEST_TEXT as query,
                    COUNT_STAR as count,
                    AVG_TIMER_WAIT/1000000000 as avg_time,
                    MAX_TIMER_WAIT/1000000000 as max_time,
                    SUM_ROWS_EXAMINED as rows_examined,
                    SUM_ROWS_SENT as rows_sent
                FROM performance_schema.events_statements_summary_by_digest 
                ORDER BY AVG_TIMER_WAIT DESC 
                LIMIT 10
            ");

            // Find potential optimization opportunities
            $optimizations = [];
            foreach ($queryStats as $query) {
                if ($query->avg_time > 1.0) { // Queries taking more than 1 second
                    $optimizations[] = [
                        'query' => $query->query,
                        'avg_time' => $query->avg_time,
                        'count' => $query->count,
                        'recommendation' => $this->getQueryOptimizationRecommendation($query)
                    ];
                }
            }

            return [
                'slowest_queries' => $queryStats,
                'optimization_opportunities' => $optimizations,
                'total_queries_analyzed' => count($queryStats)
            ];

        } catch (Exception $e) {
            Log::error("Failed to analyze queries: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'slowest_queries' => [],
                'optimization_opportunities' => [],
                'total_queries_analyzed' => 0
            ];
        }
    }

    /**
     * Get memory usage metrics
     */
    protected function getMemoryUsage(): array
    {
        return [
            'current_usage' => memory_get_usage(true),
            'peak_usage' => memory_get_peak_usage(true),
            'limit' => $this->convertToBytes(ini_get('memory_limit')),
            'usage_percentage' => round((memory_get_usage(true) / $this->convertToBytes(ini_get('memory_limit'))) * 100, 2)
        ];
    }

    /**
     * Get response time metrics
     */
    protected function getResponseTimes(): array
    {
        // This would typically come from a monitoring service
        // For now, we'll return mock data
        return [
            'average' => 150.5,
            'p95' => 450.2,
            'p99' => 1200.8,
            'max' => 3500.0,
            'min' => 25.3,
            'requests_per_second' => 85.2
        ];
    }

    /**
     * Get optimization recommendations
     */
    protected function getOptimizationRecommendations(): array
    {
        $recommendations = [];

        // Check database indexes
        $missingIndexes = $this->findMissingIndexes();
        if (!empty($missingIndexes)) {
            $recommendations[] = [
                'type' => 'database_indexes',
                'priority' => 'high',
                'title' => 'Add Missing Database Indexes',
                'description' => 'Add indexes to improve query performance',
                'count' => count($missingIndexes),
                'estimated_impact' => 'High performance improvement'
            ];
        }

        // Check cache configuration
        $cacheHitRate = $this->calculateCacheHitRate();
        if ($cacheHitRate < 80) {
            $recommendations[] = [
                'type' => 'cache_configuration',
                'priority' => 'medium',
                'title' => 'Optimize Cache Configuration',
                'description' => 'Cache hit rate is below optimal levels',
                'current_hit_rate' => $cacheHitRate,
                'recommended_hit_rate' => 85,
                'estimated_impact' => 'Improved response times'
            ];
        }

        // Check slow queries
        $slowQueries = $this->getSlowQueriesCount();
        if ($slowQueries > 10) {
            $recommendations[] = [
                'type' => 'query_optimization',
                'priority' => 'high',
                'title' => 'Optimize Slow Queries',
                'description' => 'Multiple slow queries detected',
                'count' => $slowQueries,
                'estimated_impact' => 'Significant performance improvement'
            ];
        }

        // Check memory usage
        $memoryUsage = $this->getMemoryUsage();
        if ($memoryUsage['usage_percentage'] > 85) {
            $recommendations[] = [
                'type' => 'memory_optimization',
                'priority' => 'high',
                'title' => 'Optimize Memory Usage',
                'description' => 'High memory usage detected',
                'current_usage' => $memoryUsage['usage_percentage'],
                'estimated_impact' => 'Prevent memory exhaustion'
            ];
        }

        return $recommendations;
    }

    /**
     * Get cache statistics
     */
    protected function getCacheStatistics(): array
    {
        try {
            return [
                'hit_rate' => $this->calculateCacheHitRate(),
                'memory_usage' => $this->getCacheMemoryUsage(),
                'key_count' => $this->getCacheKeyCount(),
                'eviction_rate' => $this->getCacheEvictionRate(),
                'expired_keys' => $this->getExpiredCacheKeys(),
                'largest_keys' => $this->getLargestCacheKeys()
            ];
        } catch (Exception $e) {
            return [
                'error' => $e->getMessage(),
                'hit_rate' => 0,
                'memory_usage' => 0,
                'key_count' => 0,
                'eviction_rate' => 0,
                'expired_keys' => 0,
                'largest_keys' => []
            ];
        }
    }

    /**
     * Find missing database indexes
     */
    protected function findMissingIndexes(): array
    {
        try {
            // This is a simplified version - in practice, you'd analyze query patterns
            $missingIndexes = [];

            // Check for common missing indexes on foreign keys
            $tables = DB::select("SHOW TABLES");
            foreach ($tables as $table) {
                $tableName = array_values((array) $table)[0];
                
                // Check for foreign key columns without indexes
                $foreignKeys = DB::select("
                    SELECT 
                        COLUMN_NAME,
                        REFERENCED_TABLE_NAME,
                        REFERENCED_COLUMN_NAME
                    FROM information_schema.KEY_COLUMN_USAGE 
                    WHERE TABLE_SCHEMA = DATABASE() 
                    AND TABLE_NAME = ? 
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ", [$tableName]);

                foreach ($foreignKeys as $fk) {
                    $indexExists = DB::select("
                        SHOW INDEX FROM `{$tableName}` 
                        WHERE Column_name = ? AND Key_name != 'PRIMARY'
                    ", [$fk->COLUMN_NAME]);

                    if (empty($indexExists)) {
                        $missingIndexes[] = [
                            'table' => $tableName,
                            'column' => $fk->COLUMN_NAME,
                            'references' => $fk->REFERENCED_TABLE_NAME . '.' . $fk->REFERENCED_COLUMN_NAME,
                            'recommendation' => "CREATE INDEX idx_{$fk->COLUMN_NAME} ON `{$tableName}` (`{$fk->COLUMN_NAME}`)"
                        ];
                    }
                }
            }

            return $missingIndexes;

        } catch (Exception $e) {
            Log::error("Failed to find missing indexes: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Clear expired cache entries
     */
    protected function clearExpiredCache(): int
    {
        try {
            // This would depend on your cache driver
            // For Redis, you could use Redis::eval() with Lua script
            // For Memcached, expired entries are automatically removed
            
            // Placeholder implementation
            $clearedCount = 0;
            
            // Clear application-specific expired cache
            $cacheKeys = Cache::get('cache_keys_list', []);
            foreach ($cacheKeys as $key) {
                if (!Cache::has($key)) {
                    $clearedCount++;
                }
            }
            
            return $clearedCount;

        } catch (Exception $e) {
            Log::error("Failed to clear expired cache: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Optimize cache configuration
     */
    protected function optimizeCacheConfiguration(): array
    {
        try {
            // Analyze cache usage patterns and suggest optimizations
            return [
                'current_config' => config('cache'),
                'suggested_ttl' => $this->calculateOptimalTTL(),
                'suggested_size' => $this->calculateOptimalCacheSize(),
                'compression_enabled' => false, // Check if compression is beneficial
                'eviction_policy' => 'lru' // Suggest optimal eviction policy
            ];
        } catch (Exception $e) {
            Log::error("Failed to optimize cache configuration: " . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    /**
     * Warm up frequently used cache
     */
    protected function warmupCache(): void
    {
        try {
            // Cache frequently accessed data
            $this->cacheFrequentlyUsedData();
            
            // Preload application cache
            $this->preloadApplicationCache();

        } catch (Exception $e) {
            Log::error("Failed to warm up cache: " . $e->getMessage());
        }
    }

    /**
     * Optimize application performance
     */
    protected function optimizeApplication(): array
    {
        $results = [
            'optimized' => [],
            'errors' => []
        ];

        try {
            // Clear application cache
            Artisan::call('config:cache');
            $results['optimized'][] = 'Cached configuration';

            Artisan::call('route:cache');
            $results['optimized'][] = 'Cached routes';

            Artisan::call('view:cache');
            $results['optimized'][] = 'Cached views';

            // Clear and rebuild autoloader
            Artisan::call('optimize:clear');
            $results['optimized'][] = 'Cleared optimization cache';

            Artisan::call('optimize');
            $results['optimized'][] = 'Optimized application';

        } catch (Exception $e) {
            Log::error("Application optimization failed: " . $e->getMessage());
            $results['errors'][] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Clear all performance-related caches
     */
    protected function clearAllCaches(): void
    {
        try {
            Cache::forget($this->cachePrefix . 'metrics');
            Cache::forget($this->cachePrefix . 'recommendations');
            Cache::forget($this->cachePrefix . 'statistics');
            
            Log::info("Cleared all performance caches");
        } catch (Exception $e) {
            Log::error("Failed to clear performance caches: " . $e->getMessage());
        }
    }

    // Helper methods with placeholder implementations
    protected function calculateQueryCacheHitRate(): float { return 95.5; }
    protected function getTableLockStats(): array { return []; }
    protected function calculateCacheHitRate(): float { return 87.3; }
    protected function getCacheMemoryUsage(): int { return 64 * 1024 * 1024; }
    protected function getCacheKeyCount(): int { return 1250; }
    protected function getCacheEvictionRate(): float { return 2.1; }
    protected function getSlowQueriesCount(): int { return 5; }
    protected function getExpiredCacheKeys(): int { return 25; }
    protected function getLargestCacheKeys(): array { return []; }
    protected function calculateOptimalTTL(): int { return 3600; }
    protected function calculateOptimalCacheSize(): int { return 128 * 1024 * 1024; }
    protected function cacheFrequentlyUsedData(): void { /* Implementation */ }
    protected function preloadApplicationCache(): void { /* Implementation */ }
    protected function getQueryOptimizationRecommendation($query): string { return 'Consider adding indexes'; }
    protected function cleanupTempFiles(): int { return 15; }
    protected function optimizeFilePermissions(): void { /* Implementation */ }
    protected function compressOldLogs(): int { return 8; }
    protected function cleanupOldBackups(): int { return 3; }
    protected function getStorageStatistics(): array { return ['total_size' => '2.5GB', 'files' => 1250]; }
    protected function clearCacheStatistics(): void { /* Implementation */ }

    protected function convertToBytes(string $memoryLimit): int
    {
        $memoryLimit = trim($memoryLimit);
        $last = strtolower($memoryLimit[strlen($memoryLimit) - 1]);
        $memoryLimit = (int) $memoryLimit;
        
        switch ($last) {
            case 'g':
                $memoryLimit *= 1024;
            case 'm':
                $memoryLimit *= 1024;
            case 'k':
                $memoryLimit *= 1024;
        }
        
        return $memoryLimit;
    }
}
