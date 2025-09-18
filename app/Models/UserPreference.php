<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPreference extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'preferences',
        'theme',
        'language',
        'timezone',
        'date_format',
        'time_format',
        'currency',
        'notifications',
        'email_notifications',
        'sms_notifications',
        'push_notifications',
        'created_by'
    ];

    protected $casts = [
        'preferences' => 'array',
        'notifications' => 'array',
        'email_notifications' => 'array',
        'sms_notifications' => 'array',
        'push_notifications' => 'array'
    ];

    /**
     * Get the user that owns the preferences
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who created these preferences
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get a specific preference value
     */
    public function getPreference(string $key, $default = null)
    {
        return data_get($this->preferences, $key, $default);
    }

    /**
     * Set a specific preference value
     */
    public function setPreference(string $key, $value): void
    {
        $preferences = $this->preferences ?? [];
        data_set($preferences, $key, $value);
        $this->update(['preferences' => $preferences]);
    }

    /**
     * Remove a specific preference
     */
    public function removePreference(string $key): void
    {
        $preferences = $this->preferences ?? [];
        data_forget($preferences, $key);
        $this->update(['preferences' => $preferences]);
    }

    /**
     * Get theme display name
     */
    public function getThemeDisplayNameAttribute(): string
    {
        $themes = [
            'light' => 'Light',
            'dark' => 'Dark',
            'auto' => 'Auto',
            'blue' => 'Blue',
            'green' => 'Green',
            'purple' => 'Purple'
        ];

        return $themes[$this->theme] ?? 'Light';
    }

    /**
     * Get language display name
     */
    public function getLanguageDisplayNameAttribute(): string
    {
        $languages = [
            'en' => 'English',
            'sw' => 'Kiswahili',
            'fr' => 'French',
            'es' => 'Spanish',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ar' => 'Arabic'
        ];

        return $languages[$this->language] ?? 'English';
    }

    /**
     * Get timezone display name
     */
    public function getTimezoneDisplayNameAttribute(): string
    {
        $timezones = [
            'Africa/Nairobi' => 'Nairobi (GMT+3)',
            'Africa/Dar_es_Salaam' => 'Dar es Salaam (GMT+3)',
            'Africa/Kampala' => 'Kampala (GMT+3)',
            'Africa/Kigali' => 'Kigali (GMT+2)',
            'Africa/Addis_Ababa' => 'Addis Ababa (GMT+3)',
            'America/New_York' => 'New York (GMT-5)',
            'Europe/London' => 'London (GMT+0)',
            'Asia/Tokyo' => 'Tokyo (GMT+9)',
            'Australia/Sydney' => 'Sydney (GMT+10)'
        ];

        return $timezones[$this->timezone] ?? $this->timezone ?? 'UTC';
    }

    /**
     * Get date format display name
     */
    public function getDateFormatDisplayNameAttribute(): string
    {
        $formats = [
            'Y-m-d' => 'YYYY-MM-DD',
            'd/m/Y' => 'DD/MM/YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'd-m-Y' => 'DD-MM-YYYY',
            'M d, Y' => 'MMM DD, YYYY',
            'd M Y' => 'DD MMM YYYY'
        ];

        return $formats[$this->date_format] ?? 'YYYY-MM-DD';
    }

    /**
     * Get time format display name
     */
    public function getTimeFormatDisplayNameAttribute(): string
    {
        $formats = [
            'H:i:s' => '24 Hour (HH:MM:SS)',
            'H:i' => '24 Hour (HH:MM)',
            'h:i:s A' => '12 Hour (HH:MM:SS AM/PM)',
            'h:i A' => '12 Hour (HH:MM AM/PM)'
        ];

        return $formats[$this->time_format] ?? '24 Hour (HH:MM:SS)';
    }

    /**
     * Get currency display name
     */
    public function getCurrencyDisplayNameAttribute(): string
    {
        $currencies = [
            'KES' => 'Kenyan Shilling (KES)',
            'USD' => 'US Dollar (USD)',
            'EUR' => 'Euro (EUR)',
            'GBP' => 'British Pound (GBP)',
            'UGX' => 'Ugandan Shilling (UGX)',
            'TZS' => 'Tanzanian Shilling (TZS)',
            'RWF' => 'Rwandan Franc (RWF)',
            'ETB' => 'Ethiopian Birr (ETB)'
        ];

        return $currencies[$this->currency] ?? 'Kenyan Shilling (KES)';
    }

    /**
     * Check if a specific notification type is enabled
     */
    public function isNotificationEnabled(string $type, string $channel = 'email'): bool
    {
        $notifications = $this->{"{$channel}_notifications"} ?? [];
        return in_array($type, $notifications);
    }

    /**
     * Enable a specific notification type
     */
    public function enableNotification(string $type, string $channel = 'email'): void
    {
        $notifications = $this->{"{$channel}_notifications"} ?? [];
        if (!in_array($type, $notifications)) {
            $notifications[] = $type;
            $this->update(["{$channel}_notifications" => $notifications]);
        }
    }

    /**
     * Disable a specific notification type
     */
    public function disableNotification(string $type, string $channel = 'email'): void
    {
        $notifications = $this->{"{$channel}_notifications"} ?? [];
        $notifications = array_diff($notifications, [$type]);
        $this->update(["{$channel}_notifications" => $notifications]);
    }

    /**
     * Get all enabled notifications for a channel
     */
    public function getEnabledNotifications(string $channel = 'email'): array
    {
        return $this->{"{$channel}_notifications"} ?? [];
    }

    /**
     * Get notification preferences summary
     */
    public function getNotificationSummaryAttribute(): array
    {
        return [
            'email' => [
                'enabled' => count($this->email_notifications ?? []),
                'total' => count(self::getAvailableNotificationTypes()),
                'types' => $this->email_notifications ?? []
            ],
            'sms' => [
                'enabled' => count($this->sms_notifications ?? []),
                'total' => count(self::getAvailableNotificationTypes()),
                'types' => $this->sms_notifications ?? []
            ],
            'push' => [
                'enabled' => count($this->push_notifications ?? []),
                'total' => count(self::getAvailableNotificationTypes()),
                'types' => $this->push_notifications ?? []
            ]
        ];
    }

    /**
     * Get preference summary
     */
    public function getPreferenceSummaryAttribute(): array
    {
        return [
            'theme' => $this->theme_display_name,
            'language' => $this->language_display_name,
            'timezone' => $this->timezone_display_name,
            'date_format' => $this->date_format_display_name,
            'time_format' => $this->time_format_display_name,
            'currency' => $this->currency_display_name,
            'total_preferences' => count($this->preferences ?? []),
            'notifications_enabled' => array_sum(array_map('count', [
                $this->email_notifications ?? [],
                $this->sms_notifications ?? [],
                $this->push_notifications ?? []
            ]))
        ];
    }

    /**
     * Scope to get preferences by theme
     */
    public function scopeTheme($query, string $theme)
    {
        return $query->where('theme', $theme);
    }

    /**
     * Scope to get preferences by language
     */
    public function scopeLanguage($query, string $language)
    {
        return $query->where('language', $language);
    }

    /**
     * Scope to get preferences by timezone
     */
    public function scopeTimezone($query, string $timezone)
    {
        return $query->where('timezone', $timezone);
    }

    /**
     * Scope to get preferences with specific notification enabled
     */
    public function scopeWithNotification($query, string $type, string $channel = 'email')
    {
        return $query->whereJsonContains("{$channel}_notifications", $type);
    }

    /**
     * Get available themes
     */
    public static function getAvailableThemes(): array
    {
        return [
            'light' => 'Light',
            'dark' => 'Dark',
            'auto' => 'Auto',
            'blue' => 'Blue',
            'green' => 'Green',
            'purple' => 'Purple'
        ];
    }

    /**
     * Get available languages
     */
    public static function getAvailableLanguages(): array
    {
        return [
            'en' => 'English',
            'sw' => 'Kiswahili',
            'fr' => 'French',
            'es' => 'Spanish',
            'de' => 'German',
            'it' => 'Italian',
            'pt' => 'Portuguese',
            'ar' => 'Arabic'
        ];
    }

    /**
     * Get available timezones
     */
    public static function getAvailableTimezones(): array
    {
        return [
            'Africa/Nairobi' => 'Nairobi (GMT+3)',
            'Africa/Dar_es_Salaam' => 'Dar es Salaam (GMT+3)',
            'Africa/Kampala' => 'Kampala (GMT+3)',
            'Africa/Kigali' => 'Kigali (GMT+2)',
            'Africa/Addis_Ababa' => 'Addis Ababa (GMT+3)',
            'Africa/Cairo' => 'Cairo (GMT+2)',
            'Africa/Johannesburg' => 'Johannesburg (GMT+2)',
            'America/New_York' => 'New York (GMT-5)',
            'America/Los_Angeles' => 'Los Angeles (GMT-8)',
            'Europe/London' => 'London (GMT+0)',
            'Europe/Paris' => 'Paris (GMT+1)',
            'Asia/Tokyo' => 'Tokyo (GMT+9)',
            'Asia/Shanghai' => 'Shanghai (GMT+8)',
            'Australia/Sydney' => 'Sydney (GMT+10)'
        ];
    }

    /**
     * Get available date formats
     */
    public static function getAvailableDateFormats(): array
    {
        return [
            'Y-m-d' => 'YYYY-MM-DD',
            'd/m/Y' => 'DD/MM/YYYY',
            'm/d/Y' => 'MM/DD/YYYY',
            'd-m-Y' => 'DD-MM-YYYY',
            'M d, Y' => 'MMM DD, YYYY',
            'd M Y' => 'DD MMM YYYY'
        ];
    }

    /**
     * Get available time formats
     */
    public static function getAvailableTimeFormats(): array
    {
        return [
            'H:i:s' => '24 Hour (HH:MM:SS)',
            'H:i' => '24 Hour (HH:MM)',
            'h:i:s A' => '12 Hour (HH:MM:SS AM/PM)',
            'h:i A' => '12 Hour (HH:MM AM/PM)'
        ];
    }

    /**
     * Get available currencies
     */
    public static function getAvailableCurrencies(): array
    {
        return [
            'KES' => 'Kenyan Shilling (KES)',
            'USD' => 'US Dollar (USD)',
            'EUR' => 'Euro (EUR)',
            'GBP' => 'British Pound (GBP)',
            'UGX' => 'Ugandan Shilling (UGX)',
            'TZS' => 'Tanzanian Shilling (TZS)',
            'RWF' => 'Rwandan Franc (RWF)',
            'ETB' => 'Ethiopian Birr (ETB)',
            'GHS' => 'Ghanaian Cedi (GHS)',
            'NGN' => 'Nigerian Naira (NGN)',
            'ZAR' => 'South African Rand (ZAR)'
        ];
    }

    /**
     * Get available notification types
     */
    public static function getAvailableNotificationTypes(): array
    {
        return [
            'login_notifications' => 'Login Notifications',
            'security_alerts' => 'Security Alerts',
            'property_updates' => 'Property Updates',
            'lease_reminders' => 'Lease Reminders',
            'payment_notifications' => 'Payment Notifications',
            'maintenance_requests' => 'Maintenance Requests',
            'system_updates' => 'System Updates',
            'marketing_emails' => 'Marketing Emails',
            'newsletter' => 'Newsletter',
            'promotional_offers' => 'Promotional Offers'
        ];
    }

    /**
     * Boot method to set default values
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($preference) {
            if (!$preference->created_by && auth()->check()) {
                $preference->created_by = auth()->id();
            }

            // Set default values if not provided
            $preference->theme = $preference->theme ?? 'light';
            $preference->language = $preference->language ?? 'en';
            $preference->timezone = $preference->timezone ?? 'Africa/Nairobi';
            $preference->date_format = $preference->date_format ?? 'Y-m-d';
            $preference->time_format = $preference->time_format ?? 'H:i:s';
            $preference->currency = $preference->currency ?? 'KES';
        });
    }
}
