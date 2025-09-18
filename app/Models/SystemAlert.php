<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemAlert extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'alert_type',
        'severity',
        'title',
        'message',
        'source',
        'metric_name',
        'threshold_value',
        'actual_value',
        'status',
        'acknowledged_at',
        'acknowledged_by',
        'resolved_at',
        'resolved_by',
        'metadata',
        'tags',
        'created_by'
    ];

    protected $casts = [
        'threshold_value' => 'decimal:4',
        'actual_value' => 'decimal:4',
        'acknowledged_at' => 'datetime',
        'resolved_at' => 'datetime',
        'metadata' => 'array',
        'tags' => 'array'
    ];

    /**
     * Get the user who created this alert
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who acknowledged this alert
     */
    public function acknowledger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Get the user who resolved this alert
     */
    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get the severity display name
     */
    public function getSeverityDisplayNameAttribute(): string
    {
        $severities = [
            'info' => 'Info',
            'warning' => 'Warning',
            'error' => 'Error',
            'critical' => 'Critical'
        ];

        return $severities[$this->severity] ?? ucfirst($this->severity);
    }

    /**
     * Get the severity badge class
     */
    public function getSeverityBadgeClassAttribute(): string
    {
        $classes = [
            'info' => 'info',
            'warning' => 'warning',
            'error' => 'danger',
            'critical' => 'dark'
        ];

        return $classes[$this->severity] ?? 'secondary';
    }

    /**
     * Get the status display name
     */
    public function getStatusDisplayNameAttribute(): string
    {
        $statuses = [
            'active' => 'Active',
            'acknowledged' => 'Acknowledged',
            'resolved' => 'Resolved',
            'suppressed' => 'Suppressed'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            'active' => 'danger',
            'acknowledged' => 'warning',
            'resolved' => 'success',
            'suppressed' => 'secondary'
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Get the alert type display name
     */
    public function getAlertTypeDisplayNameAttribute(): string
    {
        $types = [
            'threshold_exceeded' => 'Threshold Exceeded',
            'service_down' => 'Service Down',
            'performance_degradation' => 'Performance Degradation',
            'security_incident' => 'Security Incident',
            'resource_exhaustion' => 'Resource Exhaustion',
            'configuration_error' => 'Configuration Error',
            'connectivity_issue' => 'Connectivity Issue',
            'data_integrity' => 'Data Integrity',
            'backup_failure' => 'Backup Failure',
            'license_expiry' => 'License Expiry'
        ];

        return $types[$this->alert_type] ?? ucfirst(str_replace('_', ' ', $this->alert_type));
    }

    /**
     * Get the alert icon
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'threshold_exceeded' => 'ni-chart-bar',
            'service_down' => 'ni-server',
            'performance_degradation' => 'ni-speedometer',
            'security_incident' => 'ni-shield-alert',
            'resource_exhaustion' => 'ni-memory',
            'configuration_error' => 'ni-setting',
            'connectivity_issue' => 'ni-signal',
            'data_integrity' => 'ni-database',
            'backup_failure' => 'ni-archive',
            'license_expiry' => 'ni-calendar'
        ];

        return $icons[$this->alert_type] ?? 'ni-alert-circle';
    }

    /**
     * Get the alert color
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'threshold_exceeded' => 'warning',
            'service_down' => 'danger',
            'performance_degradation' => 'warning',
            'security_incident' => 'danger',
            'resource_exhaustion' => 'warning',
            'configuration_error' => 'info',
            'connectivity_issue' => 'warning',
            'data_integrity' => 'danger',
            'backup_failure' => 'warning',
            'license_expiry' => 'info'
        ];

        return $colors[$this->alert_type] ?? 'secondary';
    }

    /**
     * Get the duration since alert was created
     */
    public function getDurationAttribute(): string
    {
        if ($this->status === 'resolved' && $this->resolved_at) {
            return $this->created_at->diffForHumans($this->resolved_at, true);
        }
        
        return $this->created_at->diffForHumans();
    }

    /**
     * Get the time to acknowledge
     */
    public function getTimeToAcknowledgeAttribute(): ?string
    {
        if ($this->acknowledged_at) {
            return $this->created_at->diffForHumans($this->acknowledged_at, true);
        }
        
        return null;
    }

    /**
     * Get the time to resolve
     */
    public function getTimeToResolveAttribute(): ?string
    {
        if ($this->resolved_at) {
            $startTime = $this->acknowledged_at ?? $this->created_at;
            return $startTime->diffForHumans($this->resolved_at, true);
        }
        
        return null;
    }

    /**
     * Check if alert is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if alert is acknowledged
     */
    public function isAcknowledged(): bool
    {
        return $this->status === 'acknowledged';
    }

    /**
     * Check if alert is resolved
     */
    public function isResolved(): bool
    {
        return $this->status === 'resolved';
    }

    /**
     * Check if alert is suppressed
     */
    public function isSuppressed(): bool
    {
        return $this->status === 'suppressed';
    }

    /**
     * Acknowledge the alert
     */
    public function acknowledge(?int $userId = null): bool
    {
        if ($this->status !== 'active') {
            return false;
        }

        $this->update([
            'status' => 'acknowledged',
            'acknowledged_at' => now(),
            'acknowledged_by' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Resolve the alert
     */
    public function resolve(?int $userId = null): bool
    {
        if (in_array($this->status, ['resolved', 'suppressed'])) {
            return false;
        }

        $this->update([
            'status' => 'resolved',
            'resolved_at' => now(),
            'resolved_by' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Suppress the alert
     */
    public function suppress(?int $userId = null): bool
    {
        if ($this->status === 'suppressed') {
            return false;
        }

        $this->update([
            'status' => 'suppressed',
            'resolved_at' => now(),
            'resolved_by' => $userId ?? auth()->id()
        ]);

        return true;
    }

    /**
     * Scope to get alerts by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('alert_type', $type);
    }

    /**
     * Scope to get alerts by severity
     */
    public function scopeBySeverity($query, string $severity)
    {
        return $query->where('severity', $severity);
    }

    /**
     * Scope to get alerts by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get active alerts
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get acknowledged alerts
     */
    public function scopeAcknowledged($query)
    {
        return $query->where('status', 'acknowledged');
    }

    /**
     * Scope to get resolved alerts
     */
    public function scopeResolved($query)
    {
        return $query->where('status', 'resolved');
    }

    /**
     * Scope to get critical alerts
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope to get alerts within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent alerts
     */
    public function scopeRecent($query, int $hours = 24)
    {
        return $query->where('created_at', '>=', now()->subHours($hours));
    }

    /**
     * Scope to get alerts by source
     */
    public function scopeBySource($query, string $source)
    {
        return $query->where('source', $source);
    }

    /**
     * Get available alert types
     */
    public static function getAvailableAlertTypes(): array
    {
        return [
            'threshold_exceeded' => 'Threshold Exceeded',
            'service_down' => 'Service Down',
            'performance_degradation' => 'Performance Degradation',
            'security_incident' => 'Security Incident',
            'resource_exhaustion' => 'Resource Exhaustion',
            'configuration_error' => 'Configuration Error',
            'connectivity_issue' => 'Connectivity Issue',
            'data_integrity' => 'Data Integrity',
            'backup_failure' => 'Backup Failure',
            'license_expiry' => 'License Expiry'
        ];
    }

    /**
     * Get available severities
     */
    public static function getAvailableSeverities(): array
    {
        return [
            'info' => 'Info',
            'warning' => 'Warning',
            'error' => 'Error',
            'critical' => 'Critical'
        ];
    }

    /**
     * Get available statuses
     */
    public static function getAvailableStatuses(): array
    {
        return [
            'active' => 'Active',
            'acknowledged' => 'Acknowledged',
            'resolved' => 'Resolved',
            'suppressed' => 'Suppressed'
        ];
    }

    /**
     * Get available sources
     */
    public static function getAvailableSources(): array
    {
        return [
            'system_monitor' => 'System Monitor',
            'performance_monitor' => 'Performance Monitor',
            'security_monitor' => 'Security Monitor',
            'database_monitor' => 'Database Monitor',
            'cache_monitor' => 'Cache Monitor',
            'queue_monitor' => 'Queue Monitor',
            'storage_monitor' => 'Storage Monitor',
            'network_monitor' => 'Network Monitor',
            'application_monitor' => 'Application Monitor',
            'external_service' => 'External Service'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($alert) {
            if (!$alert->created_by && auth()->check()) {
                $alert->created_by = auth()->id();
            }

            if (!$alert->status) {
                $alert->status = 'active';
            }
        });
    }
}
