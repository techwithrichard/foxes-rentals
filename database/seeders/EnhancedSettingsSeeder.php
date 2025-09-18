<?php

namespace Database\Seeders;

use App\Models\SettingsCategory;
use App\Models\SettingsGroup;
use App\Models\SettingsItem;
use Illuminate\Database\Seeder;

class EnhancedSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->createSecuritySettings();
        $this->createBusinessSettings();
        $this->createNotificationSettings();
        $this->createPerformanceSettings();
    }

    private function createSecuritySettings()
    {
        $securityCategory = SettingsCategory::create([
            'name' => 'Security & Authentication',
            'slug' => 'security-authentication',
            'description' => 'Security settings, authentication policies, and access control',
            'icon' => 'ni-shield-check',
            'order_index' => 1,
            'is_active' => true
        ]);

        $authGroup = SettingsGroup::create([
            'category_id' => $securityCategory->id,
            'name' => 'Authentication',
            'slug' => 'authentication',
            'description' => 'User authentication and login settings',
            'order_index' => 1,
            'is_active' => true
        ]);

        SettingsItem::create([
            'group_id' => $authGroup->id,
            'key' => 'two_factor_authentication',
            'value' => 'false',
            'type' => 'boolean',
            'description' => 'Enable two-factor authentication for all users',
            'default_value' => 'false',
            'is_active' => true,
            'order_index' => 1
        ]);

        SettingsItem::create([
            'group_id' => $authGroup->id,
            'key' => 'failed_login_attempts',
            'value' => '5',
            'type' => 'number',
            'description' => 'Maximum failed login attempts before account lockout',
            'default_value' => '5',
            'validation_rules' => ['min:3', 'max:10'],
            'is_active' => true,
            'order_index' => 2
        ]);

        SettingsItem::create([
            'group_id' => $authGroup->id,
            'key' => 'session_timeout',
            'value' => '120',
            'type' => 'select',
            'description' => 'Session timeout in minutes',
            'default_value' => '120',
            'options' => [
                '15' => '15 minutes',
                '30' => '30 minutes',
                '60' => '1 hour',
                '120' => '2 hours',
                '240' => '4 hours',
                '480' => '8 hours',
                '1440' => '24 hours'
            ],
            'is_active' => true,
            'order_index' => 3
        ]);
    }

    private function createBusinessSettings()
    {
        $businessCategory = SettingsCategory::create([
            'name' => 'Business Configuration',
            'slug' => 'business-configuration',
            'description' => 'Financial settings, business rules, and operational policies',
            'icon' => 'ni-building',
            'order_index' => 2,
            'is_active' => true
        ]);

        $financialGroup = SettingsGroup::create([
            'category_id' => $businessCategory->id,
            'name' => 'Financial Settings',
            'slug' => 'financial-settings',
            'description' => 'Tax rates, fees, and financial calculations',
            'order_index' => 1,
            'is_active' => true
        ]);

        SettingsItem::create([
            'group_id' => $financialGroup->id,
            'key' => 'default_tax_rate',
            'value' => '16.0',
            'type' => 'number',
            'description' => 'Default tax rate percentage',
            'default_value' => '16.0',
            'validation_rules' => ['min:0', 'max:100'],
            'is_active' => true,
            'order_index' => 1
        ]);

        SettingsItem::create([
            'group_id' => $financialGroup->id,
            'key' => 'late_payment_penalty_rate',
            'value' => '2.0',
            'type' => 'number',
            'description' => 'Late payment penalty rate percentage per month',
            'default_value' => '2.0',
            'validation_rules' => ['min:0', 'max:20'],
            'is_active' => true,
            'order_index' => 2
        ]);

        SettingsItem::create([
            'group_id' => $financialGroup->id,
            'key' => 'commission_rate_landlord',
            'value' => '8.0',
            'type' => 'number',
            'description' => 'Commission rate for landlords (%)',
            'default_value' => '8.0',
            'validation_rules' => ['min:0', 'max:50'],
            'is_active' => true,
            'order_index' => 3
        ]);
    }

    private function createNotificationSettings()
    {
        $notificationCategory = SettingsCategory::create([
            'name' => 'Notification Management',
            'slug' => 'notification-management',
            'description' => 'Email, SMS, and push notification settings',
            'icon' => 'ni-notification',
            'order_index' => 3,
            'is_active' => true
        ]);

        $emailGroup = SettingsGroup::create([
            'category_id' => $notificationCategory->id,
            'name' => 'Email Settings',
            'slug' => 'email-settings',
            'description' => 'Email notification preferences and templates',
            'order_index' => 1,
            'is_active' => true
        ]);

        SettingsItem::create([
            'group_id' => $emailGroup->id,
            'key' => 'email_notifications_enabled',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Enable email notifications',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 1
        ]);

        SettingsItem::create([
            'group_id' => $emailGroup->id,
            'key' => 'invoice_reminder_days',
            'value' => '7',
            'type' => 'number',
            'description' => 'Days before invoice due date to send reminder',
            'default_value' => '7',
            'validation_rules' => ['min:1', 'max:30'],
            'is_active' => true,
            'order_index' => 2
        ]);
    }

    private function createPerformanceSettings()
    {
        $performanceCategory = SettingsCategory::create([
            'name' => 'System Performance',
            'slug' => 'system-performance',
            'description' => 'Cache, optimization, and monitoring settings',
            'icon' => 'ni-speedometer',
            'order_index' => 4,
            'is_active' => true
        ]);

        $cacheGroup = SettingsGroup::create([
            'category_id' => $performanceCategory->id,
            'name' => 'Cache Settings',
            'slug' => 'cache-settings',
            'description' => 'Cache configuration and optimization',
            'order_index' => 1,
            'is_active' => true
        ]);

        SettingsItem::create([
            'group_id' => $cacheGroup->id,
            'key' => 'cache_enabled',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Enable application caching',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 1
        ]);

        SettingsItem::create([
            'group_id' => $cacheGroup->id,
            'key' => 'cache_ttl',
            'value' => '3600',
            'type' => 'select',
            'description' => 'Cache time-to-live in seconds',
            'default_value' => '3600',
            'options' => [
                '900' => '15 minutes',
                '1800' => '30 minutes',
                '3600' => '1 hour',
                '7200' => '2 hours',
                '14400' => '4 hours',
                '86400' => '24 hours'
            ],
            'is_active' => true,
            'order_index' => 2
        ]);
    }
}
