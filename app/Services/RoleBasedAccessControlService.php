<?php

namespace App\Services;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class RoleBasedAccessControlService
{
    /**
     * Define all system permissions
     */
    public function defineSystemPermissions(): array
    {
        return [
            // User Management Permissions
            'user_management' => [
                'view_users' => 'View users list',
                'create_users' => 'Create new users',
                'edit_users' => 'Edit user information',
                'delete_users' => 'Delete users',
                'manage_user_roles' => 'Assign/remove user roles',
                'manage_user_permissions' => 'Assign/remove user permissions',
                'view_user_activity' => 'View user activity logs',
                'reset_user_passwords' => 'Reset user passwords',
                'toggle_user_status' => 'Activate/deactivate users',
            ],

            // Property Management Permissions
            'property_management' => [
                'view_properties' => 'View properties list',
                'create_properties' => 'Create new properties',
                'edit_properties' => 'Edit property information',
                'delete_properties' => 'Delete properties',
                'manage_property_types' => 'Manage property types',
                'manage_property_amenities' => 'Manage property amenities',
                'toggle_property_status' => 'Toggle property status',
                'manage_property_images' => 'Manage property images',
                'view_property_analytics' => 'View property analytics',
            ],

            // Lease Management Permissions
            'lease_management' => [
                'view_leases' => 'View leases list',
                'create_leases' => 'Create new leases',
                'edit_leases' => 'Edit lease information',
                'delete_leases' => 'Delete leases',
                'renew_leases' => 'Renew existing leases',
                'terminate_leases' => 'Terminate leases',
                'view_lease_history' => 'View lease history',
                'manage_lease_templates' => 'Manage lease templates',
                'generate_lease_documents' => 'Generate lease documents',
            ],

            // Payment Management Permissions
            'payment_management' => [
                'view_payments' => 'View payments list',
                'create_payments' => 'Create new payments',
                'edit_payments' => 'Edit payment information',
                'delete_payments' => 'Delete payments',
                'verify_payments' => 'Verify payments',
                'process_payments' => 'Process payments',
                'refund_payments' => 'Refund payments',
                'view_payment_reports' => 'View payment reports',
                'manage_payment_methods' => 'Manage payment methods',
            ],

            // Financial Management Permissions
            'financial_management' => [
                'view_financial_reports' => 'View financial reports',
                'create_invoices' => 'Create invoices',
                'edit_invoices' => 'Edit invoices',
                'delete_invoices' => 'Delete invoices',
                'manage_expenses' => 'Manage expenses',
                'view_profit_loss' => 'View profit/loss statements',
                'manage_budgets' => 'Manage budgets',
                'view_cash_flow' => 'View cash flow reports',
            ],

            // System Administration Permissions
            'system_administration' => [
                'view_system_settings' => 'View system settings',
                'edit_system_settings' => 'Edit system settings',
                'manage_roles' => 'Manage roles',
                'manage_permissions' => 'Manage permissions',
                'view_system_logs' => 'View system logs',
                'manage_backups' => 'Manage system backups',
                'view_system_health' => 'View system health',
                'manage_api_keys' => 'Manage API keys',
            ],

            // Reporting Permissions
            'reporting' => [
                'view_reports' => 'View all reports',
                'create_custom_reports' => 'Create custom reports',
                'export_reports' => 'Export reports',
                'schedule_reports' => 'Schedule reports',
                'view_analytics' => 'View analytics dashboard',
            ],

            // Communication Permissions
            'communication' => [
                'send_notifications' => 'Send notifications',
                'manage_email_templates' => 'Manage email templates',
                'view_communication_logs' => 'View communication logs',
                'manage_sms_settings' => 'Manage SMS settings',
            ],
        ];
    }

