<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Property;
use App\Models\Lease;
use App\Models\RentalProperty;
use App\Models\SaleProperty;
use App\Models\LeaseProperty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PropertyDashboardController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        // Get key metrics
        $totalProperties = Property::count();
        $totalRentalProperties = RentalProperty::count();
        $totalSaleProperties = SaleProperty::count();
        $totalLeaseProperties = LeaseProperty::count();
        
        // Occupancy metrics
        $occupiedProperties = Property::where('is_vacant', false)->count();
        $vacantProperties = Property::where('is_vacant', true)->count();
        $occupancyRate = $totalProperties > 0 ? round(($occupiedProperties / $totalProperties) * 100, 2) : 0;
        
        // Revenue metrics
        $totalRentRevenue = Lease::sum('rent');
        $monthlyRentRevenue = Lease::whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->sum('rent');
        
        // Recent activity
        $recentProperties = Property::with(['address', 'landlord'])
            ->latest()
            ->limit(5)
            ->get();
            
        $recentLeases = Lease::with(['tenant', 'property'])
            ->latest()
            ->limit(5)
            ->get();
        
        // Performance metrics
        $propertiesByType = Property::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();
            
        $propertiesByStatus = Property::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();
        
        return view('admin.property-dashboard.index', compact(
            'totalProperties',
            'totalRentalProperties',
            'totalSaleProperties',
            'totalLeaseProperties',
            'occupiedProperties',
            'vacantProperties',
            'occupancyRate',
            'totalRentRevenue',
            'monthlyRentRevenue',
            'recentProperties',
            'recentLeases',
            'propertiesByType',
            'propertiesByStatus'
        ));
    }
    
    public function analytics()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        // Advanced analytics data
        $monthlyRevenue = Lease::select(
                DB::raw('MONTH(start_date) as month'),
                DB::raw('YEAR(start_date) as year'),
                DB::raw('SUM(rent) as revenue')
            )
            ->whereYear('start_date', now()->year)
            ->groupBy('year', 'month')
            ->orderBy('year', 'month')
            ->get();
            
        $propertyPerformance = Property::with(['leases'])
            ->withCount('leases')
            ->withSum('leases', 'rent')
            ->orderBy('leases_sum_rent', 'desc')
            ->limit(10)
            ->get();
        
        return view('admin.property-dashboard.analytics', compact(
            'monthlyRevenue',
            'propertyPerformance'
        ));
    }
    
    public function metrics()
    {
        abort_unless(auth()->user()->can('view property'), 403);
        
        // Return JSON data for AJAX requests
        $metrics = [
            'total_properties' => Property::count(),
            'occupied_properties' => Property::where('is_vacant', false)->count(),
            'vacant_properties' => Property::where('is_vacant', true)->count(),
            'total_revenue' => Lease::sum('rent'),
            'monthly_revenue' => Lease::whereMonth('start_date', now()->month)
                ->whereYear('start_date', now()->year)
                ->sum('rent'),
            'occupancy_rate' => Property::count() > 0 ? 
                round((Property::where('is_vacant', false)->count() / Property::count()) * 100, 2) : 0
        ];
        
        return response()->json($metrics);
    }
}
