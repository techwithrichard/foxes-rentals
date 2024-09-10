<?php

namespace App\Http\Livewire\Widgets;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use JetBrains\PhpStorm\ArrayShape;
use Livewire\Component;

class UpdatePasswordComponent extends Component
{

    public $current_password;

    public $password;
    public $password_confirmation;

    public $previousRoute;


    protected function getRules(): array
    {
        return [
            'current_password' => 'required',
            'password' => 'required|confirmed',
        ];
    }

    public function mount()
    {
        $this->previousRoute = URL::current();
    }


    public function render()
    {
        return view('livewire.widgets.update-password-component');
    }

    public function submit()
    {
        $this->validate();

        $user = auth()->user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', __('Current password is incorrect'));
            return null;
        }

        $user->password = Hash::make($this->password);
        $user->password_changed_at = now();
        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation']);

        $message = __('Password updated successfully.Use your new password in the next login.');

        session()->flash('success', $message);
        return redirect()->to($this->previousRoute);


    }
}
