<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UsersController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('manage users'), 403);
        $users = User::query()
            ->with('roles')
            ->whereHas('roles', function ($query) {
                $query->whereNotIn('name', ['landlord', 'tenant']);
            })
            ->latest('id')
            ->get();
        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete users'), 403);
        //prevent deleting yourself
        if (auth()->user()->id == $id) {
            return redirect()->back()->with('error', __('You cannot delete yourself'));
        }

        $user = User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', __('User deleted successfully'));
    }
}
