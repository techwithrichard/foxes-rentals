<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class AutomationRule extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'rule_type',
        'trigger_type',
        'trigger_conditions',
        'action_type',
        'action_parameters',
        'target_conditions',
        'is_active',
        'priority',
        'execution_count',
        'last_executed_at',
        'next_execution_at',
        'created_by'
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'action_parameters' => 'array',
        'target_conditions' => 'array',
        'is_active' => 'boolean',
        'last_executed_at' => 'datetime',
        'next_execution_at' => 'datetime',
        'priority' => 'integer',
        'execution_count' => 'integer'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user who created this rule
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the automation executions
     */
    public function executions(): HasMany
    {
        return $this->hasMany(AutomationExecution::class);
    }

    /**
     * Get the rule type display name
     */
    public function getRuleTypeDisplayNameAttribute(): string
    {
        $types = [
            'invoicing' => 'Invoicing',
            'reminders' => 'Reminders',
            'lease_management' => 'Lease Management',
            'property_maintenance' => 'Property Maintenance',
            'tenant_communication' => 'Tenant Communication',
            'payment_processing' => 'Payment Processing',
            'report_generation' => 'Report Generation',
            'data_sync' => 'Data Synchronization'
        ];

        return $types[$this->rule_type] ?? ucfirst(str_replace('_', ' ', $this->rule_type));
    }

    /**
     * Get the trigger type display name
     */
    public function getTriggerTypeDisplayNameAttribute(): string
    {
        $types = [
            'event_based' => 'Event Based',
            'schedule_based' => 'Schedule Based',
            'condition_based' => 'Condition Based',
            'webhook_based' => 'Webhook Based',
            'api_based' => 'API Based'
        ];

        return $types[$this->trigger_type] ?? ucfirst(str_replace('_', ' ', $this->trigger_type));
    }

    /**
     * Get the action type display name
     */
    public function getActionTypeDisplayNameAttribute(): string
    {
        $types = [
            'send_email' => 'Send Email',
            'send_sms' => 'Send SMS',
            'create_invoice' => 'Create Invoice',
            'update_status' => 'Update Status',
            'generate_report' => 'Generate Report',
            'webhook_call' => 'Webhook Call',
            'database_update' => 'Database Update',
            'file_generation' => 'File Generation',
            'notification_push' => 'Push Notification'
        ];

        return $types[$this->action_type] ?? ucfirst(str_replace('_', ' ', $this->action_type));
    }

    /**
     * Get the rule status
     */
    public function getStatusAttribute(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->last_executed_at && $this->last_executed_at->isToday()) {
            return 'executed_today';
        }

        if ($this->next_execution_at && $this->next_execution_at->isPast()) {
            return 'overdue';
        }

        return 'active';
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        $classes = [
            'active' => 'success',
            'inactive' => 'secondary',
            'executed_today' => 'info',
            'overdue' => 'warning'
        ];

        return $classes[$this->status] ?? 'secondary';
    }

    /**
     * Get the status display text
     */
    public function getStatusDisplayTextAttribute(): string
    {
        $texts = [
            'active' => 'Active',
            'inactive' => 'Inactive',
            'executed_today' => 'Executed Today',
            'overdue' => 'Overdue'
        ];

        return $texts[$this->status] ?? 'Unknown';
    }

    /**
     * Get the execution frequency
     */
    public function getExecutionFrequencyAttribute(): string
    {
        if (!$this->trigger_conditions) {
            return 'Manual';
        }

        $frequency = $this->trigger_conditions['frequency'] ?? null;
        
        if (!$frequency) {
            return 'Event-based';
        }

        $frequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'custom' => 'Custom Schedule'
        ];

        return $frequencies[$frequency] ?? ucfirst($frequency);
    }

    /**
     * Get the last execution status
     */
    public function getLastExecutionStatusAttribute(): string
    {
        $lastExecution = $this->executions()->latest()->first();
        
        if (!$lastExecution) {
            return 'never_executed';
        }

        return $lastExecution->status;
    }

    /**
     * Get the success rate
     */
    public function getSuccessRateAttribute(): float
    {
        $totalExecutions = $this->executions()->count();
        
        if ($totalExecutions === 0) {
            return 0;
        }

        $successfulExecutions = $this->executions()->where('status', 'completed')->count();
        
        return round(($successfulExecutions / $totalExecutions) * 100, 2);
    }

    /**
     * Get the average execution time
     */
    public function getAverageExecutionTimeAttribute(): float
    {
        $executions = $this->executions()->whereNotNull('execution_time_ms')->get();
        
        if ($executions->isEmpty()) {
            return 0;
        }

        $totalTime = $executions->sum('execution_time_ms');
        
        return round($totalTime / $executions->count(), 2);
    }

    /**
     * Scope to get active rules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get rules by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('rule_type', $type);
    }

    /**
     * Scope to get rules by trigger type
     */
    public function scopeByTriggerType($query, string $triggerType)
    {
        return $query->where('trigger_type', $triggerType);
    }

    /**
     * Scope to get rules by action type
     */
    public function scopeByActionType($query, string $actionType)
    {
        return $query->where('action_type', $actionType);
    }

    /**
     * Scope to get rules that need execution
     */
    public function scopeNeedsExecution($query)
    {
        return $query->where('is_active', true)
                    ->where(function ($q) {
                        $q->whereNull('next_execution_at')
                          ->orWhere('next_execution_at', '<=', now());
                    });
    }

    /**
     * Scope to get overdue rules
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_active', true)
                    ->where('next_execution_at', '<', now());
    }

    /**
     * Scope to get rules executed today
     */
    public function scopeExecutedToday($query)
    {
        return $query->whereDate('last_executed_at', today());
    }

    /**
     * Scope to get rules by priority
     */
    public function scopeByPriority($query, string $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope to get high priority rules
     */
    public function scopeHighPriority($query)
    {
        return $query->where('priority', '>=', 8);
    }

    /**
     * Scope to get medium priority rules
     */
    public function scopeMediumPriority($query)
    {
        return $query->whereBetween('priority', [4, 7]);
    }

    /**
     * Scope to get low priority rules
     */
    public function scopeLowPriority($query)
    {
        return $query->where('priority', '<=', 3);
    }

    /**
     * Get available rule types
     */
    public static function getAvailableRuleTypes(): array
    {
        return [
            'invoicing' => 'Invoicing',
            'reminders' => 'Reminders',
            'lease_management' => 'Lease Management',
            'property_maintenance' => 'Property Maintenance',
            'tenant_communication' => 'Tenant Communication',
            'payment_processing' => 'Payment Processing',
            'report_generation' => 'Report Generation',
            'data_sync' => 'Data Synchronization'
        ];
    }

    /**
     * Get available trigger types
     */
    public static function getAvailableTriggerTypes(): array
    {
        return [
            'event_based' => 'Event Based',
            'schedule_based' => 'Schedule Based',
            'condition_based' => 'Condition Based',
            'webhook_based' => 'Webhook Based',
            'api_based' => 'API Based'
        ];
    }

    /**
     * Get available action types
     */
    public static function getAvailableActionTypes(): array
    {
        return [
            'send_email' => 'Send Email',
            'send_sms' => 'Send SMS',
            'create_invoice' => 'Create Invoice',
            'update_status' => 'Update Status',
            'generate_report' => 'Generate Report',
            'webhook_call' => 'Webhook Call',
            'database_update' => 'Database Update',
            'file_generation' => 'File Generation',
            'notification_push' => 'Push Notification'
        ];
    }

    /**
     * Get available priorities
     */
    public static function getAvailablePriorities(): array
    {
        return [
            1 => 'Lowest (1)',
            2 => 'Very Low (2)',
            3 => 'Low (3)',
            4 => 'Below Average (4)',
            5 => 'Average (5)',
            6 => 'Above Average (6)',
            7 => 'High (7)',
            8 => 'Very High (8)',
            9 => 'Critical (9)',
            10 => 'Highest (10)'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($rule) {
            if (!$rule->created_by && auth()->check()) {
                $rule->created_by = auth()->id();
            }

            if (!$rule->priority) {
                $rule->priority = 5; // Default medium priority
            }
        });
    }
}
