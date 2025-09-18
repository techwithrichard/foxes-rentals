<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AutomationExecution extends Model
{
    use HasUuids, HasFactory, SoftDeletes;

    protected $fillable = [
        'automation_rule_id',
        'status',
        'started_at',
        'completed_at',
        'execution_time_ms',
        'trigger_data',
        'action_data',
        'error_message',
        'execution_log',
        'affected_records_count',
        'created_by'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'trigger_data' => 'array',
        'action_data' => 'array',
        'execution_log' => 'array',
        'execution_time_ms' => 'integer',
        'affected_records_count' => 'integer'
    ];

    /**
     * Get the automation rule that was executed
     */
    public function automationRule(): BelongsTo
    {
        return $this->belongsTo(AutomationRule::class);
    }

    /**
     * Get the user who triggered the execution
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the status display name
     */
    public function getStatusDisplayNameAttribute(): string
    {
        $statuses = [
            'pending' => 'Pending',
            'running' => 'Running',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'timeout' => 'Timeout'
        ];

        return $statuses[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            'pending' => 'warning',
            'running' => 'info',
            'completed' => 'success',
            'failed' => 'danger',
            'cancelled' => 'secondary',
            'timeout' => 'dark'
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Get the execution duration
     */
    public function getExecutionDurationAttribute(): string
    {
        if (!$this->started_at || !$this->completed_at) {
            return 'N/A';
        }

        $duration = $this->completed_at->diffInMilliseconds($this->started_at);
        
        if ($duration < 1000) {
            return $duration . 'ms';
        } elseif ($duration < 60000) {
            return round($duration / 1000, 2) . 's';
        } else {
            $minutes = floor($duration / 60000);
            $seconds = round(($duration % 60000) / 1000);
            return $minutes . 'm ' . $seconds . 's';
        }
    }

    /**
     * Get the execution duration in human readable format
     */
    public function getExecutionDurationHumanAttribute(): string
    {
        if (!$this->started_at || !$this->completed_at) {
            return 'Not completed';
        }

        return $this->completed_at->diffForHumans($this->started_at, true);
    }

    /**
     * Get the execution summary
     */
    public function getExecutionSummaryAttribute(): array
    {
        return [
            'id' => $this->id,
            'rule_name' => $this->automationRule->name ?? 'Unknown Rule',
            'status' => $this->status,
            'status_display' => $this->status_display_name,
            'status_class' => $this->status_badge_class,
            'started_at' => $this->started_at,
            'completed_at' => $this->completed_at,
            'duration' => $this->execution_duration,
            'duration_human' => $this->execution_duration_human,
            'affected_records' => $this->affected_records_count ?? 0,
            'error_message' => $this->error_message,
            'has_error' => !empty($this->error_message),
            'execution_time_ms' => $this->execution_time_ms
        ];
    }

    /**
     * Get the trigger summary
     */
    public function getTriggerSummaryAttribute(): array
    {
        return [
            'trigger_type' => $this->automationRule->trigger_type ?? 'Unknown',
            'trigger_data' => $this->trigger_data ?? [],
            'triggered_at' => $this->started_at,
            'triggered_by' => $this->creator->name ?? 'System'
        ];
    }

    /**
     * Get the action summary
     */
    public function getActionSummaryAttribute(): array
    {
        return [
            'action_type' => $this->automationRule->action_type ?? 'Unknown',
            'action_data' => $this->action_data ?? [],
            'affected_records' => $this->affected_records_count ?? 0,
            'execution_log' => $this->execution_log ?? []
        ];
    }

    /**
     * Check if execution is successful
     */
    public function isSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if execution failed
     */
    public function isFailed(): bool
    {
        return in_array($this->status, ['failed', 'cancelled', 'timeout']);
    }

    /**
     * Check if execution is running
     */
    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    /**
     * Check if execution is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Get the error summary
     */
    public function getErrorSummaryAttribute(): ?array
    {
        if (!$this->error_message) {
            return null;
        }

        return [
            'message' => $this->error_message,
            'type' => $this->getErrorType(),
            'severity' => $this->getErrorSeverity(),
            'timestamp' => $this->completed_at ?? $this->started_at
        ];
    }

    /**
     * Get error type
     */
    protected function getErrorType(): string
    {
        $message = strtolower($this->error_message);
        
        if (str_contains($message, 'timeout')) {
            return 'timeout';
        } elseif (str_contains($message, 'permission')) {
            return 'permission';
        } elseif (str_contains($message, 'validation')) {
            return 'validation';
        } elseif (str_contains($message, 'connection')) {
            return 'connection';
        } elseif (str_contains($message, 'not found')) {
            return 'not_found';
        } else {
            return 'unknown';
        }
    }

    /**
     * Get error severity
     */
    protected function getErrorSeverity(): string
    {
        $message = strtolower($this->error_message);
        
        if (str_contains($message, 'critical') || str_contains($message, 'fatal')) {
            return 'critical';
        } elseif (str_contains($message, 'error')) {
            return 'high';
        } elseif (str_contains($message, 'warning')) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Scope to get executions by status
     */
    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get successful executions
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed executions
     */
    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'cancelled', 'timeout']);
    }

    /**
     * Scope to get running executions
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Scope to get pending executions
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope to get executions for a specific rule
     */
    public function scopeForRule($query, string $ruleId)
    {
        return $query->where('automation_rule_id', $ruleId);
    }

    /**
     * Scope to get executions within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent executions
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('started_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get executions with errors
     */
    public function scopeWithErrors($query)
    {
        return $query->whereNotNull('error_message');
    }

    /**
     * Scope to get executions without errors
     */
    public function scopeWithoutErrors($query)
    {
        return $query->whereNull('error_message');
    }

    /**
     * Scope to get long running executions
     */
    public function scopeLongRunning($query, int $thresholdMs = 30000)
    {
        return $query->where('execution_time_ms', '>', $thresholdMs);
    }

    /**
     * Scope to get fast executions
     */
    public function scopeFast($query, int $thresholdMs = 1000)
    {
        return $query->where('execution_time_ms', '<', $thresholdMs);
    }

    /**
     * Get available statuses
     */
    public static function getAvailableStatuses(): array
    {
        return [
            'pending' => 'Pending',
            'running' => 'Running',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'cancelled' => 'Cancelled',
            'timeout' => 'Timeout'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($execution) {
            if (!$execution->created_by && auth()->check()) {
                $execution->created_by = auth()->id();
            }

            if (!$execution->started_at) {
                $execution->started_at = now();
            }
        });
    }
}
