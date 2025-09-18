<?php

namespace App\Helpers;

use App\Services\EnhancedSettingsService;

class EnhancedSettingsHelper
{
    protected static $service;

    /**
     * Get the enhanced settings service instance
     */
    protected static function getService()
    {
        if (!static::$service) {
            static::$service = app(EnhancedSettingsService::class);
        }
        return static::$service;
    }

    /**
     * Get a setting value
     */
    public static function get($key, $default = null)
    {
        return static::getService()->getSetting($key, $default);
    }

    /**
     * Set a setting value
     */
    public static function set($key, $value, $userId = null)
    {
        return static::getService()->setSetting($key, $value, $userId);
    }

    /**
     * Get all settings
     */
    public static function all()
    {
        return static::getService()->getAllSettings();
    }

    /**
     * Get settings by category
     */
    public static function category($categorySlug)
    {
        return static::getService()->getSettingsByCategory($categorySlug);
    }

    /**
     * Security Settings Helpers
     */
    public static function isTwoFactorEnabled()
    {
        return static::get('two_factor_authentication', false);
    }

    public static function getMaxLoginAttempts()
    {
        return static::get('failed_login_attempts', 5);
    }

    public static function getSessionTimeout()
    {
        return static::get('session_timeout', 120);
    }

    /**
     * Business Settings Helpers
     */
    public static function getTaxRate()
    {
        return static::get('default_tax_rate', 16.0);
    }

    public static function getLatePaymentPenaltyRate()
    {
        return static::get('late_payment_penalty_rate', 2.0);
    }

    public static function getLandlordCommissionRate()
    {
        return static::get('commission_rate_landlord', 8.0);
    }

    public static function getAdminCommissionRate()
    {
        return static::get('commission_rate_admin', 5.0);
    }

    /**
     * Notification Settings Helpers
     */
    public static function isEmailNotificationsEnabled()
    {
        return static::get('email_notifications_enabled', true);
    }

    public static function getInvoiceReminderDays()
    {
        return static::get('invoice_reminder_days', 7);
    }

    public static function isSmsNotificationsEnabled()
    {
        return static::get('sms_notifications_enabled', true);
    }

    /**
     * Performance Settings Helpers
     */
    public static function isCacheEnabled()
    {
        return static::get('cache_enabled', true);
    }

    public static function getCacheTtl()
    {
        return static::get('cache_ttl', 3600);
    }

    /**
     * Clear all settings cache
     */
    public static function clearCache()
    {
        return static::getService()->clearAllCache();
    }

    /**
     * Export settings
     */
    public static function export()
    {
        return static::getService()->exportSettings();
    }

    /**
     * Import settings
     */
    public static function import($settings, $userId = null)
    {
        return static::getService()->importSettings($settings, $userId);
    }
}
