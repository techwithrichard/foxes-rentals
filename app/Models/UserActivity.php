<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class UserActivity extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'activity_type',
        'activity_name',
        'description',
        'metadata',
        'ip_address',
        'user_agent',
        'session_id',
        'subject_type',
        'subject_id',
        'causer_type',
        'causer_id',
        'properties',
        'created_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'properties' => 'array',
        'created_at' => 'datetime'
    ];

    /**
     * Get the user that performed the activity
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the subject of the activity (polymorphic)
     */
    public function subject(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the causer of the activity (polymorphic)
     */
    public function causer(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the activity type display name
     */
    public function getActivityTypeDisplayNameAttribute(): string
    {
        $types = [
            'authentication' => 'Authentication',
            'user_management' => 'User Management',
            'property_management' => 'Property Management',
            'lease_management' => 'Lease Management',
            'payment_processing' => 'Payment Processing',
            'system_settings' => 'System Settings',
            'data_export' => 'Data Export',
            'data_import' => 'Data Import',
            'security' => 'Security',
            'api_usage' => 'API Usage'
        ];

        return $types[$this->activity_type] ?? ucfirst(str_replace('_', ' ', $this->activity_type));
    }

    /**
     * Get the activity name display name
     */
    public function getActivityNameDisplayNameAttribute(): string
    {
        $names = [
            'login' => 'User Login',
            'logout' => 'User Logout',
            'failed_login' => 'Failed Login Attempt',
            'password_reset' => 'Password Reset',
            'user_created' => 'User Created',
            'user_updated' => 'User Updated',
            'user_deleted' => 'User Deleted',
            'user_activated' => 'User Activated',
            'user_deactivated' => 'User Deactivated',
            'role_assigned' => 'Role Assigned',
            'permission_granted' => 'Permission Granted',
            'property_created' => 'Property Created',
            'property_updated' => 'Property Updated',
            'property_deleted' => 'Property Deleted',
            'lease_created' => 'Lease Created',
            'lease_updated' => 'Lease Updated',
            'lease_terminated' => 'Lease Terminated',
            'payment_received' => 'Payment Received',
            'payment_failed' => 'Payment Failed',
            'settings_updated' => 'Settings Updated',
            'data_exported' => 'Data Exported',
            'data_imported' => 'Data Imported',
            'security_alert' => 'Security Alert',
            'api_access' => 'API Access',
            'bulk_action' => 'Bulk Action'
        ];

        return $names[$this->activity_name] ?? ucfirst(str_replace('_', ' ', $this->activity_name));
    }

    /**
     * Get the formatted description
     */
    public function getFormattedDescriptionAttribute(): string
    {
        if ($this->description) {
            return $this->description;
        }

        // Generate description based on activity name and metadata
        $user = $this->user ? $this->user->name : 'Unknown User';
        $subject = $this->subject ? $this->subject->name ?? 'Unknown' : null;
        $causer = $this->causer ? $this->causer->name : 'System';

        switch ($this->activity_name) {
            case 'login':
                return "{$user} logged in successfully";
            case 'logout':
                return "{$user} logged out";
            case 'failed_login':
                return "Failed login attempt for {$user}";
            case 'user_created':
                return "{$causer} created user {$subject}";
            case 'user_updated':
                return "{$causer} updated user {$user}";
            case 'user_deleted':
                return "{$causer} deleted user {$subject}";
            case 'user_activated':
                return "{$causer} activated user {$user}";
            case 'user_deactivated':
                return "{$causer} deactivated user {$user}";
            case 'role_assigned':
                return "{$causer} assigned role to {$user}";
            case 'permission_granted':
                return "{$causer} granted permission to {$user}";
            case 'property_created':
                return "{$user} created property {$subject}";
            case 'property_updated':
                return "{$user} updated property {$subject}";
            case 'property_deleted':
                return "{$user} deleted property {$subject}";
            case 'lease_created':
                return "{$user} created lease {$subject}";
            case 'lease_updated':
                return "{$user} updated lease {$subject}";
            case 'lease_terminated':
                return "{$user} terminated lease {$subject}";
            case 'payment_received':
                return "Payment received from {$user}";
            case 'payment_failed':
                return "Payment failed for {$user}";
            case 'settings_updated':
                return "{$user} updated system settings";
            case 'data_exported':
                return "{$user} exported data";
            case 'data_imported':
                return "{$user} imported data";
            case 'security_alert':
                return "Security alert triggered for {$user}";
            case 'api_access':
                return "{$user} accessed API";
            case 'bulk_action':
                return "{$user} performed bulk action";
            default:
                return "{$user} performed {$this->activity_name}";
        }
    }

    /**
     * Get the activity icon
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'login' => 'ni-signin',
            'logout' => 'ni-signout',
            'failed_login' => 'ni-alert-circle',
            'password_reset' => 'ni-key',
            'user_created' => 'ni-user-plus',
            'user_updated' => 'ni-user-edit',
            'user_deleted' => 'ni-user-remove',
            'user_activated' => 'ni-check-circle',
            'user_deactivated' => 'ni-cross-circle',
            'role_assigned' => 'ni-shield-star',
            'permission_granted' => 'ni-shield-check',
            'property_created' => 'ni-building-plus',
            'property_updated' => 'ni-building-edit',
            'property_deleted' => 'ni-building-remove',
            'lease_created' => 'ni-file-text-plus',
            'lease_updated' => 'ni-file-text-edit',
            'lease_terminated' => 'ni-file-text-remove',
            'payment_received' => 'ni-money',
            'payment_failed' => 'ni-money-cross',
            'settings_updated' => 'ni-setting',
            'data_exported' => 'ni-download',
            'data_imported' => 'ni-upload',
            'security_alert' => 'ni-shield-alert',
            'api_access' => 'ni-code',
            'bulk_action' => 'ni-list'
        ];

        return $icons[$this->activity_name] ?? 'ni-activity';
    }

    /**
     * Get the activity color
     */
    public function getColorAttribute(): string
    {
        $colors = [
            'login' => 'success',
            'logout' => 'secondary',
            'failed_login' => 'danger',
            'password_reset' => 'warning',
            'user_created' => 'success',
            'user_updated' => 'info',
            'user_deleted' => 'danger',
            'user_activated' => 'success',
            'user_deactivated' => 'warning',
            'role_assigned' => 'primary',
            'permission_granted' => 'success',
            'property_created' => 'success',
            'property_updated' => 'info',
            'property_deleted' => 'danger',
            'lease_created' => 'success',
            'lease_updated' => 'info',
            'lease_terminated' => 'warning',
            'payment_received' => 'success',
            'payment_failed' => 'danger',
            'settings_updated' => 'info',
            'data_exported' => 'primary',
            'data_imported' => 'success',
            'security_alert' => 'danger',
            'api_access' => 'info',
            'bulk_action' => 'primary'
        ];

        return $colors[$this->activity_name] ?? 'secondary';
    }

    /**
     * Get the browser name from user agent
     */
    public function getBrowserAttribute(): string
    {
        $userAgent = $this->user_agent;
        
        if (str_contains($userAgent, 'Chrome')) {
            return 'Chrome';
        } elseif (str_contains($userAgent, 'Firefox')) {
            return 'Firefox';
        } elseif (str_contains($userAgent, 'Safari')) {
            return 'Safari';
        } elseif (str_contains($userAgent, 'Edge')) {
            return 'Edge';
        } elseif (str_contains($userAgent, 'Opera')) {
            return 'Opera';
        }
        
        return 'Unknown';
    }

    /**
     * Get the operating system from user agent
     */
    public function getOperatingSystemAttribute(): string
    {
        $userAgent = $this->user_agent;
        
        if (str_contains($userAgent, 'Windows')) {
            return 'Windows';
        } elseif (str_contains($userAgent, 'Mac')) {
            return 'macOS';
        } elseif (str_contains($userAgent, 'Linux')) {
            return 'Linux';
        } elseif (str_contains($userAgent, 'Android')) {
            return 'Android';
        } elseif (str_contains($userAgent, 'iOS')) {
            return 'iOS';
        }
        
        return 'Unknown';
    }

    /**
     * Get the device type
     */
    public function getDeviceTypeAttribute(): string
    {
        $userAgent = $this->user_agent;
        
        if (str_contains($userAgent, 'Mobile')) {
            return 'Mobile';
        } elseif (str_contains($userAgent, 'Tablet')) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    /**
     * Get the location from IP address (simplified)
     */
    public function getLocationAttribute(): string
    {
        // This is a simplified implementation
        // In a real application, you would use a service like GeoIP
        $ip = $this->ip_address;
        
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
            return 'External IP';
        } else {
            return 'Local Network';
        }
    }

    /**
     * Scope to get activities by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('activity_type', $type);
    }

    /**
     * Scope to get activities by name
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('activity_name', $name);
    }

    /**
     * Scope to get activities for specific user
     */
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope to get activities by IP address
     */
    public function scopeByIpAddress($query, string $ipAddress)
    {
        return $query->where('ip_address', $ipAddress);
    }

    /**
     * Scope to get activities within date range
     */
    public function scopeWithinDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get recent activities
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to get security-related activities
     */
    public function scopeSecurity($query)
    {
        return $query->whereIn('activity_type', ['authentication', 'security'])
                    ->orWhereIn('activity_name', ['failed_login', 'password_reset', 'security_alert']);
    }

    /**
     * Scope to get user management activities
     */
    public function scopeUserManagement($query)
    {
        return $query->where('activity_type', 'user_management')
                    ->orWhereIn('activity_name', ['user_created', 'user_updated', 'user_deleted', 'user_activated', 'user_deactivated']);
    }

    /**
     * Get available activity types
     */
    public static function getAvailableActivityTypes(): array
    {
        return [
            'authentication' => 'Authentication',
            'user_management' => 'User Management',
            'property_management' => 'Property Management',
            'lease_management' => 'Lease Management',
            'payment_processing' => 'Payment Processing',
            'system_settings' => 'System Settings',
            'data_export' => 'Data Export',
            'data_import' => 'Data Import',
            'security' => 'Security',
            'api_usage' => 'API Usage'
        ];
    }

    /**
     * Get available activity names
     */
    public static function getAvailableActivityNames(): array
    {
        return [
            'login' => 'User Login',
            'logout' => 'User Logout',
            'failed_login' => 'Failed Login Attempt',
            'password_reset' => 'Password Reset',
            'user_created' => 'User Created',
            'user_updated' => 'User Updated',
            'user_deleted' => 'User Deleted',
            'user_activated' => 'User Activated',
            'user_deactivated' => 'User Deactivated',
            'role_assigned' => 'Role Assigned',
            'permission_granted' => 'Permission Granted',
            'property_created' => 'Property Created',
            'property_updated' => 'Property Updated',
            'property_deleted' => 'Property Deleted',
            'lease_created' => 'Lease Created',
            'lease_updated' => 'Lease Updated',
            'lease_terminated' => 'Lease Terminated',
            'payment_received' => 'Payment Received',
            'payment_failed' => 'Payment Failed',
            'settings_updated' => 'Settings Updated',
            'data_exported' => 'Data Exported',
            'data_imported' => 'Data Imported',
            'security_alert' => 'Security Alert',
            'api_access' => 'API Access',
            'bulk_action' => 'Bulk Action'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($activity) {
            if (!$activity->ip_address) {
                $activity->ip_address = request()->ip();
            }
            
            if (!$activity->user_agent) {
                $activity->user_agent = request()->userAgent();
            }
            
            if (!$activity->session_id) {
                $activity->session_id = session()->getId();
            }
        });
    }
}
