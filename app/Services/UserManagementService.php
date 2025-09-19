<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserManagementService
{
    /**
     * Get all users with pagination and filtering
     */
    public function getAllUsers(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = User::with(['roles', 'permissions'])
            ->withCount(['properties', 'leases', 'payments']);

        // Apply filters
        if (isset($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        if (isset($filters['status'])) {
            $query->where('is_active', $filters['status'] === 'active');
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get user by ID with full details
     */
    public function getUserById(string $id): ?User
    {
        return User::with([
            'roles.permissions',
            'permissions',
            'properties',
            'leases',
            'payments'
        ])->find($id);
    }

    /**
     * Create a new user
     */
    public function createUser(array $data): User
    {
        DB::beginTransaction();
        
        try {
            // Create user
            $user = User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'] ?? null,
                'password' => Hash::make($data['password'] ?? 'temporary123'),
                'is_active' => $data['is_active'] ?? true,
                'email_verified_at' => $data['email_verified_at'] ?? null,
            ]);

            // Assign role if provided
            if (isset($data['role'])) {
                $user->assignRole($data['role']);
            }

            // Assign permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $user->givePermissionTo($data['permissions']);
            }

            // Send welcome notification if needed
            if ($data['send_welcome'] ?? false) {
                $expiresAt = now()->addDays(config('app.invitation_link_expiry_days', 365));
                $user->sendWelcomeNotification($expiresAt);
            }

            DB::commit();
            
            Log::info('User created successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $data['role'] ?? null
            ]);

            return $user;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user', [
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Update user information
     */
    public function updateUser(string $id, array $data): User
    {
        DB::beginTransaction();
        
        try {
            $user = User::findOrFail($id);
            
            $updateData = array_filter([
                'name' => $data['name'] ?? null,
                'email' => $data['email'] ?? null,
                'phone' => $data['phone'] ?? null,
                'is_active' => $data['is_active'] ?? null,
            ], function ($value) {
                return $value !== null;
            });

            $user->update($updateData);

            // Update role if provided
            if (isset($data['role'])) {
                $user->syncRoles([$data['role']]);
            }

            // Update permissions if provided
            if (isset($data['permissions']) && is_array($data['permissions'])) {
                $user->syncPermissions($data['permissions']);
            }

            DB::commit();
            
            Log::info('User updated successfully', [
                'user_id' => $user->id,
                'updated_fields' => array_keys($updateData)
            ]);

            return $user->fresh();

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user', [
                'user_id' => $id,
                'data' => $data,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Delete user (soft delete)
     */
    public function deleteUser(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $user = User::findOrFail($id);
            
            // Prevent deleting yourself
            if (auth()->id() === $user->id) {
                throw new \Exception('You cannot delete your own account');
            }

            $user->delete();

            DB::commit();
            
            Log::info('User deleted successfully', [
                'user_id' => $id,
                'deleted_by' => auth()->id()
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Restore soft deleted user
     */
    public function restoreUser(string $id): User
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        Log::info('User restored successfully', [
            'user_id' => $id,
            'restored_by' => auth()->id()
        ]);

        return $user;
    }

    /**
     * Permanently delete user
     */
    public function forceDeleteUser(string $id): bool
    {
        DB::beginTransaction();
        
        try {
            $user = User::withTrashed()->findOrFail($id);
            
            // Prevent deleting yourself
            if (auth()->id() === $user->id) {
                throw new \Exception('You cannot delete your own account');
            }

            $user->forceDelete();

            DB::commit();
            
            Log::info('User permanently deleted', [
                'user_id' => $id,
                'deleted_by' => auth()->id()
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to permanently delete user', [
                'user_id' => $id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleUserStatus(string $id): User
    {
        $user = User::findOrFail($id);
        $user->update(['is_active' => !$user->is_active]);

        Log::info('User status toggled', [
            'user_id' => $id,
            'new_status' => $user->is_active,
            'toggled_by' => auth()->id()
        ]);

        return $user;
    }

    /**
     * Reset user password
     */
    public function resetUserPassword(string $id, string $newPassword = null): User
    {
        $user = User::findOrFail($id);
        $password = $newPassword ?? $this->generateSecurePassword();
        
        $user->update([
            'password' => Hash::make($password),
            'password_changed_at' => now()
        ]);

        Log::info('User password reset', [
            'user_id' => $id,
            'reset_by' => auth()->id()
        ]);

        return $user;
    }

    /**
     * Get users by role
     */
    public function getUsersByRole(string $role): Collection
    {
        return User::role($role)
            ->with(['roles', 'permissions'])
            ->get();
    }

    /**
     * Get user statistics
     */
    public function getUserStatistics(): array
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('is_active', true)->count(),
            'inactive_users' => User::where('is_active', false)->count(),
            'users_by_role' => Role::withCount('users')->get()->pluck('users_count', 'name'),
            'recent_registrations' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'unverified_users' => User::whereNull('email_verified_at')->count(),
        ];
    }

    /**
     * Bulk operations on users
     */
    public function bulkAction(array $userIds, string $action, array $data = []): array
    {
        $results = [];
        
        foreach ($userIds as $userId) {
            try {
                switch ($action) {
                    case 'activate':
                        $this->toggleUserStatus($userId);
                        $results[$userId] = 'activated';
                        break;
                    case 'deactivate':
                        $user = User::findOrFail($userId);
                        $user->update(['is_active' => false]);
                        $results[$userId] = 'deactivated';
                        break;
                    case 'delete':
                        $this->deleteUser($userId);
                        $results[$userId] = 'deleted';
                        break;
                    case 'assign_role':
                        if (isset($data['role'])) {
                            $user = User::findOrFail($userId);
                            $user->assignRole($data['role']);
                            $results[$userId] = 'role_assigned';
                        }
                        break;
                    case 'remove_role':
                        if (isset($data['role'])) {
                            $user = User::findOrFail($userId);
                            $user->removeRole($data['role']);
                            $results[$userId] = 'role_removed';
                        }
                        break;
                }
            } catch (\Exception $e) {
                $results[$userId] = 'error: ' . $e->getMessage();
            }
        }

        return $results;
    }

    /**
     * Generate secure password
     */
    private function generateSecurePassword(int $length = 12): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*';
        $password = '';
        
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $password;
    }

    /**
     * Get user activity summary
     */
    public function getUserActivitySummary(string $userId): array
    {
        $user = User::findOrFail($userId);
        
        return [
            'properties_count' => $user->properties()->count(),
            'leases_count' => $user->leases()->count(),
            'payments_count' => $user->payments()->count(),
            'last_login' => $user->last_login_at,
            'created_at' => $user->created_at,
            'email_verified' => $user->email_verified_at !== null,
        ];
    }
}

