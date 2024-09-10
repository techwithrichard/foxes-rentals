<?php

namespace App\Http\Livewire\Admin\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Spatie\Permission\Models\Role;

class CreateUserComponent extends Component
{

    public $name;
    public $email;
    public $phone;
    public $role;

    protected $rules = [
        'name' => 'required',
        'email' => 'required|email|unique:users',
        'phone' => 'required',
        'role' => 'required',
    ];

    public function render()
    {
        //get all roles apart from admin,tenant and landlord
        $roles = Role::query()
            ->whereNotIn('name', ['admin', 'tenant', 'landlord'])
            ->get();
        return view('livewire.admin.users.create-user-component', compact('roles'));
    }

    public function submit()
    {

        $this->validate();

        DB::beginTransaction();

        try {
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => '1',
                'phone' => $this->phone,
            ]);

            $user->assignRole($this->role);

            $user->givePermissionTo('view_admin_portal');


            DB::commit();
        } catch (\Exception $ex) {
            DB::rollBack();
            return back()->with('error', $ex->getMessage());

        }


        $this->reset('name', 'email', 'phone');

        $expiresAt = now()->addDays(setting('invitation_link_expiry_days', 365));
        $user->sendWelcomeNotification($expiresAt);

        return redirect()->route('admin.users-management.index')
            ->with('success', __('User created successfully and they will receive an email to set up their password.'));


    }
}
