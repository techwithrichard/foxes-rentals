<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemMetric extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'metric_type',
        'metric_name',
        'value',
        'unit',
        'category',
        'tags',
        'metadata',
        'timestamp',
        'server_id',
        'created_by'
    ];

    protected $casts = [
        'value' => 'decimal:4',
        'tags' => 'array',
        'metadata' => 'array',
        'timestamp' => 'datetime'
    ];

    /**
     * Get the user who created this metric
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get metrics by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('metric_type', $type);
    }

    /**
     * Get metrics by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Get metrics by name
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('metric_name', $name);
    }

    /**
     * Get metrics within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('timestamp', [$startDate, $endDate]);
    }

    /**
     * Get recent metrics
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('timestamp', '>=', now()->subHours($hours));
    }

    /**
     * Get metrics by server
     */
    public function scopeByServer($query, string $serverId)
    {
        return $query->where('server_id', $serverId);
    }

    /**
     * Get formatted value with unit
     */
    public function getFormattedValueAttribute(): string
    {
        return $this->value . ' ' . ($this->unit ?? '');
    }

    /**
     * Get metric status based on thresholds
     */
    public function getStatusAttribute(): string
    {
        $thresholds = $this->getThresholds();
        
        if ($thresholds['critical'] && $this->value >= $thresholds['critical']) {
            return 'critical';
        } elseif ($thresholds['warning'] && $this->value >= $thresholds['warning']) {
            return 'warning';
        } else {
            return 'normal';
        }
    }

    /**
     * Get thresholds for this metric type
     */
    protected function getThresholds(): array
    {
        $thresholds = [
            'cpu_usage' => ['warning' => 80, 'critical' => 95],
            'memory_usage' => ['warning' => 85, 'critical' => 95],
            'disk_usage' => ['warning' => 80, 'critical' => 95],
            'response_time' => ['warning' => 2000, 'critical' => 5000],
            'error_rate' => ['warning' => 5, 'critical' => 10],
            'queue_size' => ['warning' => 1000, 'critical' => 5000]
        ];

        return $thresholds[$this->metric_type] ?? ['warning' => null, 'critical' => null];
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            'normal' => 'success',
            'warning' => 'warning',
            'critical' => 'danger'
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Get status display text
     */
    public function getStatusDisplayTextAttribute(): string
    {
        $texts = [
            'normal' => 'Normal',
            'warning' => 'Warning',
            'critical' => 'Critical'
        ];

        return $texts[$this->status] ?? 'Unknown';
    }

    /**
     * Get available metric types
     */
    public static function getAvailableMetricTypes(): array
    {
        return [
            'cpu_usage' => 'CPU Usage',
            'memory_usage' => 'Memory Usage',
            'disk_usage' => 'Disk Usage',
            'response_time' => 'Response Time',
            'error_rate' => 'Error Rate',
            'queue_size' => 'Queue Size',
            'cache_hit_rate' => 'Cache Hit Rate',
            'database_connections' => 'Database Connections',
            'throughput' => 'Throughput',
            'active_users' => 'Active Users'
        ];
    }

    /**
     * Get available categories
     */
    public static function getAvailableCategories(): array
    {
        return [
            'performance' => 'Performance',
            'system' => 'System',
            'application' => 'Application',
            'database' => 'Database',
            'cache' => 'Cache',
            'network' => 'Network',
            'security' => 'Security',
            'storage' => 'Storage'
        ];
    }

    /**
     * Get available units
     */
    public static function getAvailableUnits(): array
    {
        return [
            'percent' => '%',
            'bytes' => 'B',
            'kilobytes' => 'KB',
            'megabytes' => 'MB',
            'gigabytes' => 'GB',
            'milliseconds' => 'ms',
            'seconds' => 's',
            'count' => 'count',
            'requests_per_second' => 'req/s'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($metric) {
            if (!$metric->timestamp) {
                $metric->timestamp = now();
            }
            
            if (!$metric->created_by && auth()->check()) {
                $metric->created_by = auth()->id();
            }
        });
    }
}