    /**
     * Define system roles with their permissions
     */
    public function defineSystemRoles(): array
    {
        return [
            'super_admin' => [
                'name' => 'Super Administrator',
                'description' => 'Full system access with all permissions',
                'permissions' => 'all',
                'color' => '#dc3545',
                'icon' => 'fas fa-crown',
            ],
            'admin' => [
                'name' => 'Administrator',
                'description' => 'Administrative access to most system features',
                'permissions' => [
                    'user_management' => ['view_users', 'create_users', 'edit_users', 'delete_users', 'manage_user_roles'],
                    'property_management' => ['view_properties', 'create_properties', 'edit_properties', 'delete_properties'],
                    'lease_management' => ['view_leases', 'create_leases', 'edit_leases', 'delete_leases'],
                    'payment_management' => ['view_payments', 'create_payments', 'edit_payments', 'verify_payments'],
                    'financial_management' => ['view_financial_reports', 'create_invoices', 'edit_invoices'],
                    'reporting' => ['view_reports', 'export_reports', 'view_analytics'],
                    'communication' => ['send_notifications', 'view_communication_logs'],
                ],
                'color' => '#6f42c1',
                'icon' => 'fas fa-user-shield',
            ],
            'manager' => [
                'name' => 'Manager',
                'description' => 'Management access to properties and tenants',
                'permissions' => [
                    'property_management' => ['view_properties', 'create_properties', 'edit_properties'],
                    'lease_management' => ['view_leases', 'create_leases', 'edit_leases'],
                    'payment_management' => ['view_payments', 'create_payments', 'verify_payments'],
                    'financial_management' => ['view_financial_reports', 'create_invoices'],
                    'reporting' => ['view_reports', 'view_analytics'],
                ],
                'color' => '#0d6efd',
                'icon' => 'fas fa-user-tie',
            ],
            'agent' => [
                'name' => 'Property Agent',
                'description' => 'Agent access to property management',
                'permissions' => [
                    'property_management' => ['view_properties', 'create_properties', 'edit_properties'],
                    'lease_management' => ['view_leases', 'create_leases'],
                    'payment_management' => ['view_payments', 'create_payments'],
                    'reporting' => ['view_reports'],
                ],
                'color' => '#198754',
                'icon' => 'fas fa-user-check',
            ],
            'landlord' => [
                'name' => 'Landlord',
                'description' => 'Landlord access to their properties',
                'permissions' => [
                    'property_management' => ['view_properties'],
                    'lease_management' => ['view_leases'],
                    'payment_management' => ['view_payments'],
                    'financial_management' => ['view_financial_reports'],
                ],
                'color' => '#fd7e14',
                'icon' => 'fas fa-home',
            ],
            'tenant' => [
                'name' => 'Tenant',
                'description' => 'Tenant access to their lease information',
                'permissions' => [
                    'lease_management' => ['view_leases'],
                    'payment_management' => ['view_payments'],
                ],
                'color' => '#20c997',
                'icon' => 'fas fa-user',
            ],
        ];
    }

    /**
     * Initialize system permissions
     */
    public function initializePermissions(): void
    {
        $permissions = $this->defineSystemPermissions();
        
        foreach ($permissions as $group => $groupPermissions) {
            foreach ($groupPermissions as $permission => $description) {
                Permission::firstOrCreate(
                    ['name' => $permission],
                    [
                        'guard_name' => 'web',
                        'description' => $description,
                        'group' => $group,
                    ]
                );
            }
        }

        Log::info('System permissions initialized');
    }

    /**
     * Initialize system roles
     */
    public function initializeRoles(): void
    {
        $roles = $this->defineSystemRoles();
        
        foreach ($roles as $roleName => $roleData) {
            $role = Role::firstOrCreate(
                ['name' => $roleName],
                [
                    'guard_name' => 'web',
                    'display_name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'color' => $roleData['color'],
                    'icon' => $roleData['icon'],
                ]
            );

            // Assign permissions to role
            if ($roleData['permissions'] === 'all') {
                $role->givePermissionTo(Permission::all());
            } elseif (is_array($roleData['permissions'])) {
                $permissions = [];
                foreach ($roleData['permissions'] as $group => $groupPermissions) {
                    $permissions = array_merge($permissions, $groupPermissions);
                }
                $role->syncPermissions($permissions);
            }
        }

        Log::info('System roles initialized');
    }

    /**
     * Check if user has permission
     */
    public function hasPermission(User $user, string $permission): bool
    {
        return Cache::remember(
            "user_permission_{$user->id}_{$permission}",
            300, // 5 minutes
            fn() => $user->hasPermissionTo($permission)
        );
    }

