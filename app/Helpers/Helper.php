<?php

if (!function_exists('enhanced_setting')) {
    /**
     * Get an enhanced setting value
     */
    function enhanced_setting($key, $default = null)
    {
        return \App\Helpers\EnhancedSettingsHelper::get($key, $default);
    }
}

if (!function_exists('enhanced_setting_set')) {
    /**
     * Set an enhanced setting value
     */
    function enhanced_setting_set($key, $value, $userId = null)
    {
        return \App\Helpers\EnhancedSettingsHelper::set($key, $value, $userId);
    }
}
