<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\UserPreference;
use App\Models\UserActivity;
use App\Services\AdvancedUserManagementService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AdvancedUserManagementController extends Controller
{
    protected $userManagementService;

    public function __construct(AdvancedUserManagementService $userManagementService)
    {
        $this->middleware('permission:manage_users');
        $this->userManagementService = $userManagementService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $query = User::with(['profile', 'roles', 'permissions']);

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        // Filter by search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20);
        $roles = Role::all();
        $statistics = $this->userManagementService->getUserStatistics();

        return view('admin.settings.users.index', compact('users', 'roles', 'statistics'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy('guard_name');
        
        return view('admin.settings.users.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean',
            'profile' => 'nullable|array',
            'preferences' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                'is_active' => $request->boolean('is_active', true),
                'email_verified_at' => now()
            ]);

            // Assign roles
            if ($request->filled('roles')) {
                $roles = Role::whereIn('id', $request->roles)->get();
                $user->assignRole($roles);
            }

            // Assign permissions
            if ($request->filled('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions)->get();
                $user->givePermissionTo($permissions);
            }

            // Create user profile
            if ($request->filled('profile')) {
                $user->profile()->create(array_merge($request->profile, [
                    'created_by' => auth()->id()
                ]));
            }

            // Create user preferences
            if ($request->filled('preferences')) {
                $user->preferences()->create([
                    'preferences' => $request->preferences,
                    'created_by' => auth()->id()
                ]);
            }

            // Log activity
            $this->userManagementService->logUserActivity($user->id, 'user_created', [
                'created_by' => auth()->id(),
                'user_data' => $user->only(['name', 'email', 'phone'])
            ]);

            Log::info("User created: {$user->email} by user " . auth()->id());

            return redirect()->route('admin.settings.users.index')
                ->with('success', 'User created successfully.');

        } catch (\Exception $e) {
            Log::error("Error creating user: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to create user. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['profile', 'roles', 'permissions', 'activities' => function ($query) {
            $query->latest()->limit(20);
        }]);

        $statistics = $this->userManagementService->getUserStatistics($user->id);
        $recentActivity = $user->activities()->latest()->limit(10)->get();

        return view('admin.settings.users.show', compact('user', 'statistics', 'recentActivity'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy('guard_name');
        $user->load(['profile', 'roles', 'permissions', 'preferences']);
        
        return view('admin.settings.users.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'roles' => 'nullable|array',
            'roles.*' => 'exists:roles,id',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'is_active' => 'boolean',
            'profile' => 'nullable|array',
            'preferences' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $oldData = $user->only(['name', 'email', 'phone', 'is_active']);

            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'is_active' => $request->boolean('is_active', true)
            ]);

            // Update password if provided
            if ($request->filled('password')) {
                $user->update(['password' => Hash::make($request->password)]);
            }

            // Update roles
            if ($request->has('roles')) {
                $roles = Role::whereIn('id', $request->roles ?? [])->get();
                $user->syncRoles($roles);
            }

            // Update permissions
            if ($request->has('permissions')) {
                $permissions = Permission::whereIn('id', $request->permissions ?? [])->get();
                $user->syncPermissions($permissions);
            }

            // Update user profile
            if ($request->filled('profile')) {
                if ($user->profile) {
                    $user->profile->update($request->profile);
                } else {
                    $user->profile()->create(array_merge($request->profile, [
                        'created_by' => auth()->id()
                    ]));
                }
            }

            // Update user preferences
            if ($request->filled('preferences')) {
                if ($user->preferences) {
                    $user->preferences->update(['preferences' => $request->preferences]);
                } else {
                    $user->preferences()->create([
                        'preferences' => $request->preferences,
                        'created_by' => auth()->id()
                    ]);
                }
            }

            // Log activity
            $this->userManagementService->logUserActivity($user->id, 'user_updated', [
                'updated_by' => auth()->id(),
                'changes' => array_diff_assoc($user->only(['name', 'email', 'phone', 'is_active']), $oldData)
            ]);

            Log::info("User updated: {$user->email} by user " . auth()->id());

            return redirect()->route('admin.settings.users.index')
                ->with('success', 'User updated successfully.');

        } catch (\Exception $e) {
            Log::error("Error updating user: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to update user. Please try again.'])
                ->withInput();
        }
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deletion of the current user
            if ($user->id === auth()->id()) {
                return redirect()->back()
                    ->withErrors(['error' => 'You cannot delete your own account.']);
            }

            $userEmail = $user->email;
            
            // Log activity before deletion
            $this->userManagementService->logUserActivity($user->id, 'user_deleted', [
                'deleted_by' => auth()->id(),
                'user_data' => $user->only(['name', 'email', 'phone'])
            ]);

            $user->delete();

            Log::info("User deleted: {$userEmail} by user " . auth()->id());

            return redirect()->route('admin.settings.users.index')
                ->with('success', 'User deleted successfully.');

        } catch (\Exception $e) {
            Log::error("Error deleting user: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to delete user. Please try again.']);
        }
    }

    /**
     * Restore a soft deleted user
     */
    public function restore($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            $user->restore();

            // Log activity
            $this->userManagementService->logUserActivity($user->id, 'user_restored', [
                'restored_by' => auth()->id()
            ]);

            Log::info("User restored: {$user->email} by user " . auth()->id());

            return redirect()->route('admin.settings.users.index')
                ->with('success', 'User restored successfully.');

        } catch (\Exception $e) {
            Log::error("Error restoring user: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to restore user. Please try again.']);
        }
    }

    /**
     * Force delete a user
     */
    public function forceDelete($id)
    {
        try {
            $user = User::withTrashed()->findOrFail($id);
            
            // Prevent force deletion of the current user
            if ($user->id === auth()->id()) {
                return redirect()->back()
                    ->withErrors(['error' => 'You cannot delete your own account.']);
            }

            $userEmail = $user->email;
            $user->forceDelete();

            Log::info("User force deleted: {$userEmail} by user " . auth()->id());

            return redirect()->route('admin.settings.users.index')
                ->with('success', 'User permanently deleted.');

        } catch (\Exception $e) {
            Log::error("Error force deleting user: " . $e->getMessage());
            
            return redirect()->back()
                ->withErrors(['error' => 'Failed to permanently delete user. Please try again.']);
        }
    }

    /**
     * Toggle user active status
     */
    public function toggleStatus(User $user): JsonResponse
    {
        try {
            // Prevent deactivating the current user
            if ($user->id === auth()->id() && $user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot deactivate your own account.'
                ], 400);
            }

            $user->update(['is_active' => !$user->is_active]);
            
            $status = $user->is_active ? 'activated' : 'deactivated';
            
            // Log activity
            $this->userManagementService->logUserActivity($user->id, 'user_status_changed', [
                'changed_by' => auth()->id(),
                'new_status' => $user->is_active
            ]);

            Log::info("User {$status}: {$user->email} by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => "User {$status} successfully",
                'is_active' => $user->is_active
            ]);

        } catch (\Exception $e) {
            Log::error("Error toggling user status: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status'
            ], 400);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, User $user): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|confirmed'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user->update(['password' => Hash::make($request->password)]);
            
            // Log activity
            $this->userManagementService->logUserActivity($user->id, 'password_reset', [
                'reset_by' => auth()->id()
            ]);

            Log::info("Password reset for user: {$user->email} by user " . auth()->id());

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully'
            ]);

        } catch (\Exception $e) {
            Log::error("Error resetting password: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password'
            ], 400);
        }
    }

    /**
     * Bulk actions on users
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:activate,deactivate,delete,assign_role,remove_role',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
            'role_id' => 'nullable|exists:roles,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $users = User::whereIn('id', $request->user_ids)->get();
            $results = [];

            switch ($request->action) {
                case 'activate':
                    foreach ($users as $user) {
                        $user->update(['is_active' => true]);
                        $results[] = $user->id;
                    }
                    $message = 'Selected users activated successfully.';
                    break;

                case 'deactivate':
                    foreach ($users as $user) {
                        if ($user->id !== auth()->id()) {
                            $user->update(['is_active' => false]);
                            $results[] = $user->id;
                        }
                    }
                    $message = 'Selected users deactivated successfully.';
                    break;

                case 'delete':
                    foreach ($users as $user) {
                        if ($user->id !== auth()->id()) {
                            $user->delete();
                            $results[] = $user->id;
                        }
                    }
                    $message = 'Selected users deleted successfully.';
                    break;

                case 'assign_role':
                    if (!$request->role_id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Role ID is required for assign_role action'
                        ], 422);
                    }
                    
                    $role = Role::findOrFail($request->role_id);
                    foreach ($users as $user) {
                        $user->assignRole($role);
                        $results[] = $user->id;
                    }
                    $message = "Role '{$role->name}' assigned to selected users successfully.";
                    break;

                case 'remove_role':
                    if (!$request->role_id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Role ID is required for remove_role action'
                        ], 422);
                    }
                    
                    $role = Role::findOrFail($request->role_id);
                    foreach ($users as $user) {
                        $user->removeRole($role);
                        $results[] = $user->id;
                    }
                    $message = "Role '{$role->name}' removed from selected users successfully.";
                    break;
            }

            // Log bulk activity
            $this->userManagementService->logBulkUserActivity($results, $request->action, [
                'performed_by' => auth()->id(),
                'role_id' => $request->role_id
            ]);

            Log::info("Bulk user action '{$request->action}' performed by user " . auth()->id() . " on " . count($results) . " users");

            return response()->json([
                'success' => true,
                'message' => $message,
                'affected_count' => count($results)
            ]);

        } catch (\Exception $e) {
            Log::error("Error in bulk user action: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action'
            ], 400);
        }
    }

    /**
     * Import users from CSV
     */
    public function importUsers(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:2048'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $result = $this->userManagementService->importUsersFromCsv($request->file('csv_file'), auth()->id());
            
            return response()->json([
                'success' => true,
                'message' => 'Users imported successfully',
                'result' => $result
            ]);

        } catch (\Exception $e) {
            Log::error("Error importing users: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Export users to CSV
     */
    public function exportUsers(Request $request): JsonResponse
    {
        try {
            $csvData = $this->userManagementService->exportUsersToCsv($request->all());
            
            return response()->json([
                'success' => true,
                'csv_data' => $csvData
            ]);

        } catch (\Exception $e) {
            Log::error("Error exporting users: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to export users'
            ], 400);
        }
    }

    /**
     * Get user statistics
     */
    public function getStatistics(): JsonResponse
    {
        try {
            $statistics = $this->userManagementService->getUserStatistics();
            
            return response()->json([
                'success' => true,
                'statistics' => $statistics
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting user statistics: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics'
            ], 400);
        }
    }

    /**
     * Get user activity log
     */
    public function getActivityLog(Request $request, User $user): JsonResponse
    {
        try {
            $activities = $this->userManagementService->getUserActivityLog(
                $user->id,
                $request->get('days', 30),
                $request->get('limit', 50)
            );
            
            return response()->json([
                'success' => true,
                'activities' => $activities
            ]);

        } catch (\Exception $e) {
            Log::error("Error getting user activity log: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get activity log'
            ], 400);
        }
    }
}