    /**
     * Check if user has any of the given permissions
     */
    public function hasAnyPermission(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($user, $permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has all of the given permissions
     */
    public function hasAllPermissions(User $user, array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->hasPermission($user, $permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if user has role
     */
    public function hasRole(User $user, string $role): bool
    {
        return Cache::remember(
            "user_role_{$user->id}_{$role}",
            300, // 5 minutes
            fn() => $user->hasRole($role)
        );
    }

    /**
     * Check if user has any of the given roles
     */
    public function hasAnyRole(User $user, array $roles): bool
    {
        foreach ($roles as $role) {
            if ($this->hasRole($user, $role)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get user's effective permissions
     */
    public function getUserPermissions(User $user): Collection
    {
        return Cache::remember(
            "user_permissions_{$user->id}",
            300, // 5 minutes
            fn() => $user->getAllPermissions()
        );
    }

    /**
     * Get user's roles
     */
    public function getUserRoles(User $user): Collection
    {
        return Cache::remember(
            "user_roles_{$user->id}",
            300, // 5 minutes
            fn() => $user->roles
        );
    }

    /**
     * Clear user permission cache
     */
    public function clearUserCache(User $user): void
    {
        $patterns = [
            "user_permission_{$user->id}_*",
            "user_role_{$user->id}_*",
            "user_permissions_{$user->id}",
            "user_roles_{$user->id}",
        ];

        foreach ($patterns as $pattern) {
            Cache::forget($pattern);
        }
    }

    /**
     * Get permission groups
     */
    public function getPermissionGroups(): array
    {
        return Permission::select('group')
            ->distinct()
            ->pluck('group')
            ->filter()
            ->toArray();
    }

    /**
     * Get permissions by group
     */
    public function getPermissionsByGroup(string $group): Collection
    {
        return Permission::where('group', $group)->get();
    }

    /**
     * Create custom role
     */
    public function createCustomRole(string $name, array $permissions, array $metadata = []): Role
    {
        $role = Role::create([
            'name' => $name,
            'guard_name' => 'web',
            'display_name' => $metadata['display_name'] ?? ucfirst($name),
            'description' => $metadata['description'] ?? '',
            'color' => $metadata['color'] ?? '#6c757d',
            'icon' => $metadata['icon'] ?? 'fas fa-user',
        ]);

        $role->givePermissionTo($permissions);

        Log::info('Custom role created', [
            'role_name' => $name,
            'permissions_count' => count($permissions)
        ]);

        return $role;
    }

    /**
     * Update role permissions
     */
    public function updateRolePermissions(Role $role, array $permissions): Role
    {
        $role->syncPermissions($permissions);

        Log::info('Role permissions updated', [
            'role_name' => $role->name,
            'permissions_count' => count($permissions)
        ]);

        return $role;
    }

    /**
     * Get role hierarchy
     */
    public function getRoleHierarchy(): array
    {
        return [
            'super_admin' => 100,
            'admin' => 90,
            'manager' => 80,
            'agent' => 70,
            'landlord' => 60,
            'tenant' => 50,
        ];
    }

    /**
     * Check if user can manage another user based on role hierarchy
     */
    public function canManageUser(User $manager, User $target): bool
    {
        $hierarchy = $this->getRoleHierarchy();
        
        $managerLevel = 0;
        $targetLevel = 0;

        foreach ($manager->roles as $role) {
            $managerLevel = max($managerLevel, $hierarchy[$role->name] ?? 0);
        }

        foreach ($target->roles as $role) {
            $targetLevel = max($targetLevel, $hierarchy[$role->name] ?? 0);
        }

        return $managerLevel > $targetLevel;
    }

    /**
     * Get accessible routes for user
     */
    public function getAccessibleRoutes(User $user): array
    {
        $routes = [];
        
        if ($this->hasPermission($user, 'view_users')) {
            $routes[] = 'admin.users.index';
        }
        
        if ($this->hasPermission($user, 'create_users')) {
            $routes[] = 'admin.users.create';
        }
        
        if ($this->hasPermission($user, 'view_properties')) {
            $routes[] = 'admin.properties.index';
        }
        
        if ($this->hasPermission($user, 'create_properties')) {
            $routes[] = 'admin.properties.create';
        }
        
        if ($this->hasPermission($user, 'view_leases')) {
            $routes[] = 'admin.leases.index';
        }
        
        if ($this->hasPermission($user, 'view_payments')) {
            $routes[] = 'admin.payments.index';
        }
        
        if ($this->hasPermission($user, 'view_financial_reports')) {
            $routes[] = 'admin.reports.index';
        }

        return $routes;
    }
}

