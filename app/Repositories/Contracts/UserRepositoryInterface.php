<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * Find users by role
     */
    public function findByRole(string $roleName): Collection;

    /**
     * Find active users
     */
    public function findActive(): Collection;

    /**
     * Find inactive users
     */
    public function findInactive(): Collection;

    /**
     * Find verified users
     */
    public function findVerified(): Collection;

    /**
     * Find unverified users
     */
    public function findUnverified(): Collection;

    /**
     * Find users by email
     */
    public function findByEmail(string $email): ?User;

    /**
     * Find users by phone
     */
    public function findByPhone(string $phone): Collection;

    /**
     * Find users by name
     */
    public function findByName(string $name): Collection;

    /**
     * Find users by date range
     */
    public function findByDateRange(string $startDate, string $endDate): Collection;

    /**
     * Find users with recent login
     */
    public function findWithRecentLogin(int $days = 7): Collection;

    /**
     * Find users without recent login
     */
    public function findWithoutRecentLogin(int $days = 30): Collection;

    /**
     * Get user statistics
     */
    public function getStatistics(): array;

    /**
     * Get users by month
     */
    public function getByMonth(int $year, int $month): Collection;

    /**
     * Get users by year
     */
    public function getByYear(int $year): Collection;

    /**
     * Get user trends
     */
    public function getUserTrends(int $months = 12): array;

    /**
     * Search users with multiple criteria
     */
    public function searchUsers(array $criteria): Collection;

    /**
     * Find users requiring attention
     */
    public function findRequiringAttention(): Collection;

    /**
     * Get users with permissions
     */
    public function getWithPermissions(): Collection;

    /**
     * Get users with roles
     */
    public function getWithRoles(): Collection;

    /**
     * Find users by permission
     */
    public function findByPermission(string $permissionName): Collection;

    /**
     * Get user activity statistics
     */
    public function getActivityStatistics(): array;
}
