<?php

namespace App\Repositories;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    /**
     * Find users by role
     */
    public function findByRole(string $roleName): Collection
    {
        return $this->getQuery()
            ->role($roleName)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find active users
     */
    public function findActive(): Collection
    {
        return $this->getQuery()
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find inactive users
     */
    public function findInactive(): Collection
    {
        return $this->getQuery()
            ->where('is_active', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find verified users
     */
    public function findVerified(): Collection
    {
        return $this->getQuery()
            ->whereNotNull('email_verified_at')
            ->orderBy('email_verified_at', 'desc')
            ->get();
    }

    /**
     * Find unverified users
     */
    public function findUnverified(): Collection
    {
        return $this->getQuery()
            ->whereNull('email_verified_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find users by email
     */
    public function findByEmail(string $email): ?User
    {
        return $this->getQuery()
            ->where('email', $email)
            ->first();
    }

    /**
     * Find users by phone
     */
    public function findByPhone(string $phone): Collection
    {
        return $this->getQuery()
            ->where('phone', 'like', '%' . $phone . '%')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find users by name
     */
    public function findByName(string $name): Collection
    {
        return $this->getQuery()
            ->where(function ($query) use ($name) {
                $query->where('first_name', 'like', '%' . $name . '%')
                      ->orWhere('last_name', 'like', '%' . $name . '%')
                      ->orWhere('name', 'like', '%' . $name . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find users by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->getQuery()
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find users with recent login
     */
    public function findWithRecentLogin(int $days = 7): Collection
    {
        return $this->getQuery()
            ->where('last_login_at', '>=', now()->subDays($days))
            ->orderBy('last_login_at', 'desc')
            ->get();
    }

    /**
     * Find users without recent login
     */
    public function findWithoutRecentLogin(int $days = 30): Collection
    {
        return $this->getQuery()
            ->where(function ($query) use ($days) {
                $query->whereNull('last_login_at')
                      ->orWhere('last_login_at', '<', now()->subDays($days));
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user statistics
     */
    public function getStatistics(): array
    {
        $totalUsers = $this->getQuery()->count();
        $activeUsers = $this->getQuery()->where('is_active', true)->count();
        $inactiveUsers = $this->getQuery()->where('is_active', false)->count();
        $verifiedUsers = $this->getQuery()->whereNotNull('email_verified_at')->count();
        $unverifiedUsers = $this->getQuery()->whereNull('email_verified_at')->count();

        $roleStats = $this->getQuery()
            ->selectRaw('roles.name as role_name, COUNT(*) as count')
            ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
            ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
            ->where('model_has_roles.model_type', User::class)
            ->groupBy('roles.name')
            ->get();

        $monthlyStats = $this->getQuery()
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as month, COUNT(*) as count')
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
     * Get users by month
     */
    public function getByMonth(int $year, int $month): Collection
    {
        return $this->getQuery()
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get users by year
     */
    public function getByYear(int $year): Collection
    {
        return $this->getQuery()
            ->whereYear('created_at', $year)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user trends
     */
    public function getUserTrends(int $months = 12): array
    {
        $trends = [];
        
        for ($i = $months - 1; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('Y-m');
            
            $trends[] = [
                'month' => $month,
                'month_name' => $date->format('F Y'),
                'count' => $this->getQuery()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
            ];
        }

        return $trends;
    }

    /**
     * Search users with multiple criteria
     */
    public function searchUsers(array $criteria): Collection
    {
        $query = $this->getQuery();

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

        // Last login filter
        if (isset($criteria['last_login_days'])) {
            $query->where('last_login_at', '>=', now()->subDays($criteria['last_login_days']));
        }

        return $query->with('roles')->orderBy('created_at', 'desc')->get();
    }

    /**
     * Find users requiring attention
     */
    public function findRequiringAttention(): Collection
    {
        return $this->getQuery()
            ->where(function ($query) {
                $query->where('is_active', false)
                      ->orWhereNull('email_verified_at')
                      ->orWhere('last_login_at', '<', now()->subDays(30));
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get users with permissions
     */
    public function getWithPermissions(): Collection
    {
        return $this->getQuery()
            ->with(['roles.permissions', 'permissions'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get users with roles
     */
    public function getWithRoles(): Collection
    {
        return $this->getQuery()
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Find users by permission
     */
    public function findByPermission(string $permissionName): Collection
    {
        return $this->getQuery()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->orWhereHas('roles.permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get user activity statistics
     */
    public function getActivityStatistics(): array
    {
        $totalUsers = $this->getQuery()->count();
        $activeUsers = $this->getQuery()->where('is_active', true)->count();
        $recentLogins = $this->getQuery()->where('last_login_at', '>=', now()->subDays(7))->count();
        $verifiedUsers = $this->getQuery()->whereNotNull('email_verified_at')->count();

        $loginTrends = $this->getQuery()
            ->selectRaw('DATE_FORMAT(last_login_at, "%Y-%m-%d") as date, COUNT(*) as count')
            ->where('last_login_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return [
            'total_users' => $totalUsers,
            'active_users' => $activeUsers,
            'recent_logins' => $recentLogins,
            'verified_users' => $verifiedUsers,
            'login_trends' => $loginTrends,
            'activity_rate' => $totalUsers > 0 ? round(($recentLogins / $totalUsers) * 100, 2) : 0
        ];
    }

    /**
     * Get recent users
     */
    public function getRecent(int $limit = 10): Collection
    {
        return $this->getQuery()
            ->with('roles')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get users by location (if address is available)
     */
    public function getByLocation(string $location): Collection
    {
        return $this->getQuery()
            ->whereHas('addresses', function ($query) use ($location) {
                $query->where('city', 'like', '%' . $location . '%')
                      ->orWhere('state', 'like', '%' . $location . '%')
                      ->orWhere('country', 'like', '%' . $location . '%');
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Get users with specific role and permission
     */
    public function getByRoleAndPermission(string $roleName, string $permissionName): Collection
    {
        return $this->getQuery()
            ->whereHas('roles', function ($query) use ($roleName) {
                $query->where('name', $roleName);
            })
            ->whereHas('roles.permissions', function ($query) use ($permissionName) {
                $query->where('name', $permissionName);
            })
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
