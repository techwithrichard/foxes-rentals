<?php

namespace App\Http\Controllers;

use App\Services\UserManagementService;
use App\Services\RoleBasedAccessControlService;
use App\Services\UserActivityService;
use App\Services\PasswordSecurityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected UserManagementService $userService;
    protected RoleBasedAccessControlService $rbacService;
    protected UserActivityService $activityService;
    protected PasswordSecurityService $passwordService;

    public function __construct(
        UserManagementService $userService,
        RoleBasedAccessControlService $rbacService,
        UserActivityService $activityService,
        PasswordSecurityService $passwordService
    ) {
        $this->middleware('auth');
        $this->userService = $userService;
        $this->rbacService = $rbacService;
        $this->activityService = $activityService;
        $this->passwordService = $passwordService;
    }

    /**
     * Show unified dashboard with role-based content filtering
     */
    public function index()
    {
        $user = Auth::user();
        
        // Collect all relevant data based on user's roles and permissions
        $data = [
            'user' => $user,
            'user_roles' => $user->getRoleNames(),
            'permissions' => $user->getAllPermissions()->pluck('name'),
            'dashboard_sections' => $this->getDashboardSections($user),
            'statistics' => $this->getRoleBasedStatistics($user),
            'recent_activities' => $this->getRecentActivities(10),
            'system_alerts' => $this->getSystemAlerts(),
            'quick_actions' => $this->getQuickActions($user),
        ];

        return view('dashboard.unified', $data);
    }

    /**
     * Get dashboard sections based on user roles and permissions
     */
    protected function getDashboardSections($user)
    {
        $sections = [];

        // Admin/Staff sections
        if ($user->hasAnyRole(['super_admin', 'admin', 'manager', 'agent'])) {
            $sections['admin'] = [
                'title' => 'System Administration',
                'icon' => 'ni ni-setting',
                'widgets' => $this->getAdminWidgets($user),
                'visible' => true
            ];
        }

        // Landlord sections
        if ($user->hasRole('landlord')) {
            $sections['landlord'] = [
                'title' => 'Property Management',
                'icon' => 'ni ni-home',
                'widgets' => $this->getLandlordWidgets($user),
                'visible' => true
            ];
        }

        // Tenant sections
        if ($user->hasRole('tenant')) {
            $sections['tenant'] = [
                'title' => 'My Lease',
                'icon' => 'ni ni-user',
                'widgets' => $this->getTenantWidgets($user),
                'visible' => true
            ];
        }

        return $sections;
    }

    /**
     * Get role-based statistics
     */
    protected function getRoleBasedStatistics($user)
    {
        $stats = [];

        // Admin statistics
        if ($user->hasAnyRole(['super_admin', 'admin'])) {
            $stats = array_merge($stats, $this->getAdminStatistics());
        }

        // Staff statistics (subset of admin)
        if ($user->hasAnyRole(['manager', 'agent'])) {
            $stats = array_merge($stats, $this->getStaffStatistics());
        }

        // Landlord statistics
        if ($user->hasRole('landlord')) {
            $stats = array_merge($stats, $this->getLandlordStatistics());
        }

        // Tenant statistics
        if ($user->hasRole('tenant')) {
            $stats = array_merge($stats, $this->getTenantStatistics());
        }

        return $stats;
    }

    /**
     * Get quick actions based on user permissions
     */
    protected function getQuickActions($user)
    {
        $actions = [];

        if ($user->can('create property')) {
            $actions[] = [
                'title' => 'Add Property',
                'url' => route('admin.properties.create'),
                'icon' => 'ni ni-plus',
                'color' => 'primary'
            ];
        }

        if ($user->can('create lease')) {
            $actions[] = [
                'title' => 'Create Lease',
                'url' => route('admin.leases.create'),
                'icon' => 'ni ni-file-text',
                'color' => 'success'
            ];
        }

        if ($user->can('view reports')) {
            $actions[] = [
                'title' => 'View Reports',
                'url' => route('admin.reports.index'),
                'icon' => 'ni ni-chart',
                'color' => 'info'
            ];
        }

        return $actions;
    }

    /**
     * Get admin widgets based on permissions
     */
    protected function getAdminWidgets($user)
    {
        $widgets = [];

        if ($user->can('view property')) {
            $widgets[] = [
                'component' => 'admin.properties-statistics-widget',
                'size' => 4
            ];
        }

        if ($user->can('view house')) {
            $widgets[] = [
                'component' => 'admin.houses-statistics-widget',
                'size' => 4
            ];
        }

        if ($user->can('view lease')) {
            $widgets[] = [
                'component' => 'admin.leases-statistics-widget',
                'size' => 4
            ];
        }

        return $widgets;
    }

    /**
     * Get landlord widgets
     */
    protected function getLandlordWidgets($user)
    {
        return [
            [
                'component' => 'landlord.properties-overview-widget',
                'size' => 6,
                'data' => ['properties' => $this->getLandlordProperties()]
            ],
            [
                'component' => 'landlord.recent-payments-widget',
                'size' => 6,
                'data' => ['payments' => $this->getRecentPayments()]
            ]
        ];
    }

    /**
     * Get tenant widgets
     */
    protected function getTenantWidgets($user)
    {
        return [
            [
                'component' => 'tenant.lease-info-widget',
                'size' => 6,
                'data' => ['lease_info' => $this->getTenantLeaseInfo()]
            ],
            [
                'component' => 'tenant.upcoming-payments-widget',
                'size' => 6,
                'data' => ['payments' => $this->getUpcomingPayments()]
            ]
        ];
    }

    /**
     * Get tenant-specific statistics
     */
    protected function getTenantStatistics()
    {
        $user = Auth::user();
        $lease = $user->leases()->where('status', 'active')->first();
        
        return [
            'lease_status' => $lease ? $lease->status : 'none',
            'outstanding_balance' => $this->getTenantOutstandingBalance(),
            'payment_history_count' => $this->getTenantPaymentHistory()->count(),
        ];
    }

    /**
     * Admin dashboard
     */
    protected function adminDashboard()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'statistics' => $this->getAdminStatistics(),
            'recent_activities' => $this->getRecentActivities(10),
            'system_alerts' => $this->getSystemAlerts(),
            'password_security' => $this->getPasswordSecurityInfo(),
        ];

        return view('admin.home.index', $data);
    }

    /**
     * Staff dashboard
     */
    protected function staffDashboard()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'statistics' => $this->getStaffStatistics(),
            'recent_activities' => $this->getRecentActivities(5),
            'assigned_tasks' => $this->getAssignedTasks(),
        ];

        return view('admin.home.index', $data);
    }

    /**
     * Landlord dashboard
     */
    protected function landlordDashboard()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'statistics' => $this->getLandlordStatistics(),
            'properties' => $this->getLandlordProperties(),
            'recent_payments' => $this->getRecentPayments(),
        ];

        return view('landlord.home.index', $data);
    }

    /**
     * Tenant dashboard
     */
    protected function tenantDashboard()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'lease_info' => $this->getTenantLeaseInfo(),
            'payment_history' => $this->getTenantPaymentHistory(),
            'upcoming_payments' => $this->getUpcomingPayments(),
            'maintenance_requests' => $this->getMaintenanceRequests(),
        ];

        return view('tenant.home.index', $data);
    }

    /**
     * Default dashboard
     */
    protected function defaultDashboard()
    {
        $user = Auth::user();
        
        $data = [
            'user' => $user,
            'recent_activities' => $this->getRecentActivities(5),
            'profile_completion' => $this->getProfileCompletion(),
        ];

        return view('dashboard.default', $data);
    }

    /**
     * Get admin statistics
     */
    protected function getAdminStatistics(): array
    {
        return [
            'total_users' => $this->userService->getUserStatistics()['total_users'],
            'active_users' => $this->userService->getUserStatistics()['active_users'],
            'total_properties' => \App\Models\Property::count(),
            'active_leases' => \App\Models\Lease::where('status', 'active')->count(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'pending_payments' => \App\Models\Payment::where('status', 'pending')->count(),
        ];
    }

    /**
     * Get staff statistics
     */
    protected function getStaffStatistics(): array
    {
        return [
            'assigned_properties' => $this->getAssignedPropertiesCount(),
            'pending_tasks' => $this->getPendingTasksCount(),
            'completed_tasks' => $this->getCompletedTasksCount(),
            'client_interactions' => $this->getClientInteractionsCount(),
        ];
    }

    /**
     * Get landlord statistics
     */
    protected function getLandlordStatistics(): array
    {
        $user = Auth::user();
        
        return [
            'total_properties' => $user->properties()->count(),
            'occupied_properties' => $user->properties()->where('is_vacant', false)->count(),
            'vacant_properties' => $user->properties()->where('is_vacant', true)->count(),
            'monthly_income' => $this->getLandlordMonthlyIncome(),
            'pending_payments' => $this->getLandlordPendingPayments(),
        ];
    }

    /**
     * Get recent activities
     */
    protected function getRecentActivities(int $limit = 5)
    {
        $user = Auth::user();
        
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return $this->activityService->getAllActivities([], $limit)->items();
        }

        return $this->activityService->getUserActivities($user->id, $limit)->items();
    }

    /**
     * Get system alerts
     */
    protected function getSystemAlerts(): array
    {
        $alerts = [];

        // Password expiry alerts
        $expiredPasswords = $this->passwordService->getUsersWithExpiredPasswords()->count();
        if ($expiredPasswords > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$expiredPasswords} users have expired passwords",
                'action' => 'admin.users.password-expired',
            ];
        }

        // System health alerts
        $systemHealth = $this->getSystemHealth();
        if ($systemHealth['status'] !== 'healthy') {
            $alerts[] = [
                'type' => 'danger',
                'message' => 'System health issues detected',
                'action' => 'admin.system-health',
            ];
        }

        return $alerts;
    }

    /**
     * Get password security information
     */
    protected function getPasswordSecurityInfo(): array
    {
        return $this->passwordService->getPasswordSecurityStatistics();
    }

    /**
     * Get profile completion percentage
     */
    protected function getProfileCompletion(): array
    {
        $user = Auth::user();
        $requiredFields = ['name', 'email', 'phone', 'address'];
        $completedFields = 0;

        foreach ($requiredFields as $field) {
            if (!empty($user->$field)) {
                $completedFields++;
            }
        }

        $percentage = ($completedFields / count($requiredFields)) * 100;

        return [
            'percentage' => $percentage,
            'completed_fields' => $completedFields,
            'total_fields' => count($requiredFields),
            'missing_fields' => array_diff($requiredFields, array_filter($requiredFields, fn($field) => !empty($user->$field))),
        ];
    }

    /**
     * Get assigned tasks (placeholder)
     */
    protected function getAssignedTasks(): array
    {
        // This would be implemented with an actual task management system
        return [
            [
                'id' => 1,
                'title' => 'Property inspection',
                'due_date' => now()->addDays(3),
                'priority' => 'high',
            ],
            [
                'id' => 2,
                'title' => 'Tenant communication',
                'due_date' => now()->addDays(1),
                'priority' => 'medium',
            ],
        ];
    }

    /**
     * Get landlord properties
     */
    protected function getLandlordProperties()
    {
        $user = Auth::user();
        return $user->properties()->with(['houses', 'leases'])->get();
    }

    /**
     * Get recent payments
     */
    protected function getRecentPayments()
    {
        $user = Auth::user();
        return $user->payments()->latest()->limit(5)->get();
    }

    /**
     * Get tenant lease info
     */
    protected function getTenantLeaseInfo()
    {
        $user = Auth::user();
        return $user->leases()->with(['property', 'house'])->latest()->first();
    }

    /**
     * Get tenant payment history
     */
    protected function getTenantPaymentHistory()
    {
        $user = Auth::user();
        return $user->payments()->latest()->limit(10)->get();
    }

    /**
     * Get upcoming payments
     */
    protected function getUpcomingPayments()
    {
        $user = Auth::user();
        return $user->payments()
            ->where('due_date', '>=', now())
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Get maintenance requests
     */
    protected function getMaintenanceRequests()
    {
        $user = Auth::user();
        // This would be implemented with an actual maintenance request system
        return collect([]);
    }

    /**
     * Get monthly revenue (placeholder)
     */
    protected function getMonthlyRevenue(): float
    {
        return \App\Models\Payment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Get assigned properties count (placeholder)
     */
    protected function getAssignedPropertiesCount(): int
    {
        return 0; // Would be implemented with actual assignment system
    }

    /**
     * Get pending tasks count (placeholder)
     */
    protected function getPendingTasksCount(): int
    {
        return 0; // Would be implemented with actual task system
    }

    /**
     * Get completed tasks count (placeholder)
     */
    protected function getCompletedTasksCount(): int
    {
        return 0; // Would be implemented with actual task system
    }

    /**
     * Get client interactions count (placeholder)
     */
    protected function getClientInteractionsCount(): int
    {
        return 0; // Would be implemented with actual interaction tracking
    }

    /**
     * Get landlord monthly income
     */
    protected function getLandlordMonthlyIncome(): float
    {
        $user = Auth::user();
        return $user->payments()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->where('status', 'completed')
            ->sum('amount');
    }

    /**
     * Get landlord pending payments
     */
    protected function getLandlordPendingPayments(): int
    {
        $user = Auth::user();
        return $user->payments()->where('status', 'pending')->count();
    }

    /**
     * Get system health (placeholder)
     */
    protected function getSystemHealth(): array
    {
        return [
            'status' => 'healthy',
            'uptime' => '99.9%',
            'response_time' => '120ms',
        ];
    }

    /**
     * Get dashboard data for API
     */
    public function getDashboardData()
    {
        $user = Auth::user();
        
        $data = [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'roles' => $user->roles->pluck('name'),
                'permissions' => $user->getAllPermissions()->pluck('name'),
            ],
            'statistics' => $this->getAdminStatistics(),
            'recent_activities' => $this->getRecentActivities(5),
            'profile_completion' => $this->getProfileCompletion(),
        ];

        return response()->json($data);
    }
}