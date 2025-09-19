<?php

namespace App\Services;

use App\Models\UserActivity;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class UserActivityService
{
    /**
     * Log user activity
     */
    public function logActivity(
        User $user,
        string $action,
        string $description,
        Request $request = null,
        array $metadata = []
    ): UserActivity {
        $activity = UserActivity::create([
            'user_id' => $user->id,
            'action' => $action,
            'description' => $description,
            'ip_address' => $request ? $request->ip() : null,
            'user_agent' => $request ? $request->userAgent() : null,
            'metadata' => $metadata,
        ]);

        Log::info('User activity logged', [
            'user_id' => $user->id,
            'action' => $action,
            'activity_id' => $activity->id
        ]);

        return $activity;
    }

    /**
     * Log login activity
     */
    public function logLogin(User $user, Request $request): UserActivity
    {
        return $this->logActivity(
            $user,
            'login',
            'User logged in successfully',
            $request,
            [
                'login_method' => 'email',
                'session_id' => $request->session()->getId(),
            ]
        );
    }

    /**
     * Log logout activity
     */
    public function logLogout(User $user, Request $request): UserActivity
    {
        return $this->logActivity(
            $user,
            'logout',
            'User logged out',
            $request
        );
    }

    /**
     * Log profile update activity
     */
    public function logProfileUpdate(User $user, array $updatedFields, Request $request = null): UserActivity
    {
        return $this->logActivity(
            $user,
            'profile_updated',
            'User profile updated',
            $request,
            [
                'updated_fields' => array_keys($updatedFields),
                'fields_count' => count($updatedFields),
            ]
        );
    }

    /**
     * Log password change activity
     */
    public function logPasswordChange(User $user, Request $request = null): UserActivity
    {
        return $this->logActivity(
            $user,
            'password_changed',
            'User password changed',
            $request,
            [
                'password_changed_at' => now()->toISOString(),
            ]
        );
    }

    /**
     * Log permission grant activity
     */
    public function logPermissionGrant(User $user, string $permission, User $grantedBy = null, Request $request = null): UserActivity
    {
        return $this->logActivity(
            $user,
            'permission_granted',
            "Permission '{$permission}' granted to user",
            $request,
            [
                'permission' => $permission,
                'granted_by' => $grantedBy ? $grantedBy->id : null,
                'granted_by_name' => $grantedBy ? $grantedBy->name : null,
            ]
        );
    }

    /**
     * Log permission revoke activity
     */
    public function logPermissionRevoke(User $user, string $permission, User $revokedBy = null, Request $request = null): UserActivity
    {
        return $this->logActivity(
            $user,
            'permission_revoked',
            "Permission '{$permission}' revoked from user",
            $request,
            [
                'permission' => $permission,
                'revoked_by' => $revokedBy ? $revokedBy->id : null,
                'revoked_by_name' => $revokedBy ? $revokedBy->name : null,
            ]
        );
    }

    /**
     * Log role assignment activity
     */
    public function logRoleAssignment(User $user, string $role, User $assignedBy = null, Request $request = null): UserActivity
    {
        return $this->logActivity(
            $user,
            'role_assigned',
            "Role '{$role}' assigned to user",
            $request,
            [
                'role' => $role,
                'assigned_by' => $assignedBy ? $assignedBy->id : null,
                'assigned_by_name' => $assignedBy ? $assignedBy->name : null,
            ]
        );
    }

    /**
     * Log role removal activity
     */
    public function logRoleRemoval(User $user, string $role, User $removedBy = null, Request $request = null): UserActivity
    {
        return $this->logActivity(
            $user,
            'role_removed',
            "Role '{$role}' removed from user",
            $request,
            [
                'role' => $role,
                'removed_by' => $removedBy ? $removedBy->id : null,
                'removed_by_name' => $removedBy ? $removedBy->name : null,
            ]
        );
    }

    /**
     * Log data export activity
     */
    public function logDataExport(User $user, string $dataType, Request $request = null): UserActivity
    {
        return $this->logActivity(
            $user,
            'data_exported',
            "User data exported: {$dataType}",
            $request,
            [
                'data_type' => $dataType,
                'export_format' => 'json',
            ]
        );
    }

    /**
     * Log account deletion activity
     */
    public function logAccountDeletion(User $user, User $deletedBy = null, Request $request = null): UserActivity
    {
        return $this->logActivity(
            $user,
            'account_deleted',
            'User account deleted',
            $request,
            [
                'deleted_by' => $deletedBy ? $deletedBy->id : null,
                'deleted_by_name' => $deletedBy ? $deletedBy->name : null,
                'deletion_reason' => 'user_request',
            ]
        );
    }

    /**
     * Get user activities with pagination
     */
    public function getUserActivities(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return UserActivity::where('user_id', $userId)
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all activities with filtering
     */
    public function getAllActivities(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        $query = UserActivity::with('user');

        // Apply filters
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        if (isset($filters['ip_address'])) {
            $query->where('ip_address', $filters['ip_address']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('action', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    /**
     * Get activity statistics
     */
    public function getActivityStatistics(): array
    {
        $totalActivities = UserActivity::count();
        $uniqueUsers = UserActivity::distinct('user_id')->count();
        
        $activitiesByAction = UserActivity::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc')
            ->pluck('count', 'action');

        $recentActivities = UserActivity::where('created_at', '>=', now()->subDays(7))->count();
        
        $topUsers = UserActivity::selectRaw('user_id, COUNT(*) as activity_count')
            ->with('user:id,name,email')
            ->groupBy('user_id')
            ->orderBy('activity_count', 'desc')
            ->limit(10)
            ->get();

        return [
            'total_activities' => $totalActivities,
            'unique_users' => $uniqueUsers,
            'activities_by_action' => $activitiesByAction,
            'recent_activities' => $recentActivities,
            'top_active_users' => $topUsers,
        ];
    }

    /**
     * Get user activity summary
     */
    public function getUserActivitySummary(int $userId): array
    {
        $user = User::find($userId);
        if (!$user) {
            return [];
        }

        $totalActivities = UserActivity::where('user_id', $userId)->count();
        $lastActivity = UserActivity::where('user_id', $userId)->latest()->first();
        
        $activitiesByAction = UserActivity::where('user_id', $userId)
            ->selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->pluck('count', 'action');

        $recentActivities = UserActivity::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(7))
            ->count();

        return [
            'user' => $user,
            'total_activities' => $totalActivities,
            'last_activity' => $lastActivity,
            'activities_by_action' => $activitiesByAction,
            'recent_activities' => $recentActivities,
        ];
    }

    /**
     * Clean up old activities
     */
    public function cleanupOldActivities(int $daysToKeep = 90): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        
        $deletedCount = UserActivity::where('created_at', '<', $cutoffDate)->delete();

        Log::info('Old user activities cleaned up', [
            'deleted_count' => $deletedCount,
            'cutoff_date' => $cutoffDate->toDateString(),
        ]);

        return $deletedCount;
    }

    /**
     * Export user activities
     */
    public function exportUserActivities(int $userId, array $filters = []): array
    {
        $query = UserActivity::where('user_id', $userId)->with('user');

        // Apply filters
        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }

        $activities = $query->orderBy('created_at', 'desc')->get();

        return [
            'user' => User::find($userId),
            'activities' => $activities->toArray(),
            'exported_at' => now()->toISOString(),
            'total_count' => $activities->count(),
        ];
    }

    /**
     * Get suspicious activities
     */
    public function getSuspiciousActivities(): Collection
    {
        // Activities from different IP addresses
        $differentIpActivities = UserActivity::selectRaw('user_id, COUNT(DISTINCT ip_address) as ip_count')
            ->groupBy('user_id')
            ->having('ip_count', '>', 3)
            ->with('user')
            ->get();

        // Multiple failed login attempts
        $failedLogins = UserActivity::where('action', 'login_failed')
            ->where('created_at', '>=', now()->subHours(24))
            ->selectRaw('ip_address, COUNT(*) as attempt_count')
            ->groupBy('ip_address')
            ->having('attempt_count', '>', 5)
            ->get();

        // Unusual activity patterns
        $unusualActivities = UserActivity::where('created_at', '>=', now()->subDays(7))
            ->selectRaw('user_id, COUNT(*) as activity_count')
            ->groupBy('user_id')
            ->having('activity_count', '>', 100)
            ->with('user')
            ->get();

        return collect([
            'different_ip_activities' => $differentIpActivities,
            'failed_logins' => $failedLogins,
            'unusual_activities' => $unusualActivities,
        ]);
    }
}

