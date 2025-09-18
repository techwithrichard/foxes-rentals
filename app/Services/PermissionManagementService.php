<?php

namespace App\Services;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;

class PermissionManagementService
{
    /**
     * Get all permissions with grouping
     */
    public function getAllPermissions(): Collection
    {
        return Permission::orderBy('group')
            ->orderBy('name')
            ->get()
            ->groupBy('group');
    }

    /**
     * Get permissions by group
     */
    public function getPermissionsByGroup(string $group): Collection
    {
        return Permission::where('group', $group)
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new permission
     */
    public function createPermission(array $data): Permission
    {
        $permission = Permission::create([
            'name' => $data['name'],
            'guard_name' => 'web',
            'description' => $data['description'] ?? '',
            'group' => $data['group'] ?? 'general',
        ]);

        Log::info('Permission created', [
            'permission_name' => $permission->name,
            'group' => $permission->group
        ]);

        $this->clearPermissionCache();

        return $permission;
    }

    /**
     * Update permission
     */
    public function updatePermission(Permission $permission, array $data): Permission
    {
        $permission->update([
            'description' => $data['description'] ?? $permission->description,
            'group' => $data['group'] ?? $permission->group,
        ]);

        Log::info('Permission updated', [
            'permission_id' => $permission->id,
            'permission_name' => $permission->name
        ]);

        $this->clearPermissionCache();

        return $permission;
    }

    /**
     * Delete permission
     */
    public function deletePermission(Permission $permission): bool
    {
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            throw new \Exception('Cannot delete permission that is assigned to roles');
        }

        $permission->delete();

        Log::info('Permission deleted', [
            'permission_id' => $permission->id,
            'permission_name' => $permission->name
        ]);

        $this->clearPermissionCache();

        return true;
    }

    /**
     * Bulk create permissions
     */
    public function bulkCreatePermissions(array $permissions): array
    {
        $created = [];
        $errors = [];

        foreach ($permissions as $permissionData) {
            try {
                $permission = $this->createPermission($permissionData);
                $created[] = $permission;
            } catch (\Exception $e) {
                $errors[] = [
                    'permission' => $permissionData,
                    'error' => $e->getMessage()
                ];
            }
        }

        return [
            'created' => $created,
            'errors' => $errors
        ];
    }

    /**
     * Get all roles with their permissions
     */
    public function getAllRoles(): Collection
    {
        return Role::with('permissions')
            ->orderBy('name')
            ->get();
    }

    /**
     * Create a new role
     */
    public function createRole(array $data): Role
    {
        $role = Role::create([
            'name' => $data['name'],
            'guard_name' => 'web',
            'display_name' => $data['display_name'] ?? ucfirst($data['name']),
            'description' => $data['description'] ?? '',
            'color' => $data['color'] ?? '#6c757d',
            'icon' => $data['icon'] ?? 'fas fa-user',
        ]);

        // Assign permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $role->givePermissionTo($data['permissions']);
        }

        Log::info('Role created', [
            'role_name' => $role->name,
            'permissions_count' => $role->permissions()->count()
        ]);

        $this->clearRoleCache();

