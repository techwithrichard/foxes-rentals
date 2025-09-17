<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\Property;

class TenantPortalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:tenant']);
    }

    /**
     * Display the tenant dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get tenant's current lease
        $currentLease = Lease::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with(['property', 'payments'])
            ->first();
        
        // Get payment history
        $payments = Payment::whereHas('lease', function($query) use ($user) {
            $query->where('tenant_id', $user->id);
        })->latest()->limit(10)->get();
        
        // Calculate statistics
        $stats = [
            'current_rent' => $currentLease ? $currentLease->rent_amount : 0,
            'next_payment_due' => $currentLease ? $currentLease->next_payment_date : null,
            'total_paid' => $payments->where('status', 'completed')->sum('amount'),
            'pending_payments' => $payments->where('status', 'pending')->sum('amount'),
            'lease_end_date' => $currentLease ? $currentLease->end_date : null,
        ];

        return view('portals.tenant.dashboard', compact('currentLease', 'payments', 'stats'));
    }

    /**
     * Display tenant's lease information
     */
    public function lease()
    {
        $user = auth()->user();
        $lease = Lease::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with(['property', 'payments'])
            ->first();

        if (!$lease) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'No active lease found.');
        }

        return view('portals.tenant.lease', compact('lease'));
    }

    /**
     * Display tenant's payment history
     */
    public function payments()
    {
        $user = auth()->user();
        $payments = Payment::whereHas('lease', function($query) use ($user) {
            $query->where('tenant_id', $user->id);
        })->with(['lease.property'])
        ->latest()
        ->paginate(20);

        return view('portals.tenant.payments', compact('payments'));
    }

    /**
     * Display tenant's property information
     */
    public function property()
    {
        $user = auth()->user();
        $lease = Lease::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with(['property'])
            ->first();

        if (!$lease) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'No active lease found.');
        }

        return view('portals.tenant.property', compact('lease'));
    }

    /**
     * Display tenant's maintenance requests
     */
    public function maintenance()
    {
        $user = auth()->user();
        $lease = Lease::where('tenant_id', $user->id)
            ->where('status', 'active')
            ->with(['property'])
            ->first();

        if (!$lease) {
            return redirect()->route('tenant.dashboard')
                ->with('error', 'No active lease found.');
        }

        // Get maintenance requests for the property
        $maintenanceRequests = $lease->property->maintenanceRequests ?? collect();

        return view('portals.tenant.maintenance', compact('lease', 'maintenanceRequests'));
    }

    /**
     * Display tenant's profile
     */
    public function profile()
    {
        $user = auth()->user();
        return view('portals.tenant.profile', compact('user'));
    }

    /**
     * Update tenant's profile
     */
    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500',
        ]);

        $user->update($request->only(['name', 'phone', 'address']));

        return redirect()->route('tenant.profile')
            ->with('success', 'Profile updated successfully.');
    }
}
