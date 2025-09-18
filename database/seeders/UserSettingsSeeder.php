<?php

namespace Database\Seeders;

use App\Models\SettingsCategory;
use App\Models\SettingsGroup;
use App\Models\SettingsItem;
use Illuminate\Database\Seeder;

class UserSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $this->createUserSettings();
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

        // Create role management settings
        $this->createRoleSettings($roleGroup);

        // Permission Management Group
        $permissionGroup = SettingsGroup::firstOrCreate(
            ['slug' => 'permission-management', 'category_id' => $userCategory->id],
            [
                'name' => 'Permission Management',
                'slug' => 'permission-management',
                'description' => 'Configure system permissions and access controls',
                'order_index' => 2,
                'is_active' => true
            ]
        );

        // Create permission settings
        $this->createPermissionSettings($permissionGroup);

        // User Profile Settings Group
        $profileGroup = SettingsGroup::firstOrCreate(
            ['slug' => 'user-profile-settings', 'category_id' => $userCategory->id],
            [
                'name' => 'User Profile Settings',
                'description' => 'Configure user profile fields and requirements',
                'order_index' => 3,
                'is_active' => true
            ]
        );

        // Create profile settings
        $this->createProfileSettings($profileGroup);

        // Account Security Group
        $accountSecurityGroup = SettingsGroup::firstOrCreate(
            ['slug' => 'account-security', 'category_id' => $userCategory->id],
            [
                'name' => 'Account Security',
                'description' => 'User account security and password policies',
                'order_index' => 4,
                'is_active' => true
            ]
        );

        // Create account security settings
        $this->createAccountSecuritySettings($accountSecurityGroup);

        // User Registration Settings Group
        $registrationGroup = SettingsGroup::firstOrCreate(
            ['slug' => 'user-registration', 'category_id' => $userCategory->id],
            [
                'name' => 'User Registration',
                'description' => 'Configure user registration process and requirements',
                'order_index' => 5,
                'is_active' => true
            ]
        );

        // Create registration settings
        $this->createRegistrationSettings($registrationGroup);
    }

    private function createRoleSettings($roleGroup)
    {
        $roleSettings = [
            [
                'key' => 'default_user_role',
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
                'order_index' => 1
            ],
            [
                'key' => 'role_hierarchy_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable role hierarchy (higher roles can manage lower roles)',
                'default_value' => 'true',
                'order_index' => 2
            ],
            [
                'key' => 'max_roles_per_user',
                'value' => '3',
                'type' => 'number',
                'description' => 'Maximum number of roles a user can have',
                'default_value' => '3',
                'validation_rules' => ['min:1', 'max:10'],
                'order_index' => 3
            ],
            [
                'key' => 'auto_assign_roles',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Automatically assign roles based on user registration data',
                'default_value' => 'false',
                'order_index' => 4
            ]
        ];

        foreach ($roleSettings as $setting) {
            SettingsItem::firstOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'group_id' => $roleGroup->id,
                    'is_active' => true
                ])
            );
        }
    }

    private function createPermissionSettings($permissionGroup)
    {
        $permissionSettings = [
            [
                'key' => 'permission_caching_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Enable permission caching for better performance',
                'default_value' => 'true',
                'order_index' => 1
            ],
            [
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
                'order_index' => 2
            ],
            [
                'key' => 'require_explicit_permissions',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Require explicit permission grants (no inheritance)',
                'default_value' => 'false',
                'order_index' => 3
            ],
            [
                'key' => 'permission_audit_logging',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Log all permission checks and access attempts',
                'default_value' => 'true',
                'order_index' => 4
            ]
        ];

        foreach ($permissionSettings as $setting) {
            SettingsItem::firstOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'group_id' => $permissionGroup->id,
                    'is_active' => true
                ])
            );
        }
    }

    private function createProfileSettings($profileGroup)
    {
        $profileSettings = [
            [
                'key' => 'require_profile_completion',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require users to complete their profile before accessing the system',
                'default_value' => 'true',
                'order_index' => 1
            ],
            [
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
                'order_index' => 2
            ],
            [
                'key' => 'allow_profile_picture_upload',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow users to upload profile pictures',
                'default_value' => 'true',
                'order_index' => 3
            ],
            [
                'key' => 'max_profile_picture_size',
                'value' => '2048',
                'type' => 'number',
                'description' => 'Maximum profile picture file size in KB',
                'default_value' => '2048',
                'validation_rules' => ['min:512', 'max:10240'],
                'order_index' => 4
            ],
            [
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
                'order_index' => 5
            ]
        ];

        foreach ($profileSettings as $setting) {
            SettingsItem::firstOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'group_id' => $profileGroup->id,
                    'is_active' => true
                ])
            );
        }
    }

    private function createAccountSecuritySettings($accountSecurityGroup)
    {
        $securitySettings = [
            [
                'key' => 'password_min_length',
                'value' => '8',
                'type' => 'number',
                'description' => 'Minimum password length',
                'default_value' => '8',
                'validation_rules' => ['min:6', 'max:32'],
                'order_index' => 1
            ],
            [
                'key' => 'password_require_uppercase',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require uppercase letters in passwords',
                'default_value' => 'true',
                'order_index' => 2
            ],
            [
                'key' => 'password_require_lowercase',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require lowercase letters in passwords',
                'default_value' => 'true',
                'order_index' => 3
            ],
            [
                'key' => 'password_require_numbers',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require numbers in passwords',
                'default_value' => 'true',
                'order_index' => 4
            ],
            [
                'key' => 'password_require_symbols',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Require special symbols in passwords',
                'default_value' => 'false',
                'order_index' => 5
            ],
            [
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
                'order_index' => 6
            ],
            [
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
                'order_index' => 7
            ]
        ];

        foreach ($securitySettings as $setting) {
            SettingsItem::firstOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'group_id' => $accountSecurityGroup->id,
                    'is_active' => true
                ])
            );
        }
    }

    private function createRegistrationSettings($registrationGroup)
    {
        $registrationSettings = [
            [
                'key' => 'registration_enabled',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Allow new user registrations',
                'default_value' => 'true',
                'order_index' => 1
            ],
            [
                'key' => 'email_verification_required',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require email verification for new accounts',
                'default_value' => 'true',
                'order_index' => 2
            ],
            [
                'key' => 'phone_verification_required',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Require phone number verification for new accounts',
                'default_value' => 'false',
                'order_index' => 3
            ],
            [
                'key' => 'admin_approval_required',
                'value' => 'false',
                'type' => 'boolean',
                'description' => 'Require admin approval for new user accounts',
                'default_value' => 'false',
                'order_index' => 4
            ],
            [
                'key' => 'registration_terms_required',
                'value' => 'true',
                'type' => 'boolean',
                'description' => 'Require users to accept terms and conditions during registration',
                'default_value' => 'true',
                'order_index' => 5
            ],
            [
                'key' => 'max_registrations_per_ip',
                'value' => '5',
                'type' => 'number',
                'description' => 'Maximum registrations allowed per IP address per day',
                'default_value' => '5',
                'validation_rules' => ['min:1', 'max:50'],
                'order_index' => 6
            ]
        ];

        foreach ($registrationSettings as $setting) {
            SettingsItem::firstOrCreate(
                ['key' => $setting['key']],
                array_merge($setting, [
                    'group_id' => $registrationGroup->id,
                    'is_active' => true
                ])
            );
        }
    }
}
