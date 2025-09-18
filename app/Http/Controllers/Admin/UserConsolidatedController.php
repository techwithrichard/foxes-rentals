<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Services\UserService;
use App\Services\RoleManagementService;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class UserConsolidatedController extends Controller
{
    protected $userService;
    protected $roleService;
    protected $userRepository;

    public function __construct(
        UserService $userService,
        RoleManagementService $roleService,
        UserRepositoryInterface $userRepository
    ) {
        $this->userService = $userService;
        $this->roleService = $roleService;
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request): View
    {
        $this->authorize('view user');

        $filters = $request->only([
            'name', 'email', 'phone', 'role', 'is_active', 'email_verified',
            'created_from', 'created_to', 'last_login_days'
        ]);

        $users = $this->userRepository->searchUsers($filters);
        $roles = Role::all();

        return view('admin.users-consolidated.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create(): View
    {
        $this->authorize('create user');

        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users-consolidated.create', compact('roles', 'permissions'));
    }

    /**
     * Store a newly created user
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create user');

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,name',
            'is_active' => 'boolean',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        try {
            $userData = $validated;
            $userData['name'] = $userData['first_name'] . ' ' . $userData['last_name'];
            $userData['is_active'] = $validated['is_active'] ?? true;

            $user = $this->userService->createUser($userData, $validated['role']);

            // Assign additional permissions if provided
            if (isset($validated['permissions'])) {
                foreach ($validated['permissions'] as $permission) {
                    $this->userService->assignPermission($user, $permission);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User created successfully.',
                'data' => $user->load(['roles', 'permissions'])
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show(User $user): View
    {
        $this->authorize('view user');

        $user->load(['roles', 'permissions']);

        return view('admin.users-consolidated.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user): View
    {
        $this->authorize('edit user');

        $user->load(['roles', 'permissions']);
        $roles = Role::all();
        $permissions = Permission::all();

        return view('admin.users-consolidated.edit', compact('user', 'roles', 'permissions'));
    }

    /**
     * Update the specified user
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $this->authorize('edit user');

        $validated = $request->validate([
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|string|min:8|confirmed',
            'is_active' => 'boolean',
            'role' => 'sometimes|string|exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'string|exists:permissions,name'
        ]);

        try {
            if (isset($validated['first_name']) || isset($validated['last_name'])) {
                $validated['name'] = ($validated['first_name'] ?? $user->first_name) . ' ' . ($validated['last_name'] ?? $user->last_name);
            }

            $user = $this->userService->updateUser($user, $validated);

            // Update role if provided
            if (isset($validated['role'])) {
                $this->userService->removeRole($user, $user->getRoleNames()->first());
                $this->userService->assignRole($user, $validated['role']);
            }

            // Update permissions if provided
            if (isset($validated['permissions'])) {
                // Remove all current permissions
                $user->permissions()->detach();
                
                // Assign new permissions
                foreach ($validated['permissions'] as $permission) {
                    $this->userService->assignPermission($user, $permission);
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully.',
                'data' => $user->load(['roles', 'permissions'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy(User $user): JsonResponse
    {
        $this->authorize('delete user');

        try {
            $this->userService->deleteUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully.'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Activate user
     */
    public function activate(User $user): JsonResponse
    {
        $this->authorize('edit user');

        try {
            $user = $this->userService->activateUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User activated successfully.',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to activate user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Deactivate user
     */
    public function deactivate(User $user): JsonResponse
    {
        $this->authorize('edit user');

        try {
            $user = $this->userService->deactivateUser($user);

            return response()->json([
                'success' => true,
                'message' => 'User deactivated successfully.',
                'data' => $user
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('edit user');

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        try {
            $user = $this->userService->assignRole($user, $validated['role']);

            return response()->json([
                'success' => true,
                'message' => 'Role assigned successfully.',
                'data' => $user->load(['roles'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove role from user
     */
    public function removeRole(Request $request, User $user): JsonResponse
    {
        $this->authorize('edit user');

        $validated = $request->validate([
            'role' => 'required|string|exists:roles,name'
        ]);

        try {
            $user = $this->userService->removeRole($user, $validated['role']);

            return response()->json([
                'success' => true,
                'message' => 'Role removed successfully.',
                'data' => $user->load(['roles'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign permission to user
     */
    public function assignPermission(Request $request, User $user): JsonResponse
    {
        $this->authorize('edit user');

        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        try {
            $user = $this->userService->assignPermission($user, $validated['permission']);

            return response()->json([
                'success' => true,
                'message' => 'Permission assigned successfully.',
                'data' => $user->load(['permissions'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove permission from user
     */
    public function removePermission(Request $request, User $user): JsonResponse
    {
        $this->authorize('edit user');

        $validated = $request->validate([
            'permission' => 'required|string|exists:permissions,name'
        ]);

        try {
            $user = $this->userService->removePermission($user, $validated['permission']);

            return response()->json([
                'success' => true,
                'message' => 'Permission removed successfully.',
                'data' => $user->load(['permissions'])
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get user statistics
     */
    public function statistics(): JsonResponse
    {
        $this->authorize('view user');

        try {
            $statistics = $this->userService->getUserStatistics();

            return response()->json([
                'success' => true,
                'data' => $statistics
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get statistics: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get users by role
     */
    public function getByRole(string $role): JsonResponse
    {
        $this->authorize('view user');

        try {
            $users = $this->userService->getUsersByRole($role);

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get active users
     */
    public function getActive(): JsonResponse
    {
        $this->authorize('view user');

        try {
            $users = $this->userService->getActiveUsers();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get active users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get inactive users
     */
    public function getInactive(): JsonResponse
    {
        $this->authorize('view user');

        try {
            $users = $this->userService->getInactiveUsers();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get inactive users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get verified users
     */
    public function getVerified(): JsonResponse
    {
        $this->authorize('view user');

        try {
            $users = $this->userService->getVerifiedUsers();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get verified users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get unverified users
     */
    public function getUnverified(): JsonResponse
    {
        $this->authorize('view user');

        try {
            $users = $this->userService->getUnverifiedUsers();

            return response()->json([
                'success' => true,
                'data' => $users
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to get unverified users: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk assign role
     */
    public function bulkAssignRole(Request $request): JsonResponse
    {
        $this->authorize('edit user');

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'string|exists:users,id',
            'role' => 'required|string|exists:roles,name'
        ]);

        try {
            $assignedCount = $this->userService->bulkAssignRole($validated['user_ids'], $validated['role']);

            return response()->json([
                'success' => true,
                'message' => "Role assigned to {$assignedCount} users successfully.",
                'assigned_count' => $assignedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to assign role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk deactivate users
     */
    public function bulkDeactivate(Request $request): JsonResponse
    {
        $this->authorize('edit user');

        $validated = $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'string|exists:users,id'
        ]);

        try {
            $deactivatedCount = $this->userService->bulkDeactivateUsers($validated['user_ids']);

            return response()->json([
                'success' => true,
                'message' => "{$deactivatedCount} users deactivated successfully.",
                'deactivated_count' => $deactivatedCount
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to deactivate users: ' . $e->getMessage()
            ], 500);
        }
    }
}
