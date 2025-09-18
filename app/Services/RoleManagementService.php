<?php

namespace App\Services;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RoleManagementService
{
    /**
     * Create a new role
     */
    public function createRole(array $roleData): Role
    {
        return DB::transaction(function () use ($roleData) {
            $role = Role::create($roleData);

            // Assign permissions if provided
            if (isset($roleData['permissions']) && is_array($roleData['permissions'])) {
                $this->assignPermissionsToRole($role, $roleData['permissions']);
            }

            Log::info('Role created successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name
            ]);

            return $role->load('permissions');
        });
    }

    /**
     * Update role
     */
    public function updateRole(Role $role, array $roleData): Role
    {
        return DB::transaction(function () use ($role, $roleData) {
            $role->update($roleData);

            // Update permissions if provided
            if (isset($roleData['permissions']) && is_array($roleData['permissions'])) {
                $this->syncPermissionsToRole($role, $roleData['permissions']);
            }

            Log::info('Role updated successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name
            ]);

            return $role->fresh()->load('permissions');
        });
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role): bool
    {
        return DB::transaction(function () use ($role) {
            // Check if role has users
            $userCount = User::role($role->name)->count();
            
            if ($userCount > 0) {
                throw new \Exception("Cannot delete role '{$role->name}' because it has {$userCount} users assigned");
            }

            // Remove all permissions
            $role->permissions()->detach();

            // Delete role
            $result = $role->delete();

            Log::info('Role deleted successfully', [
                'role_id' => $role->id,
                'role_name' => $role->name
            ]);

            return $result;
        });
    }

    /**
     * Create a new permission
     */
    public function createPermission(array $permissionData): Permission
    {
        return DB::transaction(function () use ($permissionData) {
            $permission = Permission::create($permissionData);

            Log::info('Permission created successfully', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name
            ]);

            return $permission;
        });
    }

    /**
     * Update permission
     */
    public function updatePermission(Permission $permission, array $permissionData): Permission
    {
        $permission->update($permissionData);

        Log::info('Permission updated successfully', [
            'permission_id' => $permission->id,
            'permission_name' => $permission->name
        ]);

        return $permission->fresh();
    }

    /**
     * Delete permission
     */
    public function deletePermission(Permission $permission): bool
    {
        return DB::transaction(function () use ($permission) {
            // Remove from all roles
            $permission->roles()->detach();

            // Remove from all users
            $permission->users()->detach();

            // Delete permission
            $result = $permission->delete();

            Log::info('Permission deleted successfully', [
                'permission_id' => $permission->id,
                'permission_name' => $permission->name
            ]);

            return $result;
        });
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissionsToRole(Role $role, array $permissionNames): Role
    {
        $permissions = Permission::whereIn('name', $permissionNames)->get();
        
        foreach ($permissions as $permission) {
            if (!$role->hasPermissionTo($permission->name)) {
                $role->givePermissionTo($permission->name);
            }
        }

        Log::info('Permissions assigned to role', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'permissions' => $permissionNames
        ]);

        return $role->fresh()->load('permissions');
    }

    /**
     * Remove permissions from role
     */
    public function removePermissionsFromRole(Role $role, array $permissionNames): Role
    {
        foreach ($permissionNames as $permissionName) {
            if ($role->hasPermissionTo($permissionName)) {
                $role->revokePermissionTo($permissionName);
            }
        }

        Log::info('Permissions removed from role', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'permissions' => $permissionNames
        ]);

        return $role->fresh()->load('permissions');
    }

    /**
     * Sync permissions to role (replace all permissions)
     */
    public function syncPermissionsToRole(Role $role, array $permissionNames): Role
    {
        $role->syncPermissions($permissionNames);

        Log::info('Permissions synced to role', [
            'role_id' => $role->id,
            'role_name' => $role->name,
            'permissions' => $permissionNames
        ]);

        return $role->fresh()->load('permissions');
    }

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions(string $roleName): ?Role
    {
        return Role::where('name', $roleName)->with('permissions')->first();
    }

    /**
     * Get all roles with permissions
     */
    public function getAllRolesWithPermissions(): \Illuminate\Database\Eloquent\Collection
    {
        return Role::with('permissions')->get();
    }

    /**
     * Get all permissions
     */
    public function getAllPermissions(): \Illuminate\Database\Eloquent\Collection
    {
        return Permission::all();
    }

    /**
     * Get permissions by group
     */
    public function getPermissionsByGroup(): array
    {
        $permissions = Permission::all();
        $grouped = [];

        foreach ($permissions as $permission) {
            $group = $permission->group ?? 'general';
            $grouped[$group][] = $permission;
        }

        return $grouped;
    }

    /**
     * Get role statistics
     */
    public function getRoleStatistics(): array
    {
        $totalRoles = Role::count();
        $totalPermissions = Permission::count();

        $roleStats = Role::selectRaw('roles.name, COUNT(model_has_roles.model_id) as user_count')
            ->leftJoin('model_has_roles', 'roles.id', '=', 'model_has_roles.role_id')
            ->where('model_has_roles.model_type', User::class)
            ->groupBy('roles.id', 'roles.name')
            ->get();

        $permissionStats = Permission::selectRaw('permissions.group, COUNT(*) as count')
            ->groupBy('permissions.group')
            ->get();

        return [
            'total_roles' => $totalRoles,
            'total_permissions' => $totalPermissions,
            'role_statistics' => $roleStats,
            'permission_statistics' => $permissionStats
        ];
    }

    /**
     * Create default roles and permissions
     */
    public function createDefaultRolesAndPermissions(): array
    {
        $created = [];

        // Create permissions
        $permissions = [
            // User Management
            ['name' => 'view user', 'display_name' => 'View Users', 'group' => 'user_management'],
            ['name' => 'create user', 'display_name' => 'Create Users', 'group' => 'user_management'],
            ['name' => 'edit user', 'display_name' => 'Edit Users', 'group' => 'user_management'],
            ['name' => 'delete user', 'display_name' => 'Delete Users', 'group' => 'user_management'],
            
            // Property Management
            ['name' => 'view property', 'display_name' => 'View Properties', 'group' => 'property_management'],
            ['name' => 'create property', 'display_name' => 'Create Properties', 'group' => 'property_management'],
            ['name' => 'edit property', 'display_name' => 'Edit Properties', 'group' => 'property_management'],
            ['name' => 'delete property', 'display_name' => 'Delete Properties', 'group' => 'property_management'],
            
            // Payment Management
            ['name' => 'view payment', 'display_name' => 'View Payments', 'group' => 'payment_management'],
            ['name' => 'create payment', 'display_name' => 'Create Payments', 'group' => 'payment_management'],
            ['name' => 'edit payment', 'display_name' => 'Edit Payments', 'group' => 'payment_management'],
            ['name' => 'delete payment', 'display_name' => 'Delete Payments', 'group' => 'payment_management'],
            ['name' => 'verify payment', 'display_name' => 'Verify Payments', 'group' => 'payment_management'],
            ['name' => 'process payment', 'display_name' => 'Process Payments', 'group' => 'payment_management'],
            ['name' => 'refund payment', 'display_name' => 'Refund Payments', 'group' => 'payment_management'],
            
            // Role Management
            ['name' => 'view role', 'display_name' => 'View Roles', 'group' => 'role_management'],
            ['name' => 'create role', 'display_name' => 'Create Roles', 'group' => 'role_management'],
            ['name' => 'edit role', 'display_name' => 'Edit Roles', 'group' => 'role_management'],
            ['name' => 'delete role', 'display_name' => 'Delete Roles', 'group' => 'role_management'],
            
            // Reports
            ['name' => 'view reports', 'display_name' => 'View Reports', 'group' => 'reports'],
            ['name' => 'export reports', 'display_name' => 'Export Reports', 'group' => 'reports'],
        ];

        foreach ($permissions as $permissionData) {
            if (!Permission::where('name', $permissionData['name'])->exists()) {
                $permission = $this->createPermission($permissionData);
                $created['permissions'][] = $permission;
            }
        }

        // Create roles
        $roles = [
            [
                'name' => 'super_admin',
                'display_name' => 'Super Administrator',
                'description' => 'Full system access',
                'permissions' => Permission::pluck('name')->toArray()
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administrator',
                'description' => 'Administrative access',
                'permissions' => [
                    'view user', 'create user', 'edit user',
                    'view property', 'create property', 'edit property', 'delete property',
                    'view payment', 'create payment', 'edit payment', 'verify payment', 'process payment',
                    'view reports', 'export reports'
                ]
            ],
            [
                'name' => 'landlord',
                'display_name' => 'Landlord',
                'description' => 'Property owner access',
                'permissions' => [
                    'view property', 'create property', 'edit property',
                    'view payment', 'view reports'
                ]
            ],
            [
                'name' => 'tenant',
                'display_name' => 'Tenant',
                'description' => 'Tenant access',
                'permissions' => [
                    'view property', 'view payment'
                ]
            ],
            [
                'name' => 'manager',
                'display_name' => 'Property Manager',
                'description' => 'Property management access',
                'permissions' => [
                    'view user', 'create user', 'edit user',
                    'view property', 'create property', 'edit property',
                    'view payment', 'create payment', 'edit payment', 'verify payment', 'process payment',
                    'view reports'
                ]
            ]
        ];

        foreach ($roles as $roleData) {
            if (!Role::where('name', $roleData['name'])->exists()) {
                $role = $this->createRole($roleData);
                $created['roles'][] = $role;
            }
        }

        Log::info('Default roles and permissions created', [
            'created_permissions' => count($created['permissions'] ?? []),
            'created_roles' => count($created['roles'] ?? [])
        ]);

        return $created;
    }

    /**
     * Validate role data
     */
    public function validateRoleData(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        if (!isset($data['name']) || empty($data['name'])) {
            $errors[] = 'Role name is required';
        } elseif (!preg_match('/^[a-z_]+$/', $data['name'])) {
            $errors[] = 'Role name must contain only lowercase letters and underscores';
        } elseif (!$isUpdate && Role::where('name', $data['name'])->exists()) {
            $errors[] = 'Role name already exists';
        }

        if (!isset($data['display_name']) || empty($data['display_name'])) {
            $errors[] = 'Display name is required';
        }

        return $errors;
    }

    /**
     * Validate permission data
     */
    public function validatePermissionData(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        if (!isset($data['name']) || empty($data['name'])) {
            $errors[] = 'Permission name is required';
        } elseif (!preg_match('/^[a-z_\s]+$/', $data['name'])) {
            $errors[] = 'Permission name must contain only lowercase letters, underscores, and spaces';
        } elseif (!$isUpdate && Permission::where('name', $data['name'])->exists()) {
            $errors[] = 'Permission name already exists';
        }

        if (!isset($data['display_name']) || empty($data['display_name'])) {
            $errors[] = 'Display name is required';
        }

        return $errors;
    }
}
