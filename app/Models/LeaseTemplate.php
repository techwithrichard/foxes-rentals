<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LeaseTemplate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'description',
        'template_type',
        'content',
        'terms',
        'variables',
        'is_active',
        'sort_order'
    ];

    protected $casts = [
        'terms' => 'array',
        'variables' => 'array',
        'is_active' => 'boolean',
        'sort_order' => 'integer'
    ];

    /**
     * Get leases that use this template
     */
    public function leases(): HasMany
    {
        return $this->hasMany(LeaseAgreement::class, 'template_id');
    }

    /**
     * Scope to get active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get templates by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('template_type', $type);
    }

    /**
     * Scope to order by type and sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('template_type')->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the template type display name
     */
    public function getTemplateTypeDisplayNameAttribute(): string
    {
        $types = [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'short_term' => 'Short Term',
            'long_term' => 'Long Term',
            'monthly' => 'Monthly',
            'weekly' => 'Weekly',
            'daily' => 'Daily',
            'vacation' => 'Vacation Rental',
            'student' => 'Student Housing',
            'senior' => 'Senior Housing'
        ];

        return $types[$this->template_type] ?? ucfirst(str_replace('_', ' ', $this->template_type));
    }

    /**
     * Get the icon for the template type
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'residential' => 'ni-home',
            'commercial' => 'ni-building',
            'short_term' => 'ni-calendar',
            'long_term' => 'ni-calendar-date',
            'monthly' => 'ni-calendar-month',
            'weekly' => 'ni-calendar-week',
            'daily' => 'ni-calendar-day',
            'vacation' => 'ni-hotel',
            'student' => 'ni-graduation',
            'senior' => 'ni-user-check'
        ];

        return $icons[$this->template_type] ?? 'ni-file-text';
    }

    /**
     * Get the preview content (first 200 characters)
     */
    public function getPreviewAttribute(): string
    {
        $content = strip_tags($this->content);
        return strlen($content) > 200 ? substr($content, 0, 200) . '...' : $content;
    }

    /**
     * Get the word count of the content
     */
    public function getWordCountAttribute(): int
    {
        $content = strip_tags($this->content);
        return str_word_count($content);
    }

    /**
     * Get the character count of the content
     */
    public function getCharacterCountAttribute(): int
    {
        $content = strip_tags($this->content);
        return strlen($content);
    }

    /**
     * Get the estimated reading time in minutes
     */
    public function getReadingTimeAttribute(): int
    {
        $wordsPerMinute = 200; // Average reading speed
        return max(1, round($this->word_count / $wordsPerMinute));
    }

    /**
     * Get usage statistics
     */
    public function getUsageStatsAttribute(): array
    {
        return [
            'total_leases' => $this->leases()->count(),
            'active_leases' => $this->leases()->where('status', 'active')->count(),
            'expired_leases' => $this->leases()->where('status', 'expired')->count(),
            'last_used' => $this->leases()->latest()->first()?->created_at,
            'average_lease_duration' => $this->leases()->avg('lease_duration_months') ?? 0
        ];
    }

    /**
     * Process template with variables
     */
    public function processTemplate(array $variables = []): string
    {
        $content = $this->content;
        
        // Merge with default variables
        $allVariables = array_merge($this->variables ?? [], $variables);
        
        // Replace variables in the format {{variable_name}}
        foreach ($allVariables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
            $content = str_replace("{{ $key }}", $value, $content);
        }
        
        // Replace common system variables
        $systemVariables = [
            'company_name' => setting('app.name', 'Foxes Rental Systems'),
            'company_address' => setting('company.address', ''),
            'company_phone' => setting('company.phone', ''),
            'company_email' => setting('company.email', ''),
            'current_date' => now()->format('F j, Y'),
            'current_year' => now()->year,
            'signature_date' => now()->format('Y-m-d')
        ];
        
        foreach ($systemVariables as $key => $value) {
            $content = str_replace("{{$key}}", $value, $content);
            $content = str_replace("{{ $key }}", $value, $content);
        }
        
        return $content;
    }

    /**
     * Get available variables from template content
     */
    public function getAvailableVariables(): array
    {
        $content = $this->content;
        preg_match_all('/\{\{([^}]+)\}\}/', $content, $matches);
        
        $variables = [];
        if (!empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $variable = trim($match);
                $variables[$variable] = [
                    'name' => $variable,
                    'description' => $this->getVariableDescription($variable),
                    'type' => $this->getVariableType($variable),
                    'required' => $this->isVariableRequired($variable)
                ];
            }
        }
        
        return array_unique($variables, SORT_REGULAR);
    }

    /**
     * Get variable description
     */
    protected function getVariableDescription(string $variable): string
    {
        $descriptions = [
            'tenant_name' => 'Full name of the tenant',
            'tenant_email' => 'Email address of the tenant',
            'tenant_phone' => 'Phone number of the tenant',
            'landlord_name' => 'Full name of the landlord',
            'landlord_email' => 'Email address of the landlord',
            'landlord_phone' => 'Phone number of the landlord',
            'property_address' => 'Full address of the property',
            'rent_amount' => 'Monthly rent amount',
            'deposit_amount' => 'Security deposit amount',
            'lease_start_date' => 'Lease start date',
            'lease_end_date' => 'Lease end date',
            'lease_duration' => 'Duration of the lease in months',
            'late_fee' => 'Late payment fee amount',
            'pet_deposit' => 'Pet deposit amount if applicable',
            'utilities_included' => 'List of utilities included in rent'
        ];

        return $descriptions[$variable] ?? ucfirst(str_replace('_', ' ', $variable));
    }

    /**
     * Get variable type
     */
    protected function getVariableType(string $variable): string
    {
        $types = [
            'tenant_name' => 'text',
            'tenant_email' => 'email',
            'tenant_phone' => 'tel',
            'landlord_name' => 'text',
            'landlord_email' => 'email',
            'landlord_phone' => 'tel',
            'property_address' => 'text',
            'rent_amount' => 'number',
            'deposit_amount' => 'number',
            'lease_start_date' => 'date',
            'lease_end_date' => 'date',
            'lease_duration' => 'number',
            'late_fee' => 'number',
            'pet_deposit' => 'number'
        ];

        return $types[$variable] ?? 'text';
    }

    /**
     * Check if variable is required
     */
    protected function isVariableRequired(string $variable): bool
    {
        $required = [
            'tenant_name', 'landlord_name', 'property_address', 
            'rent_amount', 'lease_start_date', 'lease_end_date'
        ];

        return in_array($variable, $required);
    }

    /**
     * Validate template content
     */
    public function validateContent(): array
    {
        $errors = [];
        
        // Check for required sections
        $requiredSections = ['tenant', 'landlord', 'property', 'terms'];
        foreach ($requiredSections as $section) {
            if (stripos($this->content, $section) === false) {
                $errors[] = "Missing required section: {$section}";
            }
        }
        
        // Check for unbalanced variables
        preg_match_all('/\{\{([^}]*)\}\}/', $this->content, $matches);
        $openCount = substr_count($this->content, '{{');
        $closeCount = substr_count($this->content, '}}');
        
        if ($openCount !== $closeCount) {
            $errors[] = 'Unbalanced variable brackets in template';
        }
        
        // Check for empty variables
        foreach ($matches[1] as $match) {
            if (empty(trim($match))) {
                $errors[] = 'Empty variable found in template';
                break;
            }
        }
        
        return $errors;
    }

    /**
     * Get suggested template types
     */
    public static function getSuggestedTemplateTypes(): array
    {
        return [
            'residential' => 'Residential',
            'commercial' => 'Commercial',
            'short_term' => 'Short Term',
            'long_term' => 'Long Term',
            'monthly' => 'Monthly',
            'weekly' => 'Weekly',
            'daily' => 'Daily',
            'vacation' => 'Vacation Rental',
            'student' => 'Student Housing',
            'senior' => 'Senior Housing'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($template) {
            if (is_null($template->sort_order)) {
                $template->sort_order = static::where('template_type', $template->template_type)->max('sort_order') + 1;
            }
        });
    }
}
