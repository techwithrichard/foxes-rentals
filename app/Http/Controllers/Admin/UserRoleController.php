<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserRoleController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view user roles'), 403);
        
        $users = User::with('roles')->paginate(15);
        $roles = Role::all();
        
        return view('admin.user-roles.index', compact('users', 'roles'));
    }

    public function assign(Request $request)
    {
        abort_unless(auth()->user()->can('assign user roles'), 403);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        if (!$user->hasRole($role->name)) {
            $user->assignRole($role->name);
            
            return redirect()->back()->with('success', __('Role assigned successfully.'));
        }

        return redirect()->back()->with('error', __('User already has this role.'));
    }

    public function remove(Request $request)
    {
        abort_unless(auth()->user()->can('remove user roles'), 403);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        if ($user->hasRole($role->name)) {
            $user->removeRole($role->name);
            
            return redirect()->back()->with('success', __('Role removed successfully.'));
        }

        return redirect()->back()->with('error', __('User does not have this role.'));
    }
}

