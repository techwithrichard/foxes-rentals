<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\LeaseProperty;
use App\Models\Lease;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PropertiesForLeaseController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        if (request()->ajax()) {
            $properties = LeaseProperty::query()
                ->with(['propertyType', 'landlord'])
                ->select('lease_properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->filter(function ($query) {
                    if (request()->filled('status_filter')) {
                        $query->where('status', request()->get('status_filter'));
                    }
                    if (request()->filled('landlord_filter')) {
                        $query->where('landlord_id', request()->get('landlord_filter'));
                    }
                    if (request()->filled('lease_duration')) {
                        $duration = request()->get('lease_duration');
                        if ($duration === 'short_term') {
                            $query->where('lease_duration_months', '<=', 12);
                        } elseif ($duration === 'long_term') {
                            $query->where('lease_duration_months', '>', 12);
                        }
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-lease.partials.actions', compact('property'));
                })
                ->addColumn('status', function ($property) {
                    return view('admin.properties-for-lease.partials.status', compact('property'));
                })
                ->addColumn('lease_amount', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->lease_amount, 2);
                })
                ->addColumn('property_type', function ($property) {
                    return $property->propertyType->name ?? 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->addColumn('lease_duration', function ($property) {
                    return $property->lease_duration_months . ' months';
                })
                ->addColumn('features', function ($property) {
                    $features = [];
                    if ($property->bedrooms) $features[] = $property->bedrooms . ' bed';
                    if ($property->bathrooms) $features[] = $property->bathrooms . ' bath';
                    if ($property->property_size) $features[] = number_format($property->property_size) . ' sq ft';
                    return implode(', ', $features);
                })
                ->rawColumns(['actions', 'status'])
                ->make(true);
        }

        $landlords = \App\Models\User::whereHas('roles', function($q) {
            $q->where('name', 'landlord');
        })->pluck('name', 'id');

        return view('admin.properties-for-lease.index', compact('landlords'));
    }
    
    public function activeLeases()
    {
        abort_unless(auth()->user()->can('view lease'), 403);
        
        if (request()->ajax()) {
            $leases = Lease::query()
                ->with(['tenant', 'property', 'house'])
                ->whereNull('deleted_at')
                ->select('leases.*')
                ->latest('id');

            return DataTables::of($leases)
                ->addIndexColumn()
                ->addColumn('actions', function ($lease) {
                    return view('admin.properties-for-lease.partials.lease-actions', compact('lease'));
                })
                ->addColumn('tenant', function ($lease) {
                    return $lease->tenant->name ?? 'N/A';
                })
                ->addColumn('property', function ($lease) {
                    return $lease->property->name ?? 'N/A';
                })
                ->addColumn('house', function ($lease) {
                    return $lease->house->name ?? 'N/A';
                })
                ->addColumn('rent', function ($lease) {
                    return setting('currency_symbol') . ' ' . number_format($lease->rent, 2);
                })
                ->addColumn('start_date', function ($lease) {
                    return $lease->start_date->format('M d, Y');
                })
                ->addColumn('end_date', function ($lease) {
                    return $lease->end_date ? $lease->end_date->format('M d, Y') : 'N/A';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-lease.active-leases');
    }
    
    public function leaseApplications()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        // This would typically show lease applications
        // For now, we'll show available lease properties
        if (request()->ajax()) {
            $properties = LeaseProperty::query()
                ->with(['propertyType', 'landlord'])
                ->where('status', 'active')
                ->where('is_available', true)
                ->select('lease_properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-lease.partials.application-actions', compact('property'));
                })
                ->addColumn('lease_amount', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->lease_amount, 2);
                })
                ->addColumn('property_type', function ($property) {
                    return $property->propertyType->name ?? 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->addColumn('lease_duration', function ($property) {
                    return $property->lease_duration_months . ' months';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-lease.lease-applications');
    }
    
    public function renewals()
    {
        abort_unless(auth()->user()->can('view lease'), 403);
        
        // Show leases that are expiring soon (within 3 months)
        if (request()->ajax()) {
            $leases = Lease::query()
                ->with(['tenant', 'property', 'house'])
                ->whereNull('deleted_at')
                ->where('end_date', '<=', now()->addMonths(3))
                ->where('end_date', '>=', now())
                ->select('leases.*')
                ->latest('end_date');

            return DataTables::of($leases)
                ->addIndexColumn()
                ->addColumn('actions', function ($lease) {
                    return view('admin.properties-for-lease.partials.renewal-actions', compact('lease'));
                })
                ->addColumn('tenant', function ($lease) {
                    return $lease->tenant->name ?? 'N/A';
                })
                ->addColumn('property', function ($lease) {
                    return $lease->property->name ?? 'N/A';
                })
                ->addColumn('house', function ($lease) {
                    return $lease->house->name ?? 'N/A';
                })
                ->addColumn('rent', function ($lease) {
                    return setting('currency_symbol') . ' ' . number_format($lease->rent, 2);
                })
                ->addColumn('start_date', function ($lease) {
                    return $lease->start_date->format('M d, Y');
                })
                ->addColumn('end_date', function ($lease) {
                    return $lease->end_date ? $lease->end_date->format('M d, Y') : 'N/A';
                })
                ->addColumn('days_remaining', function ($lease) {
                    if ($lease->end_date) {
                        $days = now()->diffInDays($lease->end_date, false);
                        return $days > 0 ? $days . ' days' : 'Expired';
                    }
                    return 'N/A';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-lease.renewals');
    }
}
