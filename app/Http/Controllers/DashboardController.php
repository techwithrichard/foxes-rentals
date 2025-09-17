<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Redirect user to appropriate dashboard based on their role
     */
    public function index()
    {
        $user = auth()->user();
        
        if ($user->hasRole('super_admin') || $user->hasRole('admin')) {
            return redirect()->route('admin.home');
        }
        
        if ($user->hasRole('landlord')) {
            return redirect()->route('landlord.dashboard');
        }
        
        if ($user->hasRole('tenant')) {
            return redirect()->route('tenant.dashboard');
        }
        
        if ($user->hasRole('maintainer')) {
            return redirect()->route('maintainer.dashboard');
        }
        
        if ($user->hasRole('accountant')) {
            return redirect()->route('accountant.dashboard');
        }
        
        if ($user->hasRole('property_manager')) {
            return redirect()->route('property-manager.dashboard');
        }
        
        if ($user->hasRole('agent')) {
            return redirect()->route('agent.dashboard');
        }
        
        // Default fallback - show a basic dashboard or redirect to profile
        return redirect()->route('profile');
    }
}
