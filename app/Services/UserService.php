<?php

namespace App\Services;

use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UserService
{
    /**
     * Create a new user with role assignment
     */
    public function createUser(array $userData, string $role = 'tenant'): User
    {
        return DB::transaction(function () use ($userData, $role) {
            // Hash password if provided
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            // Generate email verification token if email is provided
            if (isset($userData['email']) && !empty($userData['email'])) {
                $userData['email_verification_token'] = Str::random(60);
            }

            // Create user
            $user = User::create($userData);

            // Assign role
            $this->assignRole($user, $role);

            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $role
            ]);

            return $user->load('roles');
        });
    }

    /**
     * Update user information
     */
    public function updateUser(User $user, array $userData): User
    {
        return DB::transaction(function () use ($user, $userData) {
            // Hash password if provided
            if (isset($userData['password'])) {
                $userData['password'] = Hash::make($userData['password']);
            }

            // Update user
            $user->update($userData);

            Log::info('User updated successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return $user->fresh();
        });
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser(User $user): bool
    {
        return DB::transaction(function () use ($user) {
            // Revoke all roles and permissions
            $user->roles()->detach();
            $user->permissions()->detach();

            // Soft delete user
            $result = $user->delete();

            Log::info('User deleted successfully', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return $result;
        });
    }

    /**
     * Assign role to user
     */
    public function assignRole(User $user, string $roleName): User
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            throw new \InvalidArgumentException("Role '{$roleName}' not found");
        }

        if (!$user->hasRole($roleName)) {
            $user->assignRole($roleName);
            
            Log::info('Role assigned to user', [
                'user_id' => $user->id,
                'role' => $roleName
            ]);
        }

        return $user->fresh();
    }

    /**
     * Remove role from user
     */
    public function removeRole(User $user, string $roleName): User
    {
        if ($user->hasRole($roleName)) {
            $user->removeRole($roleName);
            
            Log::info('Role removed from user', [
                'user_id' => $user->id,
                'role' => $roleName
            ]);
        }

        return $user->fresh();
    }

    /**
     * Assign permission to user
     */
    public function assignPermission(User $user, string $permissionName): User
    {
        $permission = Permission::where('name', $permissionName)->first();
        
        if (!$permission) {
            throw new \InvalidArgumentException("Permission '{$permissionName}' not found");
        }

        if (!$user->hasPermissionTo($permissionName)) {
            $user->givePermissionTo($permissionName);
            
            Log::info('Permission assigned to user', [
                'user_id' => $user->id,
                'permission' => $permissionName
            ]);
        }

        return $user->fresh();
    }

    /**
     * Remove permission from user
     */
    public function removePermission(User $user, string $permissionName): User
    {
        if ($user->hasPermissionTo($permissionName)) {
            $user->revokePermissionTo($permissionName);
            
            Log::info('Permission removed from user', [
                'user_id' => $user->id,
                'permission' => $permissionName
            ]);
        }

        return $user->fresh();
    }

    /**
     * Verify user email
     */
    public function verifyEmail(User $user, string $token): bool
    {
        if ($user->email_verification_token === $token) {
            $user->update([
                'email_verified_at' => now(),
                'email_verification_token' => null
            ]);

            Log::info('User email verified', [
                'user_id' => $user->id,
                'email' => $user->email
            ]);

            return true;
        }

        return false;
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user, string $newPassword): User
    {
        $user->update([
            'password' => Hash::make($newPassword),
            'password_reset_token' => null,
            'password_reset_expires_at' => null
        ]);

        Log::info('User password reset', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return $user->fresh();
    }

    /**
     * Generate password reset token
     */
    public function generatePasswordResetToken(User $user): string
    {
        $token = Str::random(60);
        
        $user->update([
            'password_reset_token' => $token,
            'password_reset_expires_at' => now()->addHours(1)
        ]);

        Log::info('Password reset token generated', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return $token;
    }

    /**
     * Activate user account
     */
    public function activateUser(User $user): User
    {
        $user->update(['is_active' => true]);

        Log::info('User account activated', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return $user->fresh();
    }

    /**
     * Deactivate user account
     */
    public function deactivateUser(User $user): User
    {
        $user->update(['is_active' => false]);

        Log::info('User account deactivated', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return $user->fresh();
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics(): array
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();
        $verifiedUsers = User::whereNotNull('email_verified_at')->count();
        $unverifiedUsers = User::whereNull('email_verified_at')->count();

        $roleStats = User::selectRaw('roles.name as role_name, COUNT(*) as count')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', User::class)
            ->groupBy('roles.name')
            ->get();

        $monthlyStats = User::selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
            ->where('created_at', '>=', now()->subMonths(12))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'inactive_users' => $inactiveUsers,
            'verified_users' => $verifiedUsers,
            'unverified_users' => $unverifiedUsers,
            'role_statistics' => $roleStats,
            'monthly_statistics' => $monthlyStats
        ];
    }

    /**
     * Search users with criteria
     */
    public function searchUsers(array $criteria): \Illuminate\Database\Eloquent\Collection
    {
        $query = User::query();

        // Name search
        if (isset($criteria['name'])) {
            $query->where(function ($q) use ($criteria) {
                $q->where('first_name', 'like', '%' . $criteria['name'] . '%')
                  ->orWhere('last_name', 'like', '%' . $criteria['name'] . '%')
                  ->orWhere('name', 'like', '%' . $criteria['name'] . '%');
            });
        }

        // Email search
        if (isset($criteria['email'])) {
            $query->where('email', 'like', '%' . $criteria['email'] . '%');
        }

        // Phone search
        if (isset($criteria['phone'])) {
            $query->where('phone', 'like', '%' . $criteria['phone'] . '%');
        }

        // Role filter
        if (isset($criteria['role'])) {
            $query->whereHas('roles', function ($q) use ($criteria) {
                $q->where('name', $criteria['role']);
            });
        }

        // Status filter
        if (isset($criteria['is_active'])) {
            $query->where('is_active', $criteria['is_active']);
        }

        // Email verification filter
        if (isset($criteria['email_verified'])) {
            if ($criteria['email_verified']) {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Date range filter
        if (isset($criteria['created_from']) && isset($criteria['created_to'])) {
            $query->whereBetween('created_at', [$criteria['created_from'], $criteria['created_to']]);
        }

        return $query->with('roles')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $roleName): \Illuminate\Database\Eloquent\Collection
    {
        return User::role($roleName)->with('roles')->get();
    }

    /**
     * Get active users
     */
    public function getActiveUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('is_active', true)->with('roles')->get();
    }

    /**
     * Get inactive users
     */
    public function getInactiveUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('is_active', false)->with('roles')->get();
    }

    /**
     * Get verified users
     */
    public function getVerifiedUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereNotNull('email_verified_at')->with('roles')->get();
    }

    /**
     * Get unverified users
     */
    public function getUnverifiedUsers(): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereNull('email_verified_at')->with('roles')->get();
    }

    /**
     * Validate user data
     */
    public function validateUserData(array $data, bool $isUpdate = false): array
    {
        $errors = [];

        // Required fields for creation
        if (!$isUpdate) {
            if (!isset($data['email']) || empty($data['email'])) {
                $errors[] = 'Email is required';
            } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format';
            } elseif (User::where('email', $data['email'])->exists()) {
                $errors[] = 'Email already exists';
            }

            if (!isset($data['password']) || empty($data['password'])) {
                $errors[] = 'Password is required';
            } elseif (strlen($data['password']) < 8) {
                $errors[] = 'Password must be at least 8 characters';
            }
        }

        // Optional field validation
        if (isset($data['phone']) && !empty($data['phone'])) {
            if (!preg_match('/^[0-9+\-\s()]+$/', $data['phone'])) {
                $errors[] = 'Invalid phone number format';
            }
        }

        if (isset($data['email']) && !empty($data['email']) && !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email format';
        }

        return $errors;
    }

    /**
     * Bulk assign role to users
     */
    public function bulkAssignRole(array $userIds, string $roleName): int
    {
        $role = Role::where('name', $roleName)->first();
        
        if (!$role) {
            throw new \InvalidArgumentException("Role '{$roleName}' not found");
        }

        $users = User::whereIn('id', $userIds)->get();
        $assignedCount = 0;

        foreach ($users as $user) {
            if (!$user->hasRole($roleName)) {
                $user->assignRole($roleName);
                $assignedCount++;
            }
        }

        Log::info('Bulk role assignment completed', [
            'role' => $roleName,
            'assigned_count' => $assignedCount,
            'total_users' => count($userIds)
        ]);

        return $assignedCount;
    }

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivateUsers(array $userIds): int
    {
        $deactivatedCount = User::whereIn('id', $userIds)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        Log::info('Bulk user deactivation completed', [
            'deactivated_count' => $deactivatedCount,
            'total_users' => count($userIds)
        ]);

        return $deactivatedCount;
    }
}
