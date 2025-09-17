<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;

class PropertySettingsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view settings'), 403);
        
        // Get property statistics
        $totalPropertyTypes = PropertyType::count();
        $activePropertyTypes = PropertyType::where('is_active', true)->count();
        
        // Get recent property types
        $recentPropertyTypes = PropertyType::latest()->limit(5)->get();
        
        return view('admin.property-settings.index', compact(
            'totalPropertyTypes',
            'activePropertyTypes',
            'recentPropertyTypes'
        ));
    }
    
    public function types()
    {
        abort_unless(auth()->user()->can('view settings'), 403);
        
        $propertyTypes = PropertyType::withCount('properties')
            ->orderBy('sort_order')
            ->get();
            
        return view('admin.property-settings.types', compact('propertyTypes'));
    }
    
    public function amenities()
    {
        abort_unless(auth()->user()->can('view settings'), 403);
        
        // This would typically show amenity management
        // For now, we'll show a placeholder
        $amenities = [
            'Air Conditioning',
            'Heating',
            'Internet',
            'Cable TV',
            'Laundry',
            'Dishwasher',
            'Microwave',
            'Refrigerator',
            'Stove',
            'Oven',
            'Parking',
            'Balcony',
            'Garden',
            'Swimming Pool',
            'Gym',
            'Security',
            'Elevator'
        ];
        
        return view('admin.property-settings.amenities', compact('amenities'));
    }
    
    public function pricing()
    {
        abort_unless(auth()->user()->can('view settings'), 403);
        
        // This would typically show pricing rules and commission settings
        $commissionRates = [
            'rental' => 10.0, // 10% commission for rentals
            'sale' => 5.0,    // 5% commission for sales
            'lease' => 8.0    // 8% commission for leases
        ];
        
        $feeStructures = [
            'listing_fee' => 100.0,
            'renewal_fee' => 50.0,
            'termination_fee' => 200.0
        ];
        
        return view('admin.property-settings.pricing', compact('commissionRates', 'feeStructures'));
    }
}
