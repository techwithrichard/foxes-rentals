<?php

namespace App\Services;

use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPreference;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdvancedUserManagementService
{
    protected $cachePrefix = 'user_management_';
    protected $cacheTtl = 3600; // 1 hour

    /**
     * Get comprehensive user statistics
     */
    public function getUserStatistics(?int $userId = null): array
    {
        $cacheKey = $this->cachePrefix . 'statistics' . ($userId ? "_user_{$userId}" : '');
        
        return Cache::remember($cacheKey, $this->cacheTtl, function () use ($userId) {
            if ($userId) {
                return $this->getIndividualUserStatistics($userId);
            }
            
            return [
                'total_users' => User::count(),
                'active_users' => User::where('is_active', true)->count(),
                'inactive_users' => User::where('is_active', false)->count(),
                'users_with_profiles' => User::has('profile')->count(),
                'users_with_preferences' => User::has('preferences')->count(),
                'users_by_role' => $this->getUsersByRole(),
                'recent_registrations' => User::where('created_at', '>=', now()->subDays(30))->count(),
                'login_statistics' => $this->getLoginStatistics(),
                'activity_statistics' => $this->getActivityStatistics(),
                'security_statistics' => $this->getSecurityStatistics()
            ];
        });
    }

    /**
     * Get individual user statistics
     */
    protected function getIndividualUserStatistics(int $userId): array
    {
        $user = User::findOrFail($userId);
        
        return [
            'user_id' => $userId,
            'profile_completion' => $user->profile ? $user->profile->completion_percentage : 0,
            'last_login' => $user->last_login_at,
            'total_activities' => $user->activities()->count(),
            'recent_activities' => $user->activities()->recent(7)->count(),
            'security_activities' => $user->activities()->security()->count(),
            'failed_logins' => $user->activities()->where('activity_name', 'failed_login')->count(),
            'role_count' => $user->roles()->count(),
            'permission_count' => $user->permissions()->count(),
            'preferences_set' => $user->preferences ? count($user->preferences->preferences ?? []) : 0
        ];
    }

    /**
     * Get users by role
     */
    protected function getUsersByRole(): array
    {
        return \Spatie\Permission\Models\Role::withCount('users')
            ->get()
            ->mapWithKeys(function ($role) {
                return [$role->name => $role->users_count];
            })
            ->toArray();
    }

    /**
     * Get login statistics
     */
    protected function getLoginStatistics(): array
    {
        $lastWeek = now()->subWeek();
        $lastMonth = now()->subMonth();
        
        return [
            'logins_today' => UserActivity::where('activity_name', 'login')
                ->whereDate('created_at', today())
                ->count(),
            'logins_this_week' => UserActivity::where('activity_name', 'login')
                ->where('created_at', '>=', $lastWeek)
                ->count(),
            'logins_this_month' => UserActivity::where('activity_name', 'login')
                ->where('created_at', '>=', $lastMonth)
                ->count(),
            'failed_logins_today' => UserActivity::where('activity_name', 'failed_login')
                ->whereDate('created_at', today())
                ->count(),
            'unique_users_logged_in_today' => UserActivity::where('activity_name', 'login')
                ->whereDate('created_at', today())
                ->distinct('user_id')
                ->count()
        ];
    }

    /**
     * Get activity statistics
     */
    protected function getActivityStatistics(): array
    {
        return [
            'total_activities_today' => UserActivity::whereDate('created_at', today())->count(),
            'total_activities_this_week' => UserActivity::where('created_at', '>=', now()->subWeek())->count(),
            'total_activities_this_month' => UserActivity::where('created_at', '>=', now()->subMonth())->count(),
            'most_active_users' => $this->getMostActiveUsers(),
            'activity_by_type' => $this->getActivityByType(),
            'activity_by_hour' => $this->getActivityByHour()
        ];
    }

    /**
     * Get security statistics
     */
    protected function getSecurityStatistics(): array
    {
        return [
            'failed_logins_today' => UserActivity::where('activity_name', 'failed_login')
                ->whereDate('created_at', today())
                ->count(),
            'password_resets_today' => UserActivity::where('activity_name', 'password_reset')
                ->whereDate('created_at', today())
                ->count(),
            'security_alerts_today' => UserActivity::where('activity_name', 'security_alert')
                ->whereDate('created_at', today())
                ->count(),
            'suspicious_ips' => $this->getSuspiciousIPs(),
            'multiple_failed_logins' => $this->getMultipleFailedLogins()
        ];
    }

    /**
     * Get most active users
     */
    protected function getMostActiveUsers(): array
    {
        return UserActivity::selectRaw('user_id, COUNT(*) as activity_count')
            ->where('created_at', '>=', now()->subWeek())
            ->groupBy('user_id')
            ->orderByDesc('activity_count')
            ->limit(10)
            ->with('user:id,name,email')
            ->get()
            ->map(function ($activity) {
                return [
                    'user_id' => $activity->user_id,
                    'user_name' => $activity->user->name ?? 'Unknown',
                    'user_email' => $activity->user->email ?? 'Unknown',
                    'activity_count' => $activity->activity_count
                ];
            })
            ->toArray();
    }

    /**
     * Get activity by type
     */
    protected function getActivityByType(): array
    {
        return UserActivity::selectRaw('activity_type, COUNT(*) as count')
            ->where('created_at', '>=', now()->subWeek())
            ->groupBy('activity_type')
            ->orderByDesc('count')
            ->pluck('count', 'activity_type')
            ->toArray();
    }

    /**
     * Get activity by hour
     */
    protected function getActivityByHour(): array
    {
        return UserActivity::selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
            ->where('created_at', '>=', now()->subWeek())
            ->groupBy('hour')
            ->orderBy('hour')
            ->pluck('count', 'hour')
            ->toArray();
    }

    /**
     * Get suspicious IPs
     */
    protected function getSuspiciousIPs(): array
    {
        return UserActivity::selectRaw('ip_address, COUNT(*) as count')
            ->where('activity_name', 'failed_login')
            ->where('created_at', '>=', now()->subWeek())
            ->groupBy('ip_address')
            ->having('count', '>', 5)
            ->orderByDesc('count')
            ->limit(10)
            ->pluck('count', 'ip_address')
            ->toArray();
    }

    /**
     * Get multiple failed logins
     */
    protected function getMultipleFailedLogins(): array
    {
        return UserActivity::selectRaw('user_id, COUNT(*) as count')
            ->where('activity_name', 'failed_login')
            ->where('created_at', '>=', now()->subWeek())
            ->groupBy('user_id')
            ->having('count', '>', 3)
            ->orderByDesc('count')
            ->limit(10)
            ->with('user:id,name,email')
            ->get()
            ->map(function ($activity) {
                return [
                    'user_id' => $activity->user_id,
                    'user_name' => $activity->user->name ?? 'Unknown',
                    'user_email' => $activity->user->email ?? 'Unknown',
                    'failed_attempts' => $activity->count
                ];
            })
            ->toArray();
    }

    /**
     * Log user activity
     */
    public function logUserActivity(int $userId, string $activityName, array $metadata = [], $subject = null, $causer = null): void
    {
        try {
            UserActivity::create([
                'user_id' => $userId,
                'activity_type' => $this->getActivityType($activityName),
                'activity_name' => $activityName,
                'description' => $this->generateActivityDescription($activityName, $metadata),
                'metadata' => $metadata,
                'subject_type' => $subject ? get_class($subject) : null,
                'subject_id' => $subject ? $subject->id : null,
                'causer_type' => $causer ? get_class($causer) : null,
                'causer_id' => $causer ? $causer->id : null,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'session_id' => session()->getId()
            ]);

            // Clear relevant caches
            $this->clearUserStatisticsCache($userId);
            $this->clearGeneralStatisticsCache();

        } catch (\Exception $e) {
            Log::error("Error logging user activity: " . $e->getMessage());
        }
    }

    /**
     * Log bulk user activity
     */
    public function logBulkUserActivity(array $userIds, string $action, array $metadata = [], $causer = null): void
    {
        foreach ($userIds as $userId) {
            $this->logUserActivity($userId, $action, $metadata, null, $causer);
        }
    }

    /**
     * Get user activity log
     */
    public function getUserActivityLog(int $userId, int $days = 30, int $limit = 50): array
    {
        return UserActivity::forUser($userId)
            ->where('created_at', '>=', now()->subDays($days))
            ->with(['user', 'subject', 'causer'])
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function ($activity) {
                return [
                    'id' => $activity->id,
                    'activity_type' => $activity->activity_type,
                    'activity_name' => $activity->activity_name,
                    'description' => $activity->formatted_description,
                    'metadata' => $activity->metadata,
                    'ip_address' => $activity->ip_address,
                    'browser' => $activity->browser,
                    'operating_system' => $activity->operating_system,
                    'device_type' => $activity->device_type,
                    'location' => $activity->location,
                    'created_at' => $activity->created_at,
                    'icon' => $activity->icon,
                    'color' => $activity->color
                ];
            })
            ->toArray();
    }

    /**
     * Import users from CSV
     */
    public function importUsersFromCsv($file, int $importedBy): array
    {
        $results = [
            'imported' => 0,
            'updated' => 0,
            'errors' => 0,
            'error_details' => []
        ];

        try {
            $csvData = array_map('str_getcsv', file($file->getPathname()));
            $headers = array_shift($csvData);

            foreach ($csvData as $index => $row) {
                try {
                    $userData = array_combine($headers, $row);
                    
                    // Validate required fields
                    if (empty($userData['email']) || empty($userData['name'])) {
                        throw new \Exception("Missing required fields (email, name)");
                    }

                    // Check if user exists
                    $existingUser = User::where('email', $userData['email'])->first();
                    
                    if ($existingUser) {
                        // Update existing user
                        $existingUser->update([
                            'name' => $userData['name'],
                            'phone' => $userData['phone'] ?? $existingUser->phone,
                            'is_active' => isset($userData['is_active']) ? (bool)$userData['is_active'] : $existingUser->is_active
                        ]);
                        $results['updated']++;
                    } else {
                        // Create new user
                        $user = User::create([
                            'name' => $userData['name'],
                            'email' => $userData['email'],
                            'phone' => $userData['phone'] ?? null,
                            'password' => Hash::make($userData['password'] ?? 'password123'),
                            'is_active' => isset($userData['is_active']) ? (bool)$userData['is_active'] : true,
                            'email_verified_at' => now()
                        ]);

                        // Assign role if specified
                        if (!empty($userData['role'])) {
                            $role = \Spatie\Permission\Models\Role::where('name', $userData['role'])->first();
                            if ($role) {
                                $user->assignRole($role);
                            }
                        }

                        $results['imported']++;
                    }

                } catch (\Exception $e) {
                    $results['errors']++;
                    $results['error_details'][] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            // Log import activity
            $this->logUserActivity($importedBy, 'data_imported', [
                'import_type' => 'users',
                'results' => $results
            ]);

            $this->clearGeneralStatisticsCache();

        } catch (\Exception $e) {
            Log::error("Error importing users from CSV: " . $e->getMessage());
            throw $e;
        }

        return $results;
    }

    /**
     * Export users to CSV
     */
    public function exportUsersToCsv(array $filters = []): string
    {
        $query = User::with(['roles', 'profile']);

        // Apply filters
        if (!empty($filters['role'])) {
            $query->role($filters['role']);
        }

        if (!empty($filters['status'])) {
            if ($filters['status'] === 'active') {
                $query->where('is_active', true);
            } elseif ($filters['status'] === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get();

        // Prepare CSV data
        $csvData = [];
        $csvData[] = [
            'Name',
            'Email',
            'Phone',
            'Roles',
            'Status',
            'Profile Complete',
            'Last Login',
            'Created At'
        ];

        foreach ($users as $user) {
            $csvData[] = [
                $user->name,
                $user->email,
                $user->phone ?? '',
                $user->roles->pluck('name')->join(', '),
                $user->is_active ? 'Active' : 'Inactive',
                $user->profile ? ($user->profile->is_complete ? 'Yes' : 'No') : 'No',
                $user->last_login_at ? $user->last_login_at->format('Y-m-d H:i:s') : 'Never',
                $user->created_at->format('Y-m-d H:i:s')
            ];
        }

        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csvString = stream_get_contents($output);
        fclose($output);

        return $csvString;
    }

    /**
     * Get activity type from activity name
     */
    protected function getActivityType(string $activityName): string
    {
        $typeMap = [
            'login' => 'authentication',
            'logout' => 'authentication',
            'failed_login' => 'authentication',
            'password_reset' => 'authentication',
            'user_created' => 'user_management',
            'user_updated' => 'user_management',
            'user_deleted' => 'user_management',
            'user_activated' => 'user_management',
            'user_deactivated' => 'user_management',
            'role_assigned' => 'user_management',
            'permission_granted' => 'user_management',
            'property_created' => 'property_management',
            'property_updated' => 'property_management',
            'property_deleted' => 'property_management',
            'lease_created' => 'lease_management',
            'lease_updated' => 'lease_management',
            'lease_terminated' => 'lease_management',
            'payment_received' => 'payment_processing',
            'payment_failed' => 'payment_processing',
            'settings_updated' => 'system_settings',
            'data_exported' => 'data_export',
            'data_imported' => 'data_import',
            'security_alert' => 'security',
            'api_access' => 'api_usage',
            'bulk_action' => 'user_management'
        ];

        return $typeMap[$activityName] ?? 'system_settings';
    }

    /**
     * Generate activity description
     */
    protected function generateActivityDescription(string $activityName, array $metadata): string
    {
        $user = auth()->user();
        $userName = $user ? $user->name : 'System';

        switch ($activityName) {
            case 'user_created':
                return "{$userName} created a new user";
            case 'user_updated':
                return "{$userName} updated user information";
            case 'user_deleted':
                return "{$userName} deleted a user";
            case 'user_activated':
                return "{$userName} activated a user";
            case 'user_deactivated':
                return "{$userName} deactivated a user";
            case 'bulk_action':
                $count = $metadata['affected_count'] ?? 0;
                return "{$userName} performed bulk action on {$count} users";
            default:
                return "{$userName} performed {$activityName}";
        }
    }

    /**
     * Clear user statistics cache
     */
    protected function clearUserStatisticsCache(int $userId): void
    {
        Cache::forget($this->cachePrefix . 'statistics_user_' . $userId);
    }

    /**
     * Clear general statistics cache
     */
    protected function clearGeneralStatisticsCache(): void
    {
        Cache::forget($this->cachePrefix . 'statistics');
    }

    /**
     * Clear all user management caches
     */
    public function clearAllCache(): void
    {
        $this->clearGeneralStatisticsCache();
        
        // Clear user-specific caches
        User::all()->each(function ($user) {
            $this->clearUserStatisticsCache($user->id);
        });
    }
}
