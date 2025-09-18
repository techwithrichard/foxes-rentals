<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class ScheduledReport extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'report_type',
        'template_id',
        'filters',
        'schedule_frequency',
        'schedule_time',
        'recipients',
        'export_format',
        'is_active',
        'last_run_at',
        'next_run_at',
        'created_by'
    ];

    protected $casts = [
        'filters' => 'array',
        'recipients' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user who created this scheduled report
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the report template
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class);
    }

    /**
     * Get the frequency display name
     */
    public function getFrequencyDisplayNameAttribute(): string
    {
        $frequencies = [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly',
            'custom' => 'Custom'
        ];

        return $frequencies[$this->schedule_frequency] ?? ucfirst($this->schedule_frequency);
    }

    /**
     * Get the format display name
     */
    public function getFormatDisplayNameAttribute(): string
    {
        $formats = [
            'pdf' => 'PDF',
            'excel' => 'Excel',
            'csv' => 'CSV',
            'json' => 'JSON'
        ];

        return $formats[$this->export_format] ?? strtoupper($this->export_format);
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        if (!$this->is_active) {
            return 'secondary';
        }

        if ($this->next_run_at && $this->next_run_at->isPast()) {
            return 'warning';
        }

        return 'success';
    }

    /**
     * Get the status display text
     */
    public function getStatusDisplayTextAttribute(): string
    {
        if (!$this->is_active) {
            return 'Inactive';
        }

        if ($this->next_run_at && $this->next_run_at->isPast()) {
            return 'Overdue';
        }

        return 'Active';
    }

    /**
     * Get the next run time formatted
     */
    public function getNextRunTimeFormattedAttribute(): string
    {
        if (!$this->next_run_at) {
            return 'Not scheduled';
        }

        return $this->next_run_at->format('M j, Y g:i A');
    }

    /**
     * Get the last run time formatted
     */
    public function getLastRunTimeFormattedAttribute(): string
    {
        if (!$this->last_run_at) {
            return 'Never run';
        }

        return $this->last_run_at->format('M j, Y g:i A');
    }

    /**
     * Get the time until next run
     */
    public function getTimeUntilNextRunAttribute(): string
    {
        if (!$this->next_run_at) {
            return 'Not scheduled';
        }

        if ($this->next_run_at->isPast()) {
            return 'Overdue';
        }

        return $this->next_run_at->diffForHumans();
    }

    /**
     * Get the recipients count
     */
    public function getRecipientsCountAttribute(): int
    {
        return count($this->recipients ?? []);
    }

    /**
     * Get the recipients list
     */
    public function getRecipientsListAttribute(): string
    {
        if (empty($this->recipients)) {
            return 'No recipients';
        }

        return collect($this->recipients)->pluck('email')->join(', ');
    }

    /**
     * Get the report type display name
     */
    public function getReportTypeDisplayNameAttribute(): string
    {
        if ($this->template) {
            return $this->template->report_type_display_name;
        }

        $types = [
            'property_report' => 'Property Report',
            'financial_report' => 'Financial Report',
            'tenant_report' => 'Tenant Report',
            'maintenance_report' => 'Maintenance Report',
            'occupancy_report' => 'Occupancy Report',
            'custom_report' => 'Custom Report'
        ];

        return $types[$this->report_type] ?? ucfirst(str_replace('_', ' ', $this->report_type));
    }

    /**
     * Get the schedule summary
     */
    public function getScheduleSummaryAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'report_type' => $this->report_type_display_name,
            'template_name' => $this->template->name ?? 'Direct Report',
            'frequency' => $this->frequency_display_name,
            'format' => $this->format_display_name,
            'recipients_count' => $this->recipients_count,
            'status' => $this->status_display_text,
            'next_run' => $this->next_run_time_formatted,
            'last_run' => $this->last_run_time_formatted,
            'time_until_next' => $this->time_until_next_run,
            'is_active' => $this->is_active
        ];
    }

    /**
     * Check if report is overdue
     */
    public function isOverdue(): bool
    {
        return $this->is_active && 
               $this->next_run_at && 
               $this->next_run_at->isPast();
    }

    /**
     * Check if report is due to run
     */
    public function isDueToRun(): bool
    {
        return $this->is_active && 
               $this->next_run_at && 
               $this->next_run_at->lte(now());
    }

    /**
     * Get run history
     */
    public function getRunHistory(): array
    {
        // This would typically come from a report_runs table
        // For now, we'll return mock data
        return [
            [
                'run_at' => $this->last_run_at,
                'status' => 'completed',
                'recipients_notified' => $this->recipients_count,
                'file_size' => '2.5 MB'
            ]
        ];
    }

    /**
     * Calculate next run time
     */
    public function calculateNextRunTime(): Carbon
    {
        $time = Carbon::createFromTimeString($this->schedule_time);
        
        switch ($this->schedule_frequency) {
            case 'daily':
                return now()->addDay()->setTime($time->hour, $time->minute);
            case 'weekly':
                return now()->addWeek()->setTime($time->hour, $time->minute);
            case 'monthly':
                return now()->addMonth()->setTime($time->hour, $time->minute);
            case 'quarterly':
                return now()->addMonths(3)->setTime($time->hour, $time->minute);
            case 'yearly':
                return now()->addYear()->setTime($time->hour, $time->minute);
            default:
                return now()->addDay();
        }
    }

    /**
     * Update next run time
     */
    public function updateNextRunTime(): void
    {
        $this->update([
            'next_run_at' => $this->calculateNextRunTime()
        ]);
    }

    /**
     * Mark as run
     */
    public function markAsRun(): void
    {
        $this->update([
            'last_run_at' => now(),
            'next_run_at' => $this->calculateNextRunTime()
        ]);
    }

    /**
     * Scope to get active scheduled reports
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get overdue reports
     */
    public function scopeOverdue($query)
    {
        return $query->where('is_active', true)
                    ->where('next_run_at', '<', now());
    }

    /**
     * Scope to get due reports
     */
    public function scopeDue($query)
    {
        return $query->where('is_active', true)
                    ->where('next_run_at', '<=', now());
    }

    /**
     * Scope to get reports by frequency
     */
    public function scopeByFrequency($query, string $frequency)
    {
        return $query->where('schedule_frequency', $frequency);
    }

    /**
     * Scope to get reports by format
     */
    public function scopeByFormat($query, string $format)
    {
        return $query->where('export_format', $format);
    }

    /**
     * Scope to get reports by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Scope to get reports with templates
     */
    public function scopeWithTemplates($query)
    {
        return $query->whereNotNull('template_id');
    }

    /**
     * Scope to get reports without templates
     */
    public function scopeWithoutTemplates($query)
    {
        return $query->whereNull('template_id');
    }

    /**
     * Scope to get recently run reports
     */
    public function scopeRecentlyRun($query, int $hours = 24)
    {
        return $query->where('last_run_at', '>=', now()->subHours($hours));
    }

    /**
     * Get available frequencies
     */
    public static function getAvailableFrequencies(): array
    {
        return [
            'daily' => 'Daily',
            'weekly' => 'Weekly',
            'monthly' => 'Monthly',
            'quarterly' => 'Quarterly',
            'yearly' => 'Yearly'
        ];
    }

    /**
     * Get available formats
     */
    public static function getAvailableFormats(): array
    {
        return [
            'pdf' => 'PDF',
            'excel' => 'Excel',
            'csv' => 'CSV',
            'json' => 'JSON'
        ];
    }

    /**
     * Get available report types
     */
    public static function getAvailableReportTypes(): array
    {
        return [
            'property_report' => 'Property Report',
            'financial_report' => 'Financial Report',
            'tenant_report' => 'Tenant Report',
            'maintenance_report' => 'Maintenance Report',
            'occupancy_report' => 'Occupancy Report',
            'custom_report' => 'Custom Report'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($scheduledReport) {
            if (!$scheduledReport->created_by && auth()->check()) {
                $scheduledReport->created_by = auth()->id();
            }

            if (!$scheduledReport->next_run_at) {
                $scheduledReport->next_run_at = $scheduledReport->calculateNextRunTime();
            }
        });
    }
}
