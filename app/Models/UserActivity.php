<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'description',
        'ip_address',
        'user_agent',
        'metadata',
        'created_at',
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    protected $dates = [
        'created_at',
    ];

    /**
     * Get the user that owns the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to filter by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope to filter by user
     */
    public function scopeByUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by IP address
     */
    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Get activity icon based on action
     */
    public function getIconAttribute(): string
    {
        return match ($this->action) {
            'login' => 'fas fa-sign-in-alt text-success',
            'logout' => 'fas fa-sign-out-alt text-warning',
            'profile_updated' => 'fas fa-user-edit text-info',
            'password_changed' => 'fas fa-key text-warning',
            'permission_granted' => 'fas fa-shield-alt text-success',
            'permission_revoked' => 'fas fa-shield-alt text-danger',
            'role_assigned' => 'fas fa-user-tag text-success',
            'role_removed' => 'fas fa-user-tag text-danger',
            'data_exported' => 'fas fa-download text-info',
            'data_imported' => 'fas fa-upload text-info',
            'account_deleted' => 'fas fa-user-times text-danger',
            'email_verified' => 'fas fa-envelope-check text-success',
            'two_factor_enabled' => 'fas fa-lock text-success',
            'two_factor_disabled' => 'fas fa-unlock text-warning',
            default => 'fas fa-circle text-secondary',
        };
    }

    /**
     * Get activity color based on action
     */
    public function getColorAttribute(): string
    {
        return match ($this->action) {
            'login', 'permission_granted', 'role_assigned', 'email_verified', 'two_factor_enabled' => 'success',
            'logout', 'password_changed', 'two_factor_disabled' => 'warning',
            'profile_updated', 'data_exported', 'data_imported' => 'info',
            'permission_revoked', 'role_removed', 'account_deleted' => 'danger',
            default => 'secondary',
        };
    }

    /**
     * Get formatted metadata
     */
    public function getFormattedMetadataAttribute(): string
    {
        if (empty($this->metadata)) {
            return '';
        }

        $formatted = [];
        foreach ($this->metadata as $key => $value) {
            $formatted[] = ucfirst(str_replace('_', ' ', $key)) . ': ' . $value;
        }

        return implode(', ', $formatted);
    }
}