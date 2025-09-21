<?php

namespace App\Console\Commands;

use App\Services\CacheService;
use App\Services\PerformanceMonitoringService;
use App\Services\QueryOptimizationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class OptimizePerformanceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'foxes:optimize-performance 
                            {--cache : Warm up caches}
                            {--database : Optimize database}
                            {--assets : Optimize assets}
                            {--all : Run all optimizations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize Foxes Rentals performance';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting Foxes Rentals Performance Optimization...');
        $this->newLine();

        $startTime = microtime(true);

        try {
            if ($this->option('all') || $this->option('cache')) {
                $this->optimizeCaching();
            }

            if ($this->option('all') || $this->option('database')) {
                $this->optimizeDatabase();
            }

            if ($this->option('all') || $this->option('assets')) {
                $this->optimizeAssets();
            }

            $duration = microtime(true) - $startTime;

            $this->newLine();
            $this->info("✅ Performance optimization completed in " . round($duration, 2) . " seconds");
            
            // Show performance report
            $this->showPerformanceReport();

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("❌ Optimization failed: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    /**
     * Optimize caching
     */
    private function optimizeCaching(): void
    {
        $this->info('📦 Optimizing caching...');

        $cacheService = app(CacheService::class);

        // Clear existing caches
        $this->line('  Clearing existing caches...');
        $cacheService->clearAllCaches();

        // Warm up caches
        $this->line('  Warming up caches...');
        $cacheService->warmUpCaches();

        // Optimize cache configuration
        $this->line('  Optimizing cache configuration...');
        $this->optimizeCacheConfig();

        $this->info('  ✅ Caching optimization completed');
    }

    /**
     * Optimize database
     */
    private function optimizeDatabase(): void
    {
        $this->info('🗄️ Optimizing database...');

        // Analyze tables
        $this->line('  Analyzing database tables...');
        $this->analyzeTables();

        // Optimize tables
        $this->line('  Optimizing database tables...');
        $this->optimizeTables();

        // Update table statistics
        $this->line('  Updating table statistics...');
        $this->updateTableStatistics();

        // Check for missing indexes
        $this->line('  Checking for missing indexes...');
        $this->checkMissingIndexes();

        $this->info('  ✅ Database optimization completed');
    }

    /**
     * Optimize assets
     */
    private function optimizeAssets(): void
    {
        $this->info('🎨 Optimizing assets...');

        // Clear view cache
        $this->line('  Clearing view cache...');
        Artisan::call('view:clear');

        // Clear route cache
        $this->line('  Clearing route cache...');
        Artisan::call('route:clear');

        // Clear config cache
        $this->line('  Clearing config cache...');
        Artisan::call('config:clear');

        // Optimize autoloader
        $this->line('  Optimizing autoloader...');
        Artisan::call('optimize:clear');
        Artisan::call('optimize');

        // Build assets
        $this->line('  Building assets...');
        $this->buildAssets();

        $this->info('  ✅ Asset optimization completed');
    }

    /**
     * Optimize cache configuration
     */
    private function optimizeCacheConfig(): void
    {
        // Set optimal cache settings
        $config = [
            'cache.default' => 'redis',
            'cache.stores.redis.options.prefix' => 'foxes_rentals:',
            'cache.stores.redis.options.serializer' => 'php',
        ];

        foreach ($config as $key => $value) {
            config([$key => $value]);
        }
    }

    /**
     * Analyze database tables
     */
    private function analyzeTables(): void
    {
        $tables = [
            'properties', 'users', 'leases', 'payments', 'houses',
            'bills', 'addresses', 'property_images', 'lease_documents'
        ];

        foreach ($tables as $table) {
            try {
                DB::statement("ANALYZE TABLE {$table}");
                $this->line("    Analyzed table: {$table}");
            } catch (\Exception $e) {
                $this->warn("    Failed to analyze table {$table}: " . $e->getMessage());
            }
        }
    }

    /**
     * Optimize database tables
     */
    private function optimizeTables(): void
    {
        $tables = [
            'properties', 'users', 'leases', 'payments', 'houses',
            'bills', 'addresses', 'property_images', 'lease_documents'
        ];

        foreach ($tables as $table) {
            try {
                DB::statement("OPTIMIZE TABLE {$table}");
                $this->line("    Optimized table: {$table}");
            } catch (\Exception $e) {
                $this->warn("    Failed to optimize table {$table}: " . $e->getMessage());
            }
        }
    }

    /**
     * Update table statistics
     */
    private function updateTableStatistics(): void
    {
        try {
            DB::statement("FLUSH TABLES");
            $this->line("    Updated table statistics");
        } catch (\Exception $e) {
            $this->warn("    Failed to update table statistics: " . $e->getMessage());
        }
    }

    /**
     * Check for missing indexes
     */
    private function checkMissingIndexes(): void
    {
        $criticalIndexes = [
            'properties' => ['landlord_id', 'status', 'is_vacant', 'type'],
            'users' => ['email', 'is_active', 'created_at'],
            'leases' => ['tenant_id', 'property_id', 'status', 'end_date'],
            'payments' => ['tenant_id', 'property_id', 'status', 'paid_at'],
            'houses' => ['property_id', 'is_vacant', 'status'],
        ];

        foreach ($criticalIndexes as $table => $columns) {
            foreach ($columns as $column) {
                try {
                    $indexExists = DB::select("
                        SELECT COUNT(*) as count 
                        FROM information_schema.statistics 
                        WHERE table_schema = DATABASE() 
                        AND table_name = '{$table}' 
                        AND column_name = '{$column}'
                    ")[0]->count;

                    if ($indexExists == 0) {
                        $this->warn("    Missing index on {$table}.{$column}");
                    } else {
                        $this->line("    Index exists on {$table}.{$column}");
                    }
                } catch (\Exception $e) {
                    $this->warn("    Failed to check index on {$table}.{$column}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Build assets
     */
    private function buildAssets(): void
    {
        try {
            // Run npm build if package.json exists
            if (file_exists(base_path('package.json'))) {
                $this->line('    Running npm build...');
                exec('npm run build 2>&1', $output, $returnCode);
                
                if ($returnCode === 0) {
                    $this->line('    ✅ Assets built successfully');
                } else {
                    $this->warn('    ⚠️ Asset build had issues: ' . implode("\n", $output));
                }
            } else {
                $this->line('    No package.json found, skipping asset build');
            }
        } catch (\Exception $e) {
            $this->warn("    Failed to build assets: " . $e->getMessage());
        }
    }

    /**
     * Show performance report
     */
    private function showPerformanceReport(): void
    {
        $this->newLine();
        $this->info('📊 Performance Report:');
        $this->newLine();

        $monitoringService = app(PerformanceMonitoringService::class);
        $report = $monitoringService->getPerformanceReport();

        // Database metrics
        $this->line('🗄️ Database Performance:');
        $this->line("  Connection Time: {$report['database']['connection_time']}ms");
        $this->line("  Query Time: {$report['database']['query_time']}ms");
        $this->line("  Slow Queries: " . count($report['database']['slow_queries']));

        // Cache metrics
        $this->line('📦 Cache Performance:');
        $this->line("  Write Time: {$report['cache']['write_time']}ms");
        $this->line("  Read Time: {$report['cache']['read_time']}ms");

        // Memory metrics
        $this->line('💾 Memory Usage:');
        $this->line("  Current: " . $this->formatBytes($report['memory']['current_memory']));
        $this->line("  Peak: " . $this->formatBytes($report['memory']['peak_memory']));
        $this->line("  Usage: {$report['memory']['memory_usage_percentage']}%");

        // Storage metrics
        $this->line('💿 Storage:');
        $this->line("  Used: {$report['storage']['storage_space']['used']}");
        $this->line("  Free: {$report['storage']['storage_space']['free']}");
        $this->line("  Usage: {$report['storage']['storage_space']['percentage']}%");

        $this->newLine();
        $this->info("Report generated in {$report['generation_time']}ms");
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
