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
        $this->createUserSettings();
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

    private function createUserSettings()
    {
        $userCategory = SettingsCategory::firstOrCreate(
            ['slug' => 'user-management'],
            [
                'name' => 'User Management',
                'description' => 'User roles, permissions, profiles, and account management settings',
                'icon' => 'ni-users',
                'order_index' => 5,
                'is_active' => true
            ]
        );

        // Role Management Group
        $roleGroup = SettingsGroup::firstOrCreate(
            ['slug' => 'role-management', 'category_id' => $userCategory->id],
            [
                'name' => 'Role Management',
                'description' => 'Configure user roles and their default permissions',
                'order_index' => 1,
                'is_active' => true
            ]
        );

        SettingsItem::firstOrCreate(
            ['key' => 'default_user_role'],
            [
                'group_id' => $roleGroup->id,
                'value' => 'tenant',
                'type' => 'select',
                'description' => 'Default role assigned to new users',
                'default_value' => 'tenant',
                'options' => [
                    'tenant' => 'Tenant',
                    'landlord' => 'Landlord',
                    'admin' => 'Administrator',
                    'manager' => 'Property Manager',
                    'viewer' => 'Viewer Only'
                ],
                'is_active' => true,
                'order_index' => 1
            ]
        );

        SettingsItem::create([
            'group_id' => $roleGroup->id,
            'key' => 'role_hierarchy_enabled',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Enable role hierarchy (higher roles can manage lower roles)',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 2
        ]);

        SettingsItem::create([
            'group_id' => $roleGroup->id,
            'key' => 'max_roles_per_user',
            'value' => '3',
            'type' => 'number',
            'description' => 'Maximum number of roles a user can have',
            'default_value' => '3',
            'validation_rules' => ['min:1', 'max:10'],
            'is_active' => true,
            'order_index' => 3
        ]);

        SettingsItem::create([
            'group_id' => $roleGroup->id,
            'key' => 'auto_assign_roles',
            'value' => 'false',
            'type' => 'boolean',
            'description' => 'Automatically assign roles based on user registration data',
            'default_value' => 'false',
            'is_active' => true,
            'order_index' => 4
        ]);

        // Permission Management Group
        $permissionGroup = SettingsGroup::create([
            'category_id' => $userCategory->id,
            'name' => 'Permission Management',
            'slug' => 'permission-management',
            'description' => 'Configure system permissions and access controls',
            'order_index' => 2,
            'is_active' => true
        ]);

        SettingsItem::create([
            'group_id' => $permissionGroup->id,
            'key' => 'permission_caching_enabled',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Enable permission caching for better performance',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 1
        ]);

        SettingsItem::create([
            'group_id' => $permissionGroup->id,
            'key' => 'permission_cache_ttl',
            'value' => '3600',
            'type' => 'select',
            'description' => 'Permission cache time-to-live in seconds',
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

        SettingsItem::create([
            'group_id' => $permissionGroup->id,
            'key' => 'require_explicit_permissions',
            'value' => 'false',
            'type' => 'boolean',
            'description' => 'Require explicit permission grants (no inheritance)',
            'default_value' => 'false',
            'is_active' => true,
            'order_index' => 3
        ]);

        SettingsItem::create([
            'group_id' => $permissionGroup->id,
            'key' => 'permission_audit_logging',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Log all permission checks and access attempts',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 4
        ]);

        // User Profile Settings Group
        $profileGroup = SettingsGroup::create([
            'category_id' => $userCategory->id,
            'name' => 'User Profile Settings',
            'slug' => 'user-profile-settings',
            'description' => 'Configure user profile fields and requirements',
            'order_index' => 3,
            'is_active' => true
        ]);

        SettingsItem::create([
            'group_id' => $profileGroup->id,
            'key' => 'require_profile_completion',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Require users to complete their profile before accessing the system',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 1
        ]);

        SettingsItem::create([
            'group_id' => $profileGroup->id,
            'key' => 'required_profile_fields',
            'value' => 'name,email,phone',
            'type' => 'multiselect',
            'description' => 'Required fields for user profiles',
            'default_value' => 'name,email,phone',
            'options' => [
                'name' => 'Full Name',
                'email' => 'Email Address',
                'phone' => 'Phone Number',
                'address' => 'Address',
                'id_number' => 'ID Number',
                'emergency_contact' => 'Emergency Contact',
                'profile_picture' => 'Profile Picture',
                'bio' => 'Biography'
            ],
            'is_active' => true,
            'order_index' => 2
        ]);

        SettingsItem::create([
            'group_id' => $profileGroup->id,
            'key' => 'allow_profile_picture_upload',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Allow users to upload profile pictures',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 3
        ]);

        SettingsItem::create([
            'group_id' => $profileGroup->id,
            'key' => 'max_profile_picture_size',
            'value' => '2048',
            'type' => 'number',
            'description' => 'Maximum profile picture file size in KB',
            'default_value' => '2048',
            'validation_rules' => ['min:512', 'max:10240'],
            'is_active' => true,
            'order_index' => 4
        ]);

        SettingsItem::create([
            'group_id' => $profileGroup->id,
            'key' => 'profile_picture_dimensions',
            'value' => '200x200',
            'type' => 'select',
            'description' => 'Standard profile picture dimensions',
            'default_value' => '200x200',
            'options' => [
                '150x150' => '150x150 pixels',
                '200x200' => '200x200 pixels',
                '300x300' => '300x300 pixels',
                '400x400' => '400x400 pixels'
            ],
            'is_active' => true,
            'order_index' => 5
        ]);

        // Account Security Group
        $accountSecurityGroup = SettingsGroup::create([
            'category_id' => $userCategory->id,
            'name' => 'Account Security',
            'slug' => 'account-security',
            'description' => 'User account security and password policies',
            'order_index' => 4,
            'is_active' => true
        ]);

        SettingsItem::create([
            'group_id' => $accountSecurityGroup->id,
            'key' => 'password_min_length',
            'value' => '8',
            'type' => 'number',
            'description' => 'Minimum password length',
            'default_value' => '8',
            'validation_rules' => ['min:6', 'max:32'],
            'is_active' => true,
            'order_index' => 1
        ]);

        SettingsItem::create([
            'group_id' => $accountSecurityGroup->id,
            'key' => 'password_require_uppercase',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Require uppercase letters in passwords',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 2
        ]);

        SettingsItem::create([
            'group_id' => $accountSecurityGroup->id,
            'key' => 'password_require_lowercase',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Require lowercase letters in passwords',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 3
        ]);

        SettingsItem::create([
            'group_id' => $accountSecurityGroup->id,
            'key' => 'password_require_numbers',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Require numbers in passwords',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 4
        ]);

        SettingsItem::create([
            'group_id' => $accountSecurityGroup->id,
            'key' => 'password_require_symbols',
            'value' => 'false',
            'type' => 'boolean',
            'description' => 'Require special symbols in passwords',
            'default_value' => 'false',
            'is_active' => true,
            'order_index' => 5
        ]);

        SettingsItem::create([
            'group_id' => $accountSecurityGroup->id,
            'key' => 'password_expiry_days',
            'value' => '90',
            'type' => 'select',
            'description' => 'Password expiry period in days (0 = never expire)',
            'default_value' => '90',
            'options' => [
                '0' => 'Never expire',
                '30' => '30 days',
                '60' => '60 days',
                '90' => '90 days',
                '180' => '180 days',
                '365' => '1 year'
            ],
            'is_active' => true,
            'order_index' => 6
        ]);

        SettingsItem::create([
            'group_id' => $accountSecurityGroup->id,
            'key' => 'account_lockout_duration',
            'value' => '15',
            'type' => 'select',
            'description' => 'Account lockout duration in minutes after failed attempts',
            'default_value' => '15',
            'options' => [
                '5' => '5 minutes',
                '15' => '15 minutes',
                '30' => '30 minutes',
                '60' => '1 hour',
                '120' => '2 hours',
                '240' => '4 hours'
            ],
            'is_active' => true,
            'order_index' => 7
        ]);

        // User Registration Settings Group
        $registrationGroup = SettingsGroup::create([
            'category_id' => $userCategory->id,
            'name' => 'User Registration',
            'slug' => 'user-registration',
            'description' => 'Configure user registration process and requirements',
            'order_index' => 5,
            'is_active' => true
        ]);

        SettingsItem::create([
            'group_id' => $registrationGroup->id,
            'key' => 'registration_enabled',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Allow new user registrations',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 1
        ]);

        SettingsItem::create([
            'group_id' => $registrationGroup->id,
            'key' => 'email_verification_required',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Require email verification for new accounts',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 2
        ]);

        SettingsItem::create([
            'group_id' => $registrationGroup->id,
            'key' => 'phone_verification_required',
            'value' => 'false',
            'type' => 'boolean',
            'description' => 'Require phone number verification for new accounts',
            'default_value' => 'false',
            'is_active' => true,
            'order_index' => 3
        ]);

        SettingsItem::create([
            'group_id' => $registrationGroup->id,
            'key' => 'admin_approval_required',
            'value' => 'false',
            'type' => 'boolean',
            'description' => 'Require admin approval for new user accounts',
            'default_value' => 'false',
            'is_active' => true,
            'order_index' => 4
        ]);

        SettingsItem::create([
            'group_id' => $registrationGroup->id,
            'key' => 'registration_terms_required',
            'value' => 'true',
            'type' => 'boolean',
            'description' => 'Require users to accept terms and conditions during registration',
            'default_value' => 'true',
            'is_active' => true,
            'order_index' => 5
        ]);

        SettingsItem::create([
            'group_id' => $registrationGroup->id,
            'key' => 'max_registrations_per_ip',
            'value' => '5',
            'type' => 'number',
            'description' => 'Maximum registrations allowed per IP address per day',
            'default_value' => '5',
            'validation_rules' => ['min:1', 'max:50'],
            'is_active' => true,
            'order_index' => 6
        ]);
    }
}
