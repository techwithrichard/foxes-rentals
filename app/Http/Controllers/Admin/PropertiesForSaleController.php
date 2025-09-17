<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\SaleProperty;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PropertiesForSaleController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        if (request()->ajax()) {
            $properties = SaleProperty::query()
                ->with(['propertyType', 'landlord'])
                ->select('sale_properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->filter(function ($query) {
                    if (request()->filled('status_filter')) {
                        $query->where('status', request()->get('status_filter'));
                    }
                    if (request()->filled('landlord_filter')) {
                        $query->where('landlord_id', request()->get('landlord_filter'));
                    }
                    if (request()->filled('price_range')) {
                        $range = request()->get('price_range');
                        if ($range === 'under_500k') {
                            $query->where('sale_price', '<', 500000);
                        } elseif ($range === '500k_to_1m') {
                            $query->whereBetween('sale_price', [500000, 1000000]);
                        } elseif ($range === 'over_1m') {
                            $query->where('sale_price', '>', 1000000);
                        }
                    }
                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-sale.partials.actions', compact('property'));
                })
                ->addColumn('status', function ($property) {
                    return view('admin.properties-for-sale.partials.status', compact('property'));
                })
                ->addColumn('sale_price', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->sale_price, 2);
                })
                ->addColumn('property_type', function ($property) {
                    return $property->propertyType->name ?? 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
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

        return view('admin.properties-for-sale.index', compact('landlords'));
    }
    
    public function activeListings()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        if (request()->ajax()) {
            $properties = SaleProperty::query()
                ->with(['propertyType', 'landlord'])
                ->where('status', 'active')
                ->where('is_available', true)
                ->where('is_published', true)
                ->select('sale_properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-sale.partials.listing-actions', compact('property'));
                })
                ->addColumn('sale_price', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->sale_price, 2);
                })
                ->addColumn('property_type', function ($property) {
                    return $property->propertyType->name ?? 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->addColumn('views_count', function ($property) {
                    return number_format($property->views_count ?? 0);
                })
                ->addColumn('inquiries_count', function ($property) {
                    return number_format($property->inquiries_count ?? 0);
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-sale.active-listings');
    }
    
    public function pendingSales()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        if (request()->ajax()) {
            $properties = SaleProperty::query()
                ->with(['propertyType', 'landlord'])
                ->where('status', 'pending')
                ->select('sale_properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-sale.partials.pending-actions', compact('property'));
                })
                ->addColumn('sale_price', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->sale_price, 2);
                })
                ->addColumn('property_type', function ($property) {
                    return $property->propertyType->name ?? 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-sale.pending-sales');
    }
    
    public function offers()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        // This would typically show property offers
        // For now, we'll show properties with offers_count > 0
        if (request()->ajax()) {
            $properties = SaleProperty::query()
                ->with(['propertyType', 'landlord'])
                ->where('offers_count', '>', 0)
                ->select('sale_properties.*')
                ->latest('id');

            return DataTables::of($properties)
                ->addIndexColumn()
                ->addColumn('actions', function ($property) {
                    return view('admin.properties-for-sale.partials.offer-actions', compact('property'));
                })
                ->addColumn('sale_price', function ($property) {
                    return setting('currency_symbol') . ' ' . number_format($property->sale_price, 2);
                })
                ->addColumn('property_type', function ($property) {
                    return $property->propertyType->name ?? 'N/A';
                })
                ->addColumn('landlord', function ($property) {
                    return $property->landlord->name ?? 'N/A';
                })
                ->addColumn('offers_count', function ($property) {
                    return number_format($property->offers_count ?? 0);
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.properties-for-sale.offers');
    }
}
