<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdvancedPermissionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:manage_permissions');
    }

    /**
     * Display a listing of permissions with advanced management
     */
    public function index(Request $request)
    {
        $query = Permission::withCount(['roles', 'users']);

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where('name', 'like', "%{$search}%");
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('name', 'like', $request->get('category') . '_%');
        }

        // Filter by roles count
        if ($request->filled('min_roles')) {
            $query->having('roles_count', '>=', $request->get('min_roles'));
        }

        $permissions = $query->latest()->paginate(50);
        
        // Get permission categories
        $categories = Permission::all()->map(function ($permission) {
            $parts = explode('_', $permission->name);
            return $parts[0] ?? 'other';
        })->unique()->sort()->values();
        
        return view('admin.settings.permissions.index', compact('permissions', 'categories'));
    }

    /**
     * Show the form for creating a new permission
     */
    public function create()
    {
        $categories = Permission::all()->map(function ($permission) {
            $parts = explode('_', $permission->name);
            return $parts[0] ?? 'other';
        })->unique()->sort()->values();
        
        return view('admin.settings.permissions.create', compact('categories'));
    }

    /**
     * Store a newly created permission
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'nullable|string|max:500',
            'category' => 'required|string|max:100',
            'guard_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $permission = Permission::create([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => $request->guard_name ?? 'web',
        ]);

        return redirect()->route('admin.settings.permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Display the specified permission
     */
    public function show(Permission $permission)
    {
        $permission->load(['roles', 'users']);
        
        return view('admin.settings.permissions.show', compact('permission'));
    }

    /**
     * Show the form for editing the specified permission
     */
    public function edit(Permission $permission)
    {
        $categories = Permission::all()->map(function ($perm) {
            $parts = explode('_', $perm->name);
            return $parts[0] ?? 'other';
        })->unique()->sort()->values();
        
        return view('admin.settings.permissions.edit', compact('permission', 'categories'));
    }

    /**
     * Update the specified permission
     */
    public function update(Request $request, Permission $permission)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'nullable|string|max:500',
            'guard_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $permission->update([
            'name' => $request->name,
            'description' => $request->description,
            'guard_name' => $request->guard_name ?? 'web',
        ]);

        return redirect()->route('admin.settings.permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission
     */
    public function destroy(Permission $permission)
    {
        // Check if permission is assigned to roles
        if ($permission->roles()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete permission assigned to roles.');
        }

        // Check if permission is assigned to users
        if ($permission->users()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete permission assigned to users.');
        }

        $permission->delete();

        return redirect()->route('admin.settings.permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }

    /**
     * Bulk create permissions for a module
     */
    public function bulkCreate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module' => 'required|string|max:100',
            'actions' => 'required|array|min:1',
            'actions.*' => 'string|max:100',
            'description_prefix' => 'nullable|string|max:200',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $created = 0;
        $skipped = 0;

        foreach ($request->actions as $action) {
            $permissionName = $request->module . '_' . $action;
            
            if (!Permission::where('name', $permissionName)->exists()) {
                Permission::create([
                    'name' => $permissionName,
                    'description' => $request->description_prefix . ' ' . ucfirst($action) . ' ' . ucfirst($request->module),
                    'guard_name' => 'web',
                ]);
                $created++;
            } else {
                $skipped++;
            }
        }

        $message = "Created {$created} permissions. {$skipped} already existed.";
        return redirect()->route('admin.settings.permissions.index')
            ->with('success', $message);
    }

    /**
     * Bulk actions on permissions
     */
    public function bulkAction(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:delete,assign_to_role,remove_from_role',
            'permission_ids' => 'required|array|min:1',
            'permission_ids.*' => 'exists:permissions,id',
            'role_id' => 'required_if:action,assign_to_role,remove_from_role|exists:roles,id',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid bulk action.');
        }

        $permissions = Permission::whereIn('id', $request->permission_ids)->get();
        $role = null;

        if ($request->filled('role_id')) {
            $role = Role::findOrFail($request->role_id);
        }

        switch ($request->action) {
            case 'delete':
                foreach ($permissions as $permission) {
                    if ($permission->roles()->count() === 0 && $permission->users()->count() === 0) {
                        $permission->delete();
                    }
                }
                $message = 'Selected permissions deleted successfully.';
                break;

            case 'assign_to_role':
                if ($role) {
                    $role->givePermissionTo($permissions);
                    $message = "Permissions assigned to {$role->name} successfully.";
                }
                break;

            case 'remove_from_role':
                if ($role) {
                    $role->revokePermissionTo($permissions);
                    $message = "Permissions removed from {$role->name} successfully.";
                }
                break;
        }

        return redirect()->back()->with('success', $message ?? 'Bulk action completed.');
    }

    /**
     * Export permissions to CSV
     */
    public function export(Request $request)
    {
        $query = Permission::withCount(['roles', 'users']);

        if ($request->filled('category')) {
            $query->where('name', 'like', $request->get('category') . '_%');
        }

        $permissions = $query->get();

        $filename = 'permissions_export_' . now()->format('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($permissions) {
            $file = fopen('php://output', 'w');
            
            // CSV headers
            fputcsv($file, [
                'ID', 'Name', 'Description', 'Guard Name', 
                'Roles Count', 'Users Count', 'Created At'
            ]);

            foreach ($permissions as $permission) {
                fputcsv($file, [
                    $permission->id,
                    $permission->name,
                    $permission->description,
                    $permission->guard_name,
                    $permission->roles_count,
                    $permission->users_count,
                    $permission->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get permission statistics
     */
    public function statistics()
    {
        $stats = [
            'total_permissions' => Permission::count(),
            'permissions_by_category' => Permission::all()->groupBy(function ($permission) {
                $parts = explode('_', $permission->name);
                return $parts[0] ?? 'other';
            })->map->count(),
            'most_used_permissions' => Permission::withCount('roles')->orderBy('roles_count', 'desc')->limit(10)->get(),
            'unused_permissions' => Permission::whereDoesntHave('roles')->whereDoesntHave('users')->count(),
        ];

        return view('admin.settings.permissions.statistics', compact('stats'));
    }
}
