<?php

namespace App\Services;

use App\Models\User;
use App\Models\Property;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SecurityService
{
    /**
     * Audit property access and modifications
     */
    public function auditPropertyAccess($propertyId, $userId, $action, $details = []): void
    {
        $user = User::find($userId);
        $property = Property::find($propertyId);

        $logData = [
            'user_id' => $userId,
            'user_name' => $user->name ?? 'Unknown',
            'property_id' => $propertyId,
            'property_name' => $property->name ?? 'Unknown',
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'timestamp' => Carbon::now(),
        ];

        // Log to application log
        Log::info('Property Access Audit', $logData);
    }

    /**
     * Monitor suspicious user activity
     */
    public function monitorSuspiciousActivity($userId): array
    {
        $user = User::find($userId);
        $suspiciousActivities = [];

        // Check for multiple failed login attempts
        $failedLogins = ActivityLog::where('causer_id', $userId)
            ->where('description', 'like', '%failed login%')
            ->where('created_at', '>=', Carbon::now()->subHours(24))
            ->count();

        if ($failedLogins > 5) {
            $suspiciousActivities[] = [
                'type' => 'multiple_failed_logins',
                'severity' => 'high',
                'message' => "User has {$failedLogins} failed login attempts",
            ];
        }

        return [
            'user_id' => $userId,
            'user_name' => $user->name,
            'suspicious_activities' => $suspiciousActivities,
            'risk_level' => count($suspiciousActivities) > 0 ? 'high' : 'low',
        ];
    }

    /**
     * Generate security audit report
     */
    public function generateSecurityAuditReport($period = 30): array
    {
        $startDate = Carbon::now()->subDays($period);
        
        $totalAccess = ActivityLog::where('created_at', '>=', $startDate)->count();
        $uniqueUsers = ActivityLog::where('created_at', '>=', $startDate)
            ->distinct('causer_id')
            ->count();

        return [
            'period_days' => $period,
            'report_date' => Carbon::now()->toDateString(),
            'total_access_attempts' => $totalAccess,
            'unique_users' => $uniqueUsers,
            'security_score' => $this->calculateSecurityScore($totalAccess),
        ];
    }

    /**
     * Calculate security score
     */
    private function calculateSecurityScore($totalAccess): int
    {
        $baseScore = 100;
        $score = $baseScore - ($totalAccess > 1000 ? 20 : 0);
        return max(0, min(100, $score));
    }
}