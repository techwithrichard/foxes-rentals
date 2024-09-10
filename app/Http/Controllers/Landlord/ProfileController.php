<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        return view('landlord.profile.profile', compact('user'));

    }

    public function login_activities()
    {

        //load latest 20 login activities of the user
        $login_activities = auth()->user()->loginActivities()->latest()->take(20)->get();
        return view('landlord.profile.login_activities', compact('login_activities'));


    }

    public function security_settings()
    {
        //load latest
        return view('landlord.profile.security_settings');

    }
}
