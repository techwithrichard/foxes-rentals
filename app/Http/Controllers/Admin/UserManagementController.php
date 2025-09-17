<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Expense;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role_or_permission:super_admin|admin']);
    }

    /**
     * Display the comprehensive user management dashboard
     */
    public function dashboard()
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        // Get user statistics
        $stats = [
            'total_users' => User::count(),
            'total_landlords' => User::role('landlord')->count(),
            'total_tenants' => User::role('tenant')->count(),
            'active_users' => User::where('email_verified_at', '!=', null)->count(),
            'pending_users' => User::where('email_verified_at', null)->count(),
            'total_roles' => Role::count(),
            'total_permissions' => Permission::count(),
        ];

        // Get recent users
        $recentUsers = User::with('roles')
            ->latest()
            ->limit(10)
            ->get();

        // Get user activity summary
        $userActivity = [
            'new_this_month' => User::whereMonth('created_at', now()->month)->count(),
            'active_this_month' => User::whereHas('loginActivities', function($query) {
                $query->whereMonth('created_at', now()->month);
            })->count(),
        ];

        return view('admin.user-management.dashboard', compact('stats', 'recentUsers', 'userActivity'));
    }

    /**
     * Display landlord properties management
     */
    public function landlordProperties()
    {
        abort_unless(auth()->user()->can('view landlord'), 403);

        $landlords = User::role('landlord')
            ->withCount(['properties', 'houses'])
            ->with(['properties' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get();

        return view('admin.user-management.landlords.properties', compact('landlords'));
    }

    /**
     * Display landlord payments
     */
    public function landlordPayments()
    {
        abort_unless(auth()->user()->can('view landlord'), 403);

        $landlordPayments = Payment::whereHas('lease.property', function($query) {
            $query->whereHas('landlord');
        })
        ->with(['lease.property.landlord', 'lease.tenant'])
        ->latest()
        ->paginate(20);

        return view('admin.user-management.landlords.payments', compact('landlordPayments'));
    }

    /**
     * Display landlord reports
     */
    public function landlordReports()
    {
        abort_unless(auth()->user()->can('view landlord'), 403);

        $landlordStats = [
            'total_landlords' => User::role('landlord')->count(),
            'active_landlords' => User::role('landlord')->whereHas('properties')->count(),
            'total_properties' => Property::whereHas('landlord')->count(),
            'total_revenue' => Payment::whereHas('lease.property.landlord')->sum('amount'),
        ];

        $topLandlords = User::role('landlord')
            ->withCount(['properties', 'houses'])
            ->withSum('properties', 'rent_amount')
            ->orderBy('properties_sum_rent_amount', 'desc')
            ->limit(10)
            ->get();

        return view('admin.user-management.landlords.reports', compact('landlordStats', 'topLandlords'));
    }

    /**
     * Display landlord activity
     */
    public function landlordActivity()
    {
        abort_unless(auth()->user()->can('view landlord'), 403);

        $landlordActivity = User::role('landlord')
            ->with(['loginActivities' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get();

        return view('admin.user-management.landlords.activity', compact('landlordActivity'));
    }

    /**
     * Display active tenants
     */
    public function activeTenants()
    {
        abort_unless(auth()->user()->can('view tenant'), 403);

        $activeTenants = User::role('tenant')
            ->whereHas('leases', function($query) {
                $query->where('status', 'active');
            })
            ->with(['leases' => function($query) {
                $query->where('status', 'active')->with('property');
            }])
            ->latest()
            ->paginate(20);

        return view('admin.user-management.tenants.active', compact('activeTenants'));
    }

    /**
     * Display tenant leases
     */
    public function tenantLeases()
    {
        abort_unless(auth()->user()->can('view tenant'), 403);

        $tenantLeases = Lease::with(['tenant', 'property', 'house'])
            ->latest()
            ->paginate(20);

        return view('admin.user-management.tenants.leases', compact('tenantLeases'));
    }

    /**
     * Display tenant payments
     */
    public function tenantPayments()
    {
        abort_unless(auth()->user()->can('view tenant'), 403);

        $tenantPayments = Payment::whereHas('lease.tenant')
            ->with(['lease.tenant', 'lease.property'])
            ->latest()
            ->paginate(20);

        return view('admin.user-management.tenants.payments', compact('tenantPayments'));
    }

    /**
     * Display tenant activity
     */
    public function tenantActivity()
    {
        abort_unless(auth()->user()->can('view tenant'), 403);

        $tenantActivity = User::role('tenant')
            ->with(['loginActivities' => function($query) {
                $query->latest()->limit(5);
            }])
            ->get();

        return view('admin.user-management.tenants.activity', compact('tenantActivity'));
    }

    /**
     * Display user analytics
     */
    public function analytics()
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        // User growth over time
        $userGrowth = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $userGrowth[] = [
                'month' => $date->format('M Y'),
                'users' => User::whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'landlords' => User::role('landlord')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
                'tenants' => User::role('tenant')
                    ->whereMonth('created_at', $date->month)
                    ->whereYear('created_at', $date->year)
                    ->count(),
            ];
        }

        // Role distribution
        $roleDistribution = Role::withCount('users')->get();

        // User activity metrics
        $activityMetrics = [
            'most_active_users' => User::withCount('loginActivities')
                ->orderBy('login_activities_count', 'desc')
                ->limit(10)
                ->get(),
            'recent_logins' => User::whereHas('loginActivities', function($query) {
                $query->where('created_at', '>=', now()->subDays(7));
            })->count(),
        ];

        return view('admin.user-management.analytics', compact('userGrowth', 'roleDistribution', 'activityMetrics'));
    }

    /**
     * Display user settings
     */
    public function settings()
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        $settings = [
            'user_registration_enabled' => setting('user_registration_enabled', true),
            'email_verification_required' => setting('email_verification_required', true),
            'default_user_role' => setting('default_user_role', 'tenant'),
            'user_invitation_expiry_days' => setting('user_invitation_expiry_days', 7),
            'max_login_attempts' => setting('max_login_attempts', 5),
            'session_timeout_minutes' => setting('session_timeout_minutes', 120),
        ];

        return view('admin.user-management.settings', compact('settings'));
    }

    /**
     * Update user settings
     */
    public function updateSettings(Request $request)
    {
        abort_unless(auth()->user()->can('manage users'), 403);

        $request->validate([
            'user_registration_enabled' => 'boolean',
            'email_verification_required' => 'boolean',
            'default_user_role' => 'required|string|exists:roles,name',
            'user_invitation_expiry_days' => 'required|integer|min:1|max:365',
            'max_login_attempts' => 'required|integer|min:1|max:10',
            'session_timeout_minutes' => 'required|integer|min:15|max:480',
        ]);

        // Update settings
        setting([
            'user_registration_enabled' => $request->user_registration_enabled,
            'email_verification_required' => $request->email_verification_required,
            'default_user_role' => $request->default_user_role,
            'user_invitation_expiry_days' => $request->user_invitation_expiry_days,
            'max_login_attempts' => $request->max_login_attempts,
            'session_timeout_minutes' => $request->session_timeout_minutes,
        ])->save();

        return redirect()->route('admin.user-management.settings')
            ->with('success', 'User settings updated successfully.');
    }
}
