<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Validation\Rules\Password;

class AdvancedUserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_users');
    }

    /**
     * Display a listing of users with advanced filtering and search
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'permissions'])
            ->withCount(['roles', 'permissions']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('identity_no', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->get('role'));
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->get('status') === 'active') {
                $query->whereNull('deleted_at');
            } elseif ($request->get('status') === 'inactive') {
                $query->onlyTrashed();
            }
        }

        // Filter by email verification
        if ($request->filled('email_verified')) {
            $query->where('email_verified_at', $request->get('email_verified') === 'verified' ? '!=' : '=', null);
        }

        $users = $query->latest()->paginate(20);
        $roles = Role::all();
        
        return view('admin.users.advanced.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user
     */
    public function create()
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('_', $permission->name)[0];
        });
        
        return view('admin.users.advanced.create', compact('roles', 'permissions'));
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
            'password' => ['required', 'confirmed', Password::defaults()],
            'identity_no' => 'nullable|string|max:50',
            'occupation_status' => 'nullable|string|max:100',
            'occupation_place' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'send_welcome_email' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'identity_no' => $request->identity_no,
            'occupation_status' => $request->occupation_status,
            'occupation_place' => $request->occupation_place,
            'address' => $request->address,
            'email_verified_at' => $request->email_verified ? now() : null,
        ]);

        // Assign roles
        $user->assignRole($request->roles);

        // Assign direct permissions
        if ($request->filled('permissions')) {
            $user->givePermissionTo($request->permissions);
        }

        // Send welcome email if requested
        if ($request->send_welcome_email) {
            // TODO: Implement welcome email sending
        }

        return redirect()->route('admin.users.advanced.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified user
     */
    public function show(User $user)
    {
        $user->load(['roles', 'permissions']);
        
        return view('admin.users.advanced.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('_', $permission->name)[0];
        });
        
        $user->load(['roles', 'permissions']);
        
        return view('admin.users.advanced.edit', compact('user', 'roles', 'permissions'));
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
            'password' => 'nullable|confirmed|min:8',
            'identity_no' => 'nullable|string|max:50',
            'occupation_status' => 'nullable|string|max:100',
            'occupation_place' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:500',
            'roles' => 'required|array|min:1',
            'roles.*' => 'exists:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $updateData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'identity_no' => $request->identity_no,
            'occupation_status' => $request->occupation_status,
            'occupation_place' => $request->occupation_place,
            'address' => $request->address,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        // Sync roles
        $user->syncRoles($request->roles);

        // Sync direct permissions
        $user->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.users.advanced.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage
     */
    public function destroy(User $user)
    {
        // Prevent deleting yourself
        if (auth()->user()->id === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $user->delete();

        return redirect()->route('admin.users.advanced.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Restore a soft-deleted user
     */
    public function restore($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        $user->restore();

        return redirect()->back()
            ->with('success', 'User restored successfully.');
    }

    /**
     * Permanently delete a user
     */
    public function forceDelete($id)
    {
        $user = User::withTrashed()->findOrFail($id);
        
        // Prevent deleting yourself
        if (auth()->user()->id === $user->id) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $user->forceDelete();

        return redirect()->route('admin.users.advanced.index')
            ->with('success', 'User permanently deleted.');
    }

    /**
     * Bulk actions on users
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,restore,assign_role,remove_role',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid bulk action.');
        }

        $users = User::whereIn('id', $request->user_ids)->get();

        switch ($request->action) {
            case 'delete':
                foreach ($users as $user) {
                    if ($user->id !== auth()->user()->id) {
                        $user->delete();
                    }
                }
                $message = 'Selected users deleted successfully.';
                break;

            case 'restore':
                foreach ($users as $user) {
                    $user->restore();
                }
                $message = 'Selected users restored successfully.';
                break;

            case 'assign_role':
                if ($request->filled('role')) {
                    foreach ($users as $user) {
                        $user->assignRole($request->role);
                    }
                    $message = 'Role assigned to selected users successfully.';
                }
                break;

            case 'remove_role':
                if ($request->filled('role')) {
                    foreach ($users as $user) {
                        $user->removeRole($request->role);
                    }
                    $message = 'Role removed from selected users successfully.';
                }
                break;
        }

        return redirect()->back()->with('success', $message ?? 'Bulk action completed.');
    }

    /**
     * Export users to CSV
     */
    public function export(Request $request)
    {
        $query = User::with(['roles', 'permissions']);

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->get('role'));
            });
        }

        $users = $query->get();

        $filename = 'users_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($users) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Phone', 'Identity No', 
                'Occupation Status', 'Occupation Place', 'Address',
                'Roles', 'Permissions Count', 'Email Verified', 'Created At'
            ]);

            foreach ($users as $user) {
                fputcsv($file, [
                    $user->id,
                    $user->name,
                    $user->email,
                    $user->phone,
                    $user->identity_no,
                    $user->occupation_status,
                    $user->occupation_place,
                    $user->address,
                    $user->roles->pluck('name')->join(', '),
                    $user->permissions->count(),
                    $user->email_verified_at ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
