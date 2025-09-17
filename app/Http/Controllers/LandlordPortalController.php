<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Property;
use App\Models\Lease;
use App\Models\Payment;

class LandlordPortalController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:landlord']);
    }

    /**
     * Display the landlord dashboard
     */
    public function index()
    {
        $user = auth()->user();
        
        // Get landlord's properties
        $properties = Property::where('landlord_id', $user->id)
            ->with(['leases', 'payments'])
            ->get();
        
        // Get recent payments
        $recentPayments = Payment::whereHas('lease', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('landlord_id', $user->id);
            });
        })->latest()->limit(10)->get();
        
        // Calculate statistics
        $stats = [
            'total_properties' => $properties->count(),
            'occupied_properties' => $properties->where('is_vacant', false)->count(),
            'vacant_properties' => $properties->where('is_vacant', true)->count(),
            'total_rent_collected' => $recentPayments->sum('amount'),
            'pending_payments' => Payment::whereHas('lease', function($query) use ($user) {
                $query->whereHas('property', function($q) use ($user) {
                    $q->where('landlord_id', $user->id);
                });
            })->where('status', 'pending')->sum('amount'),
        ];

        return view('portals.landlord.dashboard', compact('properties', 'recentPayments', 'stats'));
    }

    /**
     * Display landlord's properties
     */
    public function properties()
    {
        $user = auth()->user();
        $properties = Property::where('landlord_id', $user->id)
            ->with(['leases.tenant', 'payments'])
            ->paginate(20);

        return view('portals.landlord.properties', compact('properties'));
    }

    /**
     * Display landlord's tenants
     */
    public function tenants()
    {
        $user = auth()->user();
        $tenants = User::role('tenant')
            ->whereHas('leases.property', function($query) use ($user) {
                $query->where('landlord_id', $user->id);
            })
            ->with(['leases.property'])
            ->paginate(20);

        return view('portals.landlord.tenants', compact('tenants'));
    }

    /**
     * Display landlord's payments
     */
    public function payments()
    {
        $user = auth()->user();
        $payments = Payment::whereHas('lease', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('landlord_id', $user->id);
            });
        })->with(['lease.property', 'lease.tenant'])
        ->latest()
        ->paginate(20);

        return view('portals.landlord.payments', compact('payments'));
    }

    /**
     * Display landlord's reports
     */
    public function reports()
    {
        $user = auth()->user();
        
        // Calculate monthly income
        $monthlyIncome = Payment::whereHas('lease', function($query) use ($user) {
            $query->whereHas('property', function($q) use ($user) {
                $q->where('landlord_id', $user->id);
            });
        })->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->sum('amount');

        return view('portals.landlord.reports', compact('monthlyIncome'));
    }
}