        return $role;
    }

    /**
     * Update role
     */
    public function updateRole(Role $role, array $data): Role
    {
        $role->update([
            'display_name' => $data['display_name'] ?? $role->display_name,
            'description' => $data['description'] ?? $role->description,
            'color' => $data['color'] ?? $role->color,
            'icon' => $data['icon'] ?? $role->icon,
        ]);

        // Update permissions if provided
        if (isset($data['permissions']) && is_array($data['permissions'])) {
            $role->syncPermissions($data['permissions']);
        }

        Log::info('Role updated', [
            'role_id' => $role->id,
            'role_name' => $role->name
        ]);

        $this->clearRoleCache();

        return $role;
    }

    /**
     * Delete role
     */
    public function deleteRole(Role $role): bool
    {
        // Check if role is assigned to any users
        if ($role->users()->count() > 0) {
            throw new \Exception('Cannot delete role that is assigned to users');
        }

        $role->delete();

        Log::info('Role deleted', [
            'role_id' => $role->id,
            'role_name' => $role->name
        ]);

        $this->clearRoleCache();

        return true;
    }

    /**
     * Assign permission to role
     */
    public function assignPermissionToRole(Role $role, string $permission): Role
    {
        $role->givePermissionTo($permission);

        Log::info('Permission assigned to role', [
            'role_name' => $role->name,
            'permission' => $permission
        ]);

        $this->clearRoleCache();

        return $role;
    }

    /**
     * Remove permission from role
     */
    public function removePermissionFromRole(Role $role, string $permission): Role
    {
        $role->revokePermissionTo($permission);

        Log::info('Permission removed from role', [
            'role_name' => $role->name,
            'permission' => $permission
        ]);

        $this->clearRoleCache();

        return $role;
    }

    /**
     * Assign role to user
     */
    public function assignRoleToUser(User $user, string $role): User
    {
        $user->assignRole($role);

        Log::info('Role assigned to user', [
            'user_id' => $user->id,
            'role' => $role
        ]);

        $this->clearUserCache($user);

        return $user;
    }

    /**
     * Remove role from user
     */
    public function removeRoleFromUser(User $user, string $role): User
    {
        $user->removeRole($role);

        Log::info('Role removed from user', [
            'user_id' => $user->id,
            'role' => $role
        ]);

        $this->clearUserCache($user);

        return $user;
    }

    /**
     * Assign permission to user
     */
    public function assignPermissionToUser(User $user, string $permission): User
    {
        $user->givePermissionTo($permission);

        Log::info('Permission assigned to user', [
            'user_id' => $user->id,
            'permission' => $permission
        ]);

        $this->clearUserCache($user);

        return $user;
    }

    /**
     * Remove permission from user
     */
    public function removePermissionFromUser(User $user, string $permission): User
    {
        $user->revokePermissionTo($permission);

        Log::info('Permission removed from user', [
            'user_id' => $user->id,
            'permission' => $permission
        ]);

        $this->clearUserCache($user);

        return $user;
    }

    /**
     * Get user's effective permissions
     */
    public function getUserEffectivePermissions(User $user): Collection
    {
        return Cache::remember(
            "user_effective_permissions_{$user->id}",
            300, // 5 minutes
            fn() => $user->getAllPermissions()
        );
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
     * Check if user can manage another user
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
     * Get permission statistics
     */
    public function getPermissionStatistics(): array
    {
        return [
            'total_permissions' => Permission::count(),
            'permissions_by_group' => Permission::selectRaw('group, COUNT(*) as count')
                ->groupBy('group')
                ->pluck('count', 'group'),
            'total_roles' => Role::count(),
            'roles_with_permissions' => Role::whereHas('permissions')->count(),
            'unused_permissions' => Permission::whereDoesntHave('roles')->count(),
        ];
    }

    /**
     * Clear permission cache
     */
    public function clearPermissionCache(): void
    {
        Cache::forget('permissions_all');
        Cache::forget('permissions_by_group');
    }

    /**
     * Clear role cache
     */
    public function clearRoleCache(): void
    {
        Cache::forget('roles_all');
        Cache::forget('roles_with_permissions');
    }

    /**
     * Clear user cache
     */
    public function clearUserCache(User $user): void
    {
        Cache::forget("user_effective_permissions_{$user->id}");
        Cache::forget("user_roles_{$user->id}");
        Cache::forget("user_permissions_{$user->id}");
    }

    /**
     * Clear all permission-related cache
     */
    public function clearAllCache(): void
    {
        $this->clearPermissionCache();
        $this->clearRoleCache();
        
        // Clear all user permission caches
        Cache::flush();
    }

    /**
     * Export permissions and roles
     */
    public function exportPermissionsAndRoles(): array
    {
        return [
            'permissions' => Permission::all()->toArray(),
            'roles' => Role::with('permissions')->get()->toArray(),
            'exported_at' => now()->toISOString(),
        ];
    }

    /**
     * Import permissions and roles
     */
    public function importPermissionsAndRoles(array $data): array
    {
        $results = [
            'permissions' => ['created' => 0, 'updated' => 0, 'errors' => []],
            'roles' => ['created' => 0, 'updated' => 0, 'errors' => []],
        ];

        // Import permissions
        if (isset($data['permissions'])) {
            foreach ($data['permissions'] as $permissionData) {
                try {
                    $permission = Permission::updateOrCreate(
                        ['name' => $permissionData['name']],
                        $permissionData
                    );
                    
                    if ($permission->wasRecentlyCreated) {
                        $results['permissions']['created']++;
                    } else {
                        $results['permissions']['updated']++;
                    }
                } catch (\Exception $e) {
                    $results['permissions']['errors'][] = [
                        'permission' => $permissionData,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }

        // Import roles
        if (isset($data['roles'])) {
            foreach ($data['roles'] as $roleData) {
                try {
                    $permissions = $roleData['permissions'] ?? [];
                    unset($roleData['permissions']);
                    
                    $role = Role::updateOrCreate(
                        ['name' => $roleData['name']],
                        $roleData
                    );
                    
                    if ($permissions) {
                        $role->syncPermissions($permissions);
                    }
                    
                    if ($role->wasRecentlyCreated) {
                        $results['roles']['created']++;
                    } else {
                        $results['roles']['updated']++;
                    }
                } catch (\Exception $e) {
                    $results['roles']['errors'][] = [
                        'role' => $roleData,
                        'error' => $e->getMessage()
                    ];
                }
            }
        }

        $this->clearAllCache();

        return $results;
    }
}
