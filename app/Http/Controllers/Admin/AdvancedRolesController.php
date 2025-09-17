<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class AdvancedRolesController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_roles');
    }

    /**
     * Display a listing of roles with advanced management
     */
    public function index(Request $request)
    {
        $query = Role::withCount(['users', 'permissions']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by permission count
        if ($request->filled('min_permissions')) {
            $query->having('permissions_count', '>=', $request->get('min_permissions'));
        }

        $roles = $query->latest()->paginate(20);
        
        return view('admin.settings.roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role
     */
    public function create()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);
            return $parts[0] ?? 'other';
        });
        
        return view('admin.settings.roles.create', compact('permissions'));
    }

    /**
     * Store a newly created role
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role = Role::create([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'icon' => $request->icon,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Assign permissions
        if ($request->filled('permissions')) {
            $role->givePermissionTo($request->permissions);
        }

        return redirect()->route('admin.settings.roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Display the specified role
     */
    public function show(Role $role)
    {
        $role->load(['permissions', 'users']);
        
        return view('admin.settings.roles.show', compact('role'));
    }

    /**
     * Show the form for editing the specified role
     */
    public function edit(Role $role)
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);
            return $parts[0] ?? 'other';
        });
        
        $role->load('permissions');
        
        return view('admin.settings.roles.edit', compact('role', 'permissions'));
    }

    /**
     * Update the specified role
     */
    public function update(Request $request, Role $role)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
            'color' => 'nullable|string|max:7|regex:/^#[0-9A-Fa-f]{6}$/',
            'icon' => 'nullable|string|max:50',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $role->update([
            'name' => $request->name,
            'description' => $request->description,
            'color' => $request->color,
            'icon' => $request->icon,
            'is_active' => $request->boolean('is_active', true),
        ]);

        // Sync permissions
        $role->syncPermissions($request->permissions ?? []);

        return redirect()->route('admin.settings.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified role
     */
    public function destroy(Role $role)
    {
        // Prevent deleting system roles
        $systemRoles = ['super_admin', 'admin', 'landlord', 'tenant'];
        if (in_array($role->name, $systemRoles)) {
            return redirect()->back()->with('error', 'Cannot delete system roles.');
        }

        // Check if role has users
        if ($role->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete role with assigned users.');
        }

        $role->delete();

        return redirect()->route('admin.settings.roles.index')
            ->with('success', 'Role deleted successfully.');
    }

    /**
     * Toggle role active status
     */
    public function toggleStatus(Role $role)
    {
        $role->update(['is_active' => !$role->is_active]);

        $status = $role->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Role {$status} successfully.");
    }

    /**
     * Duplicate a role
     */
    public function duplicate(Role $role)
    {
        $newRole = Role::create([
            'name' => $role->name . '_copy',
            'description' => $role->description . ' (Copy)',
            'color' => $role->color,
            'icon' => $role->icon,
            'is_active' => false,
        ]);

        // Copy permissions
        $newRole->syncPermissions($role->permissions);

        return redirect()->route('admin.settings.roles.edit', $newRole)
            ->with('success', 'Role duplicated successfully. Please update the name.');
    }

    /**
     * Bulk actions on roles
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,activate,deactivate',
            'role_ids' => 'required|array|min:1',
            'role_ids.*' => 'exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid bulk action.');
        }

        $roles = Role::whereIn('id', $request->role_ids)->get();
        $systemRoles = ['super_admin', 'admin', 'landlord', 'tenant'];

        switch ($request->action) {
            case 'delete':
                foreach ($roles as $role) {
                    if (!in_array($role->name, $systemRoles) && $role->users()->count() === 0) {
                        $role->delete();
                    }
                }
                $message = 'Selected roles deleted successfully.';
                break;

            case 'activate':
                foreach ($roles as $role) {
                    $role->update(['is_active' => true]);
                }
                $message = 'Selected roles activated successfully.';
                break;

            case 'deactivate':
                foreach ($roles as $role) {
                    $role->update(['is_active' => false]);
                }
                $message = 'Selected roles deactivated successfully.';
                break;
        }

        return redirect()->back()->with('success', $message ?? 'Bulk action completed.');
    }

    /**
     * Export roles to CSV
     */
    public function export()
    {
        $roles = Role::withCount(['users', 'permissions'])->get();

        $filename = 'roles_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($roles) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Description', 'Color', 'Icon', 
                'Users Count', 'Permissions Count', 'Is Active', 'Created At'
            ]);

            foreach ($roles as $role) {
                fputcsv($file, [
                    $role->id,
                    $role->name,
                    $role->description,
                    $role->color,
                    $role->icon,
                    $role->users_count,
                    $role->permissions_count,
                    $role->is_active ? 'Yes' : 'No',
                    $role->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
