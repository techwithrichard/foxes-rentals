<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\RentalProperty;
use App\Models\Lease;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PropertiesForRentController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        if (request()->ajax()) {
            $properties = Property::query()
                ->with(['address', 'landlord', 'leases.tenant'])
                ->where('status', 'active')
                ->select('properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->filter(function ($query) {
                    if (request()->filled('status_filter')) {
                        $query->where('is_vacant', request()->get('status_filter'));
                    }
                    if (request()->filled('landlord_filter')) {
                        $query->where('landlord_id', request()->get('landlord_filter'));
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-rent.partials.actions', compact('property'));
                })
                ->addColumn('status', function ($property) {
                    return view('admin.properties-for-rent.partials.status', compact('property'));
                })
                ->addColumn('tenant', function ($property) {
                    $activeLease = $property->leases()->whereNull('deleted_at')->first();
                    return $activeLease ? $activeLease->tenant->name : 'Vacant';
                })
                ->addColumn('rent_amount', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->rent ?? 0, 2);
                })
                ->addColumn('address', function ($property) {
                    return $property->address ? $property->address->city . ', ' . $property->address->state : 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        $landlords = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'landlord');
        })->pluck('name', 'id');

        return view('admin.properties-for-rent.index', compact('landlords'));
    }
    
    public function vacant()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        if (request()->ajax()) {
            $properties = Property::query()
                ->with(['address', 'landlord'])
                ->where('status', 'active')
                ->where('is_vacant', true)
                ->select('properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-rent.partials.vacant-actions', compact('property'));
                })
                ->addColumn('rent_amount', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->rent ?? 0, 2);
                })
                ->addColumn('address', function ($property) {
                    return $property->address ? $property->address->city . ', ' . $property->address->state : 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-rent.vacant');
    }
    
    public function applications()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        // This would typically show rental applications
        // For now, we'll show properties with pending lease applications
        if (request()->ajax()) {
            $properties = Property::query()
                ->with(['address', 'landlord'])
                ->where('status', 'active')
                ->where('is_vacant', true)
                ->select('properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-rent.partials.application-actions', compact('property'));
                })
                ->addColumn('rent_amount', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->rent ?? 0, 2);
                })
                ->addColumn('address', function ($property) {
                    return $property->address ? $property->address->city . ', ' . $property->address->state : 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-rent.applications');
    }
    
    public function activeRentals()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        if (request()->ajax()) {
            $properties = Property::query()
                ->with(['address', 'landlord', 'leases.tenant'])
                ->where('status', 'active')
                ->where('is_vacant', false)
                ->select('properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-rent.partials.rental-actions', compact('property'));
                })
                ->addColumn('tenant', function ($property) {
                    $activeLease = $property->leases()->whereNull('deleted_at')->first();
                    return $activeLease ? $activeLease->tenant->name : 'N/A';
                })
                ->addColumn('rent_amount', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->rent ?? 0, 2);
                })
                ->addColumn('address', function ($property) {
                    return $property->address ? $property->address->city . ', ' . $property->address->state : 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-rent.active-rentals');
    }
}
