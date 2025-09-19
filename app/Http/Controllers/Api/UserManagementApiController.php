<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\ChangePasswordRequest;
use App\Services\UserManagementService;
use App\Services\RoleBasedAccessControlService;
use App\Services\PermissionManagementService;
use App\Services\UserActivityService;
use App\Services\PasswordSecurityService;
use App\Services\UserInvitationService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserManagementApiController extends Controller
{
    protected UserManagementService $userService;
    protected RoleBasedAccessControlService $rbacService;
    protected PermissionManagementService $permissionService;
    protected UserActivityService $activityService;
    protected PasswordSecurityService $passwordService;
    protected UserInvitationService $invitationService;

    public function __construct(
        UserManagementService $userService,
        RoleBasedAccessControlService $rbacService,
        PermissionManagementService $permissionService,
        UserActivityService $activityService,
        PasswordSecurityService $passwordService,
        UserInvitationService $invitationService
    ) {
        $this->middleware('auth:sanctum');
        $this->userService = $userService;
        $this->rbacService = $rbacService;
        $this->permissionService = $permissionService;
        $this->activityService = $activityService;
        $this->passwordService = $passwordService;
        $this->invitationService = $invitationService;
    }

    /**
     * Get all users with filtering and pagination
     */
    public function index(Request $request): JsonResponse
    {
        $this->authorize('view_users');

        $filters = $request->only(['role', 'status', 'search', 'date_from', 'date_to']);
        $perPage = $request->get('per_page', 15);

        $users = $this->userService->getAllUsers($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => $users->items(),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Get user by ID
     */
    public function show(string $id): JsonResponse
    {
        $this->authorize('view_users');

        $user = $this->userService->getUserById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    /**
     * Create new user
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create_users');

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|min:2',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'nullable|string|max:20',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
            'send_welcome' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $this->userService->createUser($request->all());

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user,
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update user
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $this->authorize('edit_users');

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255|min:2',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
            'role' => 'sometimes|string|exists:roles,name',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $this->userService->updateUser($id, $request->all());

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete user
     */
    public function destroy(string $id): JsonResponse
    {
        $this->authorize('delete_users');

        try {
            $this->userService->deleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Toggle user status
     */
    public function toggleStatus(string $id): JsonResponse
    {
        $this->authorize('toggle_user_status');

        try {
            $user = $this->userService->toggleUserStatus($id);

            return response()->json([
                'success' => true,
                'message' => 'User status updated successfully',
                'data' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user status',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Reset user password
     */
    public function resetPassword(Request $request, string $id): JsonResponse
    {
        $this->authorize('reset_user_passwords');

        $validator = Validator::make($request->all(), [
            'password' => 'nullable|string|min:8',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = $this->userService->resetUserPassword($id, $request->get('password'));

            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'data' => $user,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset password',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('view_users');

        $statistics = $this->userService->getUserStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get user activities
     */
    public function activities(Request $request, string $id): JsonResponse
    {
        $this->authorize('view_user_activity');

        $filters = $request->only(['action', 'date_from', 'date_to', 'search']);
        $perPage = $request->get('per_page', 15);

        $activities = $this->activityService->getAllActivities(array_merge($filters, ['user_id' => $id]), $perPage);

        return response()->json([
            'success' => true,
            'data' => $activities->items(),
            'meta' => [
                'current_page' => $activities->currentPage(),
                'last_page' => $activities->lastPage(),
                'per_page' => $activities->perPage(),
                'total' => $activities->total(),
            ],
        ]);
    }

    /**
     * Get user permissions
     */
    public function permissions(string $id): JsonResponse
    {
        $this->authorize('manage_user_permissions');

        $user = User::findOrFail($id);
        $permissions = $this->rbacService->getUserPermissions($user);

        return response()->json([
            'success' => true,
            'data' => $permissions,
        ]);
    }

    /**
     * Assign permission to user
     */
    public function assignPermission(Request $request, string $id): JsonResponse
    {
        $this->authorize('manage_user_permissions');

        $validator = Validator::make($request->all(), [
            'permission' => 'required|string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::findOrFail($id);
            $this->permissionService->assignPermissionToUser($user, $request->permission);

            return response()->json([
                'success' => true,
                'message' => 'Permission assigned successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove permission from user
     */
    public function removePermission(Request $request, string $id): JsonResponse
    {
        $this->authorize('manage_user_permissions');

        $validator = Validator::make($request->all(), [
            'permission' => 'required|string|exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::findOrFail($id);
            $this->permissionService->removePermissionFromUser($user, $request->permission);

            return response()->json([
                'success' => true,
                'message' => 'Permission removed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove permission',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, string $id): JsonResponse
    {
        $this->authorize('manage_user_roles');

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::findOrFail($id);
            $this->permissionService->assignRoleToUser($user, $request->role);

            return response()->json([
                'success' => true,
                'message' => 'Role assigned successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request, string $id): JsonResponse
    {
        $this->authorize('manage_user_roles');

        $validator = Validator::make($request->all(), [
            'role' => 'required|string|exists:roles,name',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $user = User::findOrFail($id);
            $this->permissionService->removeRoleFromUser($user, $request->role);

            return response()->json([
                'success' => true,
                'message' => 'Role removed successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove role',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Bulk actions on users
     */
    public function bulkAction(Request $request): JsonResponse
    {
        $this->authorize('manage_users');

        $validator = Validator::make($request->all(), [
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'action' => 'required|string|in:activate,deactivate,delete,assign_role,remove_role',
            'data' => 'nullable|array',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {
            $results = $this->userService->bulkAction(
                $request->user_ids,
                $request->action,
                $request->data ?? []
            );

            return response()->json([
                'success' => true,
                'message' => 'Bulk action completed',
                'data' => $results,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to perform bulk action',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Export user data
     */
    public function export(Request $request): JsonResponse
    {
        $this->authorize('view_users');

        $filters = $request->only(['role', 'status', 'date_from', 'date_to']);
        $users = $this->userService->getAllUsers($filters, 1000); // Get all users

        $exportData = [
            'users' => $users->items(),
            'exported_at' => now()->toISOString(),
            'exported_by' => Auth::user()->name,
        ];

        return response()->json([
            'success' => true,
            'data' => $exportData,
        ]);
    }

    /**
     * Get password security information
     */
    public function passwordSecurity(): JsonResponse
    {
        $this->authorize('view_users');

        $statistics = $this->passwordService->getPasswordSecurityStatistics();

        return response()->json([
            'success' => true,
            'data' => $statistics,
        ]);
    }

    /**
     * Get users with expired passwords
     */
    public function expiredPasswords(): JsonResponse
    {
        $this->authorize('view_users');

        $users = $this->passwordService->getUsersWithExpiredPasswords();

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }

    /**
     * Get users with passwords expiring soon
     */
    public function expiringPasswords(Request $request): JsonResponse
    {
        $this->authorize('view_users');

        $days = $request->get('days', 7);
        $users = $this->passwordService->getUsersWithPasswordsExpiringSoon($days);

        return response()->json([
            'success' => true,
            'data' => $users,
        ]);
    }
}

