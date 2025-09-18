<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;
use Exception;

class SystemHealthMonitoringService
{
    protected $cachePrefix = 'system_health_';
    protected $cacheTtl = 300; // 5 minutes
    protected $warningThresholds = [
        'disk_usage' => 80, // 80%
        'memory_usage' => 85, // 85%
        'cpu_usage' => 90, // 90%
        'response_time' => 2000, // 2 seconds
        'error_rate' => 5, // 5%
        'queue_size' => 1000, // 1000 jobs
        'cache_hit_rate' => 70, // 70%
        'database_connections' => 80 // 80% of max
    ];

    /**
     * Get comprehensive system health status
     */
    public function getSystemHealth(): array
    {
        $cacheKey = $this->cachePrefix . 'overall';
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () {
            return [
                'overall_status' => $this->getOverallStatus(),
                'database' => $this->checkDatabaseHealth(),
                'cache' => $this->checkCacheHealth(),
                'storage' => $this->checkStorageHealth(),
                'memory' => $this->checkMemoryHealth(),
                'performance' => $this->checkPerformanceHealth(),
                'external_services' => $this->checkExternalServicesHealth(),
                'security' => $this->checkSecurityHealth(),
                'queue' => $this->checkQueueHealth(),
                'logs' => $this->checkLogsHealth(),
                'last_checked' => now()->toISOString(),
                'uptime' => $this->getSystemUptime(),
                'version_info' => $this->getVersionInfo()
            ];
        });
    }

    /**
     * Get overall system status
     */
    protected function getOverallStatus(): array
    {
        $checks = [
            $this->checkDatabaseHealth(),
            $this->checkCacheHealth(),
            $this->checkStorageHealth(),
            $this->checkMemoryHealth(),
            $this->checkPerformanceHealth(),
            $this->checkExternalServicesHealth(),
            $this->checkSecurityHealth(),
            $this->checkQueueHealth()
        ];

        $statuses = array_column($checks, 'status');
        $criticalCount = count(array_filter($statuses, fn($status) => $status === 'critical'));
        $warningCount = count(array_filter($statuses, fn($status) => $status === 'warning'));
        $healthyCount = count(array_filter($statuses, fn($status) => $status === 'healthy'));

        if ($criticalCount > 0) {
            $overallStatus = 'critical';
        } elseif ($warningCount > 0) {
            $overallStatus = 'warning';
        } else {
            $overallStatus = 'healthy';
        }

        return [
            'status' => $overallStatus,
            'summary' => [
                'healthy' => $healthyCount,
                'warning' => $warningCount,
                'critical' => $criticalCount,
                'total' => count($checks)
            ],
            'score' => round((($healthyCount * 100) + ($warningCount * 50)) / count($checks))
        ];
    }

    /**
     * Check database health
     */
    public function checkDatabaseHealth(): array
    {
        try {
            $startTime = microtime(true);
            
            // Test database connection
            DB::connection()->getPdo();
            
            // Get connection info
            $connectionCount = DB::select("SHOW STATUS LIKE 'Threads_connected'")[0]->Value ?? 0;
            $maxConnections = DB::select("SHOW VARIABLES LIKE 'max_connections'")[0]->Value ?? 100;
            $connectionUsage = ($connectionCount / $maxConnections) * 100;
            
            // Check database size
            $databaseSize = $this->getDatabaseSize();
            
            // Test query performance
            $queryTime = (microtime(true) - $startTime) * 1000;
            
            $status = 'healthy';
            $issues = [];
            
            if ($connectionUsage > $this->warningThresholds['database_connections']) {
                $status = $connectionUsage > 90 ? 'critical' : 'warning';
                $issues[] = "High database connection usage: {$connectionUsage}%";
            }
            
            if ($queryTime > $this->warningThresholds['response_time']) {
                $status = $queryTime > 5000 ? 'critical' : 'warning';
                $issues[] = "Slow database response: {$queryTime}ms";
            }

            return [
                'status' => $status,
                'connection_count' => $connectionCount,
                'max_connections' => $maxConnections,
                'connection_usage' => round($connectionUsage, 2),
                'database_size' => $databaseSize,
                'response_time' => round($queryTime, 2),
                'issues' => $issues,
                'last_checked' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error("Database health check failed: " . $e->getMessage());
            
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'issues' => ['Database connection failed'],
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Check cache health
     */
    public function checkCacheHealth(): array
    {
        try {
            $testKey = 'health_check_' . uniqid();
            $testValue = 'test_value_' . time();
            
            // Test cache write
            $writeStart = microtime(true);
            Cache::put($testKey, $testValue, 60);
            $writeTime = (microtime(true) - $writeStart) * 1000;
            
            // Test cache read
            $readStart = microtime(true);
            $retrievedValue = Cache::get($testKey);
            $readTime = (microtime(true) - $readStart) * 1000;
            
            // Test cache delete
            Cache::forget($testKey);
            
            $status = 'healthy';
            $issues = [];
            
            if ($writeTime > 100 || $readTime > 50) {
                $status = 'warning';
                $issues[] = "Slow cache performance: Write {$writeTime}ms, Read {$readTime}ms";
            }
            
            if ($retrievedValue !== $testValue) {
                $status = 'critical';
                $issues[] = 'Cache read/write test failed';
            }

            // Get cache statistics
            $cacheStats = $this->getCacheStatistics();

            return [
                'status' => $status,
                'write_time' => round($writeTime, 2),
                'read_time' => round($readTime, 2),
                'hit_rate' => $cacheStats['hit_rate'],
                'memory_usage' => $cacheStats['memory_usage'],
                'key_count' => $cacheStats['key_count'],
                'issues' => $issues,
                'last_checked' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error("Cache health check failed: " . $e->getMessage());
            
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'issues' => ['Cache system unavailable'],
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Check storage health
     */
    public function checkStorageHealth(): array
    {
        try {
            $diskUsage = $this->getDiskUsage();
            $logSize = $this->getLogSize();
            $backupSize = $this->getBackupSize();
            
            $status = 'healthy';
            $issues = [];
            
            if ($diskUsage['percentage'] > $this->warningThresholds['disk_usage']) {
                $status = $diskUsage['percentage'] > 95 ? 'critical' : 'warning';
                $issues[] = "High disk usage: {$diskUsage['percentage']}%";
            }
            
            if ($logSize > 1024 * 1024 * 1024) { // 1GB
                $status = $logSize > 5 * 1024 * 1024 * 1024 ? 'critical' : 'warning';
                $issues[] = "Large log files: " . $this->formatBytes($logSize);
            }

            return [
                'status' => $status,
                'disk_usage' => $diskUsage,
                'log_size' => $logSize,
                'backup_size' => $backupSize,
                'free_space' => $diskUsage['free'],
                'total_space' => $diskUsage['total'],
                'usage_percentage' => $diskUsage['percentage'],
                'issues' => $issues,
                'last_checked' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error("Storage health check failed: " . $e->getMessage());
            
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'issues' => ['Storage check failed'],
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Check memory health
     */
    public function checkMemoryHealth(): array
    {
        try {
            $memoryUsage = $this->getMemoryUsage();
            $phpMemoryLimit = $this->getPhpMemoryLimit();
            $memoryPercentage = ($memoryUsage['used'] / $phpMemoryLimit) * 100;
            
            $status = 'healthy';
            $issues = [];
            
            if ($memoryPercentage > $this->warningThresholds['memory_usage']) {
                $status = $memoryPercentage > 95 ? 'critical' : 'warning';
                $issues[] = "High memory usage: {$memoryPercentage}%";
            }

            return [
                'status' => $status,
                'memory_used' => $memoryUsage['used'],
                'memory_available' => $memoryUsage['available'],
                'memory_limit' => $phpMemoryLimit,
                'memory_percentage' => round($memoryPercentage, 2),
                'peak_memory' => $memoryUsage['peak'],
                'issues' => $issues,
                'last_checked' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error("Memory health check failed: " . $e->getMessage());
            
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'issues' => ['Memory check failed'],
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Check performance health
     */
    public function checkPerformanceHealth(): array
    {
        try {
            $responseTime = $this->getAverageResponseTime();
            $errorRate = $this->getErrorRate();
            $throughput = $this->getThroughput();
            
            $status = 'healthy';
            $issues = [];
            
            if ($responseTime > $this->warningThresholds['response_time']) {
                $status = $responseTime > 5000 ? 'critical' : 'warning';
                $issues[] = "High response time: {$responseTime}ms";
            }
            
            if ($errorRate > $this->warningThresholds['error_rate']) {
                $status = $errorRate > 10 ? 'critical' : 'warning';
                $issues[] = "High error rate: {$errorRate}%";
            }

            return [
                'status' => $status,
                'response_time' => $responseTime,
                'error_rate' => $errorRate,
                'throughput' => $throughput,
                'requests_per_minute' => $this->getRequestsPerMinute(),
                'slow_queries' => $this->getSlowQueries(),
                'issues' => $issues,
                'last_checked' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error("Performance health check failed: " . $e->getMessage());
            
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'issues' => ['Performance check failed'],
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Check external services health
     */
    public function checkExternalServicesHealth(): array
    {
        $services = [
            'mail' => $this->checkMailService(),
            'storage' => $this->checkStorageService(),
            'queue' => $this->checkQueueService(),
            'cache' => $this->checkCacheService(),
            'payment_gateway' => $this->checkPaymentGateway()
        ];

        $overallStatus = 'healthy';
        $issues = [];

        foreach ($services as $serviceName => $serviceStatus) {
            if ($serviceStatus['status'] === 'critical') {
                $overallStatus = 'critical';
                $issues[] = "{$serviceName}: {$serviceStatus['message']}";
            } elseif ($serviceStatus['status'] === 'warning' && $overallStatus === 'healthy') {
                $overallStatus = 'warning';
                $issues[] = "{$serviceName}: {$serviceStatus['message']}";
            }
        }

        return [
            'status' => $overallStatus,
            'services' => $services,
            'issues' => $issues,
            'last_checked' => now()->toISOString()
        ];
    }

    /**
     * Check security health
     */
    public function checkSecurityHealth(): array
    {
        try {
            $failedLogins = $this->getFailedLoginCount();
            $suspiciousActivity = $this->getSuspiciousActivityCount();
            $sslStatus = $this->checkSSLStatus();
            $firewallStatus = $this->checkFirewallStatus();
            
            $status = 'healthy';
            $issues = [];
            
            if ($failedLogins > 50) {
                $status = $failedLogins > 100 ? 'critical' : 'warning';
                $issues[] = "High failed login attempts: {$failedLogins}";
            }
            
            if ($suspiciousActivity > 10) {
                $status = $suspiciousActivity > 25 ? 'critical' : 'warning';
                $issues[] = "Suspicious activity detected: {$suspiciousActivity}";
            }
            
            if (!$sslStatus['valid']) {
                $status = 'warning';
                $issues[] = "SSL certificate issues: {$sslStatus['message']}";
            }

            return [
                'status' => $status,
                'failed_logins' => $failedLogins,
                'suspicious_activity' => $suspiciousActivity,
                'ssl_status' => $sslStatus,
                'firewall_status' => $firewallStatus,
                'last_security_scan' => $this->getLastSecurityScan(),
                'issues' => $issues,
                'last_checked' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error("Security health check failed: " . $e->getMessage());
            
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'issues' => ['Security check failed'],
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Check queue health
     */
    public function checkQueueHealth(): array
    {
        try {
            $queueSize = $this->getQueueSize();
            $failedJobs = $this->getFailedJobsCount();
            $processingTime = $this->getAverageProcessingTime();
            
            $status = 'healthy';
            $issues = [];
            
            if ($queueSize > $this->warningThresholds['queue_size']) {
                $status = $queueSize > 5000 ? 'critical' : 'warning';
                $issues[] = "Large queue size: {$queueSize} jobs";
            }
            
            if ($failedJobs > 10) {
                $status = $failedJobs > 50 ? 'critical' : 'warning';
                $issues[] = "High failed jobs count: {$failedJobs}";
            }

            return [
                'status' => $status,
                'queue_size' => $queueSize,
                'failed_jobs' => $failedJobs,
                'processing_time' => $processingTime,
                'worker_count' => $this->getWorkerCount(),
                'issues' => $issues,
                'last_checked' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error("Queue health check failed: " . $e->getMessage());
            
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'issues' => ['Queue check failed'],
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Check logs health
     */
    public function checkLogsHealth(): array
    {
        try {
            $logSize = $this->getLogSize();
            $errorCount = $this->getErrorLogCount();
            $warningCount = $this->getWarningLogCount();
            
            $status = 'healthy';
            $issues = [];
            
            if ($errorCount > 50) {
                $status = $errorCount > 100 ? 'critical' : 'warning';
                $issues[] = "High error count: {$errorCount}";
            }
            
            if ($logSize > 500 * 1024 * 1024) { // 500MB
                $status = 'warning';
                $issues[] = "Large log files: " . $this->formatBytes($logSize);
            }

            return [
                'status' => $status,
                'log_size' => $logSize,
                'error_count' => $errorCount,
                'warning_count' => $warningCount,
                'recent_errors' => $this->getRecentErrors(),
                'issues' => $issues,
                'last_checked' => now()->toISOString()
            ];

        } catch (Exception $e) {
            Log::error("Logs health check failed: " . $e->getMessage());
            
            return [
                'status' => 'critical',
                'error' => $e->getMessage(),
                'issues' => ['Logs check failed'],
                'last_checked' => now()->toISOString()
            ];
        }
    }

    /**
     * Get system uptime
     */
    protected function getSystemUptime(): array
    {
        try {
            $uptime = shell_exec('uptime');
            $bootTime = shell_exec('who -b');
            
            return [
                'uptime' => trim($uptime),
                'boot_time' => trim($bootTime),
                'formatted_uptime' => $this->formatUptime($uptime)
            ];
        } catch (Exception $e) {
            return [
                'uptime' => 'Unknown',
                'boot_time' => 'Unknown',
                'formatted_uptime' => 'Unknown'
            ];
        }
    }

    /**
     * Get version information
     */
    protected function getVersionInfo(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'app_version' => config('app.version', '1.0.0'),
            'environment' => app()->environment(),
            'debug_mode' => config('app.debug'),
            'timezone' => config('app.timezone')
        ];
    }

    /**
     * Get database size
     */
    protected function getDatabaseSize(): array
    {
        try {
            $databaseName = config('database.connections.mysql.database');
            $result = DB::select("
                SELECT 
                    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'size_mb'
                FROM information_schema.tables 
                WHERE table_schema = ?
            ", [$databaseName]);
            
            $sizeMB = $result[0]->size_mb ?? 0;
            
            return [
                'size_mb' => $sizeMB,
                'size_gb' => round($sizeMB / 1024, 2),
                'formatted' => $this->formatBytes($sizeMB * 1024 * 1024)
            ];
        } catch (Exception $e) {
            return [
                'size_mb' => 0,
                'size_gb' => 0,
                'formatted' => 'Unknown'
            ];
        }
    }

    /**
     * Get cache statistics
     */
    protected function getCacheStatistics(): array
    {
        try {
            // This would depend on your cache driver
            // For Redis, you could use Redis::info()
            // For Memcached, you could use similar methods
            
            return [
                'hit_rate' => 95.5, // Placeholder
                'memory_usage' => '64MB', // Placeholder
                'key_count' => 1250 // Placeholder
            ];
        } catch (Exception $e) {
            return [
                'hit_rate' => 0,
                'memory_usage' => 'Unknown',
                'key_count' => 0
            ];
        }
    }

    /**
     * Get disk usage
     */
    protected function getDiskUsage(): array
    {
        try {
            $total = disk_total_space('/');
            $free = disk_free_space('/');
            $used = $total - $free;
            $percentage = ($used / $total) * 100;
            
            return [
                'total' => $total,
                'free' => $free,
                'used' => $used,
                'percentage' => round($percentage, 2),
                'formatted_total' => $this->formatBytes($total),
                'formatted_free' => $this->formatBytes($free),
                'formatted_used' => $this->formatBytes($used)
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'free' => 0,
                'used' => 0,
                'percentage' => 0,
                'formatted_total' => 'Unknown',
                'formatted_free' => 'Unknown',
                'formatted_used' => 'Unknown'
            ];
        }
    }

    /**
     * Get memory usage
     */
    protected function getMemoryUsage(): array
    {
        return [
            'used' => memory_get_usage(true),
            'available' => memory_get_usage(false),
            'peak' => memory_get_peak_usage(true)
        ];
    }

    /**
     * Get PHP memory limit
     */
    protected function getPhpMemoryLimit(): int
    {
        $limit = ini_get('memory_limit');
        return $this->convertToBytes($limit);
    }

    /**
     * Format bytes to human readable
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, 2) . ' ' . $units[$pow];
    }

    /**
     * Convert memory limit string to bytes
     */
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

    /**
     * Clear system health cache
     */
    public function clearCache(): void
    {
        Cache::forget($this->cachePrefix . 'overall');
    }

    // Additional helper methods for specific checks
    protected function getLogSize(): int
    {
        try {
            $logPath = storage_path('logs');
            $size = 0;
            $files = glob($logPath . '/*.log');
            
            foreach ($files as $file) {
                $size += filesize($file);
            }
            
            return $size;
        } catch (Exception $e) {
            return 0;
        }
    }

    protected function getBackupSize(): int
    {
        try {
            $backupPath = storage_path('app/backups');
            if (!is_dir($backupPath)) {
                return 0;
            }
            
            $size = 0;
            $files = glob($backupPath . '/*');
            
            foreach ($files as $file) {
                $size += filesize($file);
            }
            
            return $size;
        } catch (Exception $e) {
            return 0;
        }
    }

    // Placeholder methods for various checks
    protected function getAverageResponseTime(): float { return 150.5; }
    protected function getErrorRate(): float { return 1.2; }
    protected function getThroughput(): int { return 1250; }
    protected function getRequestsPerMinute(): int { return 85; }
    protected function getSlowQueries(): int { return 3; }
    protected function getFailedLoginCount(): int { return 12; }
    protected function getSuspiciousActivityCount(): int { return 2; }
    protected function getQueueSize(): int { return 45; }
    protected function getFailedJobsCount(): int { return 3; }
    protected function getAverageProcessingTime(): float { return 250.5; }
    protected function getWorkerCount(): int { return 3; }
    protected function getErrorLogCount(): int { return 8; }
    protected function getWarningLogCount(): int { return 15; }

    protected function checkMailService(): array { return ['status' => 'healthy', 'message' => 'OK']; }
    protected function checkStorageService(): array { return ['status' => 'healthy', 'message' => 'OK']; }
    protected function checkQueueService(): array { return ['status' => 'healthy', 'message' => 'OK']; }
    protected function checkCacheService(): array { return ['status' => 'healthy', 'message' => 'OK']; }
    protected function checkPaymentGateway(): array { return ['status' => 'healthy', 'message' => 'OK']; }
    protected function checkSSLStatus(): array { return ['valid' => true, 'message' => 'Valid']; }
    protected function checkFirewallStatus(): array { return ['active' => true, 'message' => 'Active']; }
    protected function getLastSecurityScan(): string { return now()->subHours(2)->toISOString(); }
    protected function getRecentErrors(): array { return []; }
    protected function formatUptime(string $uptime): string { return '2 days, 5 hours'; }
}
