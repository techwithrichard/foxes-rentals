<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\House;
use App\Models\Lease;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class DeletedRecordsController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->can('view deleted records'), 403);
        return view('admin.deleted-records.index');
    }

    public function houses(Request $request)
    {
        abort_unless(auth()->user()->can('view deleted records'), 403);
        
        if ($request->ajax()) {
            $houses = House::onlyTrashed()
                ->with(['property:id,name', 'landlord:id,name'])
                ->select('houses.*');

            return DataTables::of($houses)
                ->addIndexColumn()
                ->addColumn('actions', function ($house) {
                    return view('admin.deleted-records.partials.house-actions', compact('house'));
                })
                ->addColumn('property', function ($house) {
                    return $house->property->name ?? 'N/A';
                })
                ->addColumn('landlord', function ($house) {
                    return $house->landlord->name ?? 'N/A';
                })
                ->addColumn('deleted_at', function ($house) {
                    return $house->deleted_at->format('M d, Y H:i');
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.deleted-records.houses');
    }

    public function leases(Request $request)
    {
        abort_unless(auth()->user()->can('view deleted records'), 403);
        
        if ($request->ajax()) {
            $leases = Lease::onlyTrashed()
                ->with(['tenant:id,name', 'house:id,name', 'property:id,name'])
                ->select('leases.*');

            return DataTables::of($leases)
                ->addIndexColumn()
                ->addColumn('actions', function ($lease) {
                    return view('admin.deleted-records.partials.lease-actions', compact('lease'));
                })
                ->addColumn('tenant', function ($lease) {
                    return $lease->tenant->name ?? 'N/A';
                })
                ->addColumn('house', function ($lease) {
                    return $lease->house->name ?? 'N/A';
                })
                ->addColumn('property', function ($lease) {
                    return $lease->property->name ?? 'N/A';
                })
                ->addColumn('deleted_at', function ($lease) {
                    return $lease->deleted_at->format('M d, Y H:i');
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.deleted-records.leases');
    }

    public function tenants(Request $request)
    {
        abort_unless(auth()->user()->can('view deleted records'), 403);
        
        if ($request->ajax()) {
            $tenants = User::onlyTrashed()
                ->whereHas('roles', function($query) {
                    $query->where('name', 'tenant');
                })
                ->select('users.*');

            return DataTables::of($tenants)
                ->addIndexColumn()
                ->addColumn('actions', function ($tenant) {
                    return view('admin.deleted-records.partials.tenant-actions', compact('tenant'));
                })
                ->addColumn('deleted_at', function ($tenant) {
                    return $tenant->deleted_at->format('M d, Y H:i');
                })
                ->rawColumns(['actions'])
                ->make(true);
        }

        return view('admin.deleted-records.tenants');
    }

    public function restoreHouse($id)
    {
        abort_unless(auth()->user()->can('restore deleted records'), 403);
        
        $house = House::onlyTrashed()->findOrFail($id);
        $house->restore();
        
        return response()->json([
            'success' => true,
            'message' => 'House restored successfully'
        ]);
    }

    public function restoreLease($id)
    {
        abort_unless(auth()->user()->can('restore deleted records'), 403);
        
        $lease = Lease::onlyTrashed()->findOrFail($id);
        $lease->restore();
        
        return response()->json([
            'success' => true,
            'message' => 'Lease restored successfully'
        ]);
    }

    public function restoreTenant($id)
    {
        abort_unless(auth()->user()->can('restore deleted records'), 403);
        
        $tenant = User::onlyTrashed()->findOrFail($id);
        $tenant->restore();
        
        return response()->json([
            'success' => true,
            'message' => 'Tenant restored successfully'
        ]);
    }

    public function permanentlyDeleteHouse($id)
    {
        abort_unless(auth()->user()->can('permanently delete records') && auth()->user()->hasRole('admin'), 403);
        
        DB::transaction(function () use ($id) {
            $house = House::onlyTrashed()->findOrFail($id);
            
            // Permanently delete associated leases
            $house->leases()->withTrashed()->forceDelete();
            
            // Permanently delete the house
            $house->forceDelete();
        });
        
        return response()->json([
            'success' => true,
            'message' => 'House permanently deleted'
        ]);
    }

    public function permanentlyDeleteLease($id)
    {
        abort_unless(auth()->user()->can('permanently delete records') && auth()->user()->hasRole('admin'), 403);
        
        $lease = Lease::onlyTrashed()->findOrFail($id);
        $lease->forceDelete();
        
        return response()->json([
            'success' => true,
            'message' => 'Lease permanently deleted'
        ]);
    }

    public function permanentlyDeleteTenant($id)
    {
        abort_unless(auth()->user()->can('permanently delete records') && auth()->user()->hasRole('admin'), 403);
        
        DB::transaction(function () use ($id) {
            $tenant = User::onlyTrashed()->findOrFail($id);
            
            // Permanently delete associated leases
            $tenant->leases()->withTrashed()->forceDelete();
            
            // Permanently delete the tenant
            $tenant->forceDelete();
        });
        
        return response()->json([
            'success' => true,
            'message' => 'Tenant permanently deleted'
        ]);
    }
}