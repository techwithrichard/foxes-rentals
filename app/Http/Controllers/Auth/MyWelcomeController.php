<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\WelcomeNotification\WelcomeController as BaseWelcomeController;

class MyWelcomeController extends BaseWelcomeController
{

    public function redirectPath()
    {
        return '/dashboard';
    }

    public function savePassword(Request $request, User $user)
    {
        $request->validate($this->rules());

        $user->password = Hash::make($request->password);
        $user->welcome_valid_until = null;
        $user->save();
        if (!Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
            return null;
        }

//        auth()->login($user);

        return $this->sendPasswordSavedResponse();
    }


}
