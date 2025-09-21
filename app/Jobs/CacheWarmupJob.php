<?php

namespace App\Jobs;

use App\Services\CacheService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CacheWarmupJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300;
    public $tries = 2;

    protected array $cacheTypes;

    /**
     * Create a new job instance.
     */
    public function __construct(array $cacheTypes = [])
    {
        $this->cacheTypes = $cacheTypes ?: ['all'];
    }

    /**
     * Execute the job.
     */
    public function handle(CacheService $cacheService): void
    {
        try {
            Log::info("Cache warmup job started", [
                'cache_types' => $this->cacheTypes
            ]);

            $startTime = microtime(true);

            if (in_array('all', $this->cacheTypes) || in_array('stats', $this->cacheTypes)) {
                $this->warmupStatistics($cacheService);
            }

            if (in_array('all', $this->cacheTypes) || in_array('dashboard', $this->cacheTypes)) {
                $this->warmupDashboard($cacheService);
            }

            if (in_array('all', $this->cacheTypes) || in_array('health', $this->cacheTypes)) {
                $this->warmupHealth($cacheService);
            }

            if (in_array('all', $this->cacheTypes) || in_array('lists', $this->cacheTypes)) {
                $this->warmupLists($cacheService);
            }

            $duration = microtime(true) - $startTime;

            Log::info("Cache warmup completed", [
                'duration' => round($duration, 2) . 's',
                'cache_types' => $this->cacheTypes
            ]);

        } catch (\Exception $e) {
            Log::error("Cache warmup failed", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Warm up statistics caches
     */
    private function warmupStatistics(CacheService $cacheService): void
    {
        Log::info("Warming up statistics caches");

        $cacheService->getPropertyStatistics();
        $cacheService->getUserStatistics();
        $cacheService->getLeaseStatistics();
        $cacheService->getPaymentStatistics();
    }

    /**
     * Warm up dashboard cache
     */
    private function warmupDashboard(CacheService $cacheService): void
    {
        Log::info("Warming up dashboard cache");

        $cacheService->getDashboardData();
    }

    /**
     * Warm up health check cache
     */
    private function warmupHealth(CacheService $cacheService): void
    {
        Log::info("Warming up health check cache");

        $cacheService->getSystemHealth();
    }

    /**
     * Warm up list caches
     */
    private function warmupLists(CacheService $cacheService): void
    {
        Log::info("Warming up list caches");

        // Warm up common filter combinations
        $commonFilters = [
            ['status' => 'active'],
            ['is_vacant' => true],
            ['is_vacant' => false],
            ['type' => 'house'],
            ['type' => 'apartment'],
        ];

        foreach ($commonFilters as $filters) {
            $cacheService->getPropertiesList($filters);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Cache warmup job failed permanently", [
            'error' => $exception->getMessage()
        ]);
    }
}
