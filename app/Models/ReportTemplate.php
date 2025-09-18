<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class ReportTemplate extends Model
{
    use HasUuids, HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'name',
        'description',
        'category',
        'report_type',
        'sections',
        'filters',
        'layout',
        'is_public',
        'is_active',
        'created_by'
    ];

    protected $casts = [
        'sections' => 'array',
        'filters' => 'array',
        'layout' => 'array',
        'is_public' => 'boolean',
        'is_active' => 'boolean'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user who created this template
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get scheduled reports using this template
     */
    public function scheduledReports(): HasMany
    {
        return $this->hasMany(ScheduledReport::class);
    }

    /**
     * Get the category display name
     */
    public function getCategoryDisplayNameAttribute(): string
    {
        $categories = [
            'financial' => 'Financial',
            'property' => 'Property',
            'tenant' => 'Tenant',
            'maintenance' => 'Maintenance',
            'occupancy' => 'Occupancy',
            'marketing' => 'Marketing',
            'operational' => 'Operational',
            'compliance' => 'Compliance',
            'custom' => 'Custom'
        ];

        return $categories[$this->category] ?? ucfirst($this->category);
    }

    /**
     * Get the report type display name
     */
    public function getReportTypeDisplayNameAttribute(): string
    {
        $types = [
            'property_report' => 'Property Report',
            'financial_report' => 'Financial Report',
            'tenant_report' => 'Tenant Report',
            'maintenance_report' => 'Maintenance Report',
            'occupancy_report' => 'Occupancy Report',
            'marketing_report' => 'Marketing Report',
            'operational_report' => 'Operational Report',
            'compliance_report' => 'Compliance Report',
            'custom_report' => 'Custom Report'
        ];

        return $types[$this->report_type] ?? ucfirst(str_replace('_', ' ', $this->report_type));
    }

    /**
     * Get the status badge class
     */
    public function getStatusBadgeClassAttribute(): string
    {
        return $this->is_active ? 'success' : 'secondary';
    }

    /**
     * Get the status display text
     */
    public function getStatusDisplayTextAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }

    /**
     * Get the visibility badge class
     */
    public function getVisibilityBadgeClassAttribute(): string
    {
        return $this->is_public ? 'primary' : 'info';
    }

    /**
     * Get the visibility display text
     */
    public function getVisibilityDisplayTextAttribute(): string
    {
        return $this->is_public ? 'Public' : 'Private';
    }

    /**
     * Get the sections count
     */
    public function getSectionsCountAttribute(): int
    {
        return count($this->sections ?? []);
    }

    /**
     * Get the filters count
     */
    public function getFiltersCountAttribute(): int
    {
        return count($this->filters ?? []);
    }

    /**
     * Get the usage count
     */
    public function getUsageCountAttribute(): int
    {
        return $this->scheduledReports()->count();
    }

    /**
     * Get template summary
     */
    public function getTemplateSummaryAttribute(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'category' => $this->category_display_name,
            'report_type' => $this->report_type_display_name,
            'sections_count' => $this->sections_count,
            'filters_count' => $this->filters_count,
            'usage_count' => $this->usage_count,
            'status' => $this->status_display_text,
            'visibility' => $this->visibility_display_text,
            'is_public' => $this->is_public,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

    /**
     * Scope to get public templates
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope to get private templates
     */
    public function scopePrivate($query)
    {
        return $query->where('is_public', false);
    }

    /**
     * Scope to get active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get templates by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get templates by report type
     */
    public function scopeByReportType($query, string $reportType)
    {
        return $query->where('report_type', $reportType);
    }

    /**
     * Scope to get templates by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('created_by', $userId)
              ->orWhere('is_public', true);
        });
    }

    /**
     * Scope to get templates with sections
     */
    public function scopeWithSections($query)
    {
        return $query->whereRaw('JSON_LENGTH(sections) > 0');
    }

    /**
     * Scope to get templates with filters
     */
    public function scopeWithFilters($query)
    {
        return $query->whereRaw('JSON_LENGTH(filters) > 0');
    }

    /**
     * Scope to get most used templates
     */
    public function scopeMostUsed($query, int $limit = 10)
    {
        return $query->withCount('scheduledReports')
                    ->orderByDesc('scheduled_reports_count')
                    ->limit($limit);
    }

    /**
     * Get available categories
     */
    public static function getAvailableCategories(): array
    {
        return [
            'financial' => 'Financial',
            'property' => 'Property',
            'tenant' => 'Tenant',
            'maintenance' => 'Maintenance',
            'occupancy' => 'Occupancy',
            'marketing' => 'Marketing',
            'operational' => 'Operational',
            'compliance' => 'Compliance',
            'custom' => 'Custom'
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
            'marketing_report' => 'Marketing Report',
            'operational_report' => 'Operational Report',
            'compliance_report' => 'Compliance Report',
            'custom_report' => 'Custom Report'
        ];
    }

    /**
     * Get available section types
     */
    public static function getAvailableSectionTypes(): array
    {
        return [
            'summary' => 'Summary',
            'chart' => 'Chart',
            'table' => 'Table',
            'metrics' => 'Metrics',
            'analysis' => 'Analysis',
            'recommendations' => 'Recommendations',
            'details' => 'Details',
            'trends' => 'Trends',
            'comparison' => 'Comparison',
            'forecast' => 'Forecast'
        ];
    }

    /**
     * Get available chart types
     */
    public static function getAvailableChartTypes(): array
    {
        return [
            'line' => 'Line Chart',
            'bar' => 'Bar Chart',
            'pie' => 'Pie Chart',
            'doughnut' => 'Doughnut Chart',
            'area' => 'Area Chart',
            'scatter' => 'Scatter Plot',
            'radar' => 'Radar Chart',
            'polar' => 'Polar Area Chart'
        ];
    }

    /**
     * Get available filter types
     */
    public static function getAvailableFilterTypes(): array
    {
        return [
            'date_range' => 'Date Range',
            'property_type' => 'Property Type',
            'location' => 'Location',
            'tenant' => 'Tenant',
            'status' => 'Status',
            'amount_range' => 'Amount Range',
            'category' => 'Category',
            'priority' => 'Priority'
        ];
    }

    /**
     * Duplicate template
     */
    public function duplicate(string $newName = null): self
    {
        $newTemplate = $this->replicate();
        $newTemplate->name = $newName ?? $this->name . ' (Copy)';
        $newTemplate->is_public = false;
        $newTemplate->created_by = auth()->id();
        $newTemplate->save();

        return $newTemplate;
    }

    /**
     * Clone template for user
     */
    public function cloneForUser(int $userId, string $newName = null): self
    {
        $newTemplate = $this->replicate();
        $newTemplate->name = $newName ?? $this->name . ' (Personal Copy)';
        $newTemplate->is_public = false;
        $newTemplate->created_by = $userId;
        $newTemplate->save();

        return $newTemplate;
    }

    /**
     * Validate template structure
     */
    public function validateStructure(): array
    {
        $errors = [];

        if (empty($this->sections)) {
            $errors[] = 'Template must have at least one section';
        }

        foreach ($this->sections as $index => $section) {
            if (empty($section['type'])) {
                $errors[] = "Section {$index} must have a type";
            }

            if (empty($section['key'])) {
                $errors[] = "Section {$index} must have a key";
            }
        }

        return $errors;
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (!$template->created_by && auth()->check()) {
                $template->created_by = auth()->id();
            }
        });
    }
}
