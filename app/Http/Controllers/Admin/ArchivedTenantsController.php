<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class ArchivedTenantsController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view archived tenant'), 403);
        if (\request()->ajax()) {
            $tenants = User::query()
                ->onlyTrashed()
                ->role('tenant')->latest('id');

            return DataTables::of($tenants)
                ->addIndexColumn()
                ->addColumn('actions', function ($tenant) {
                    return view('admin.archived_tenants.partials.actions', compact('tenant'));
//                    return '<a href="' . route('admin.tenants.show', $tenant->id) . '" class="btn btn-sm btn-primary">View</a>';
                })
                ->editColumn('created_at', function ($tenant) {
                    return $tenant->created_at->diffForHumans();
                })
                ->editColumn('updated_at', function ($tenant) {
                    return $tenant->updated_at->diffForHumans();
                })
                ->rawColumns(['actions'])
                ->setRowClass('nk-tb-item')
                ->make(true);
        }
        return view('admin.archived_tenants.index');
    }

    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete archived tenant'), 403);
        //force delete tenant
        $tenant = User::withTrashed()->findOrFail($id);
        $tenant->forceDelete();
        $message = __('Tenant') . ' ' . '' . $tenant->name . ' ' . __('account has been deleted permanently');
        return redirect()->route('admin.archived-tenants.index')->with('success', $message);
    }

    public function restore($id)
    {
        abort_unless(auth()->user()->can('restore archived tenant'), 403);

        $tenant = User::withTrashed()->findOrFail($id);
        $tenant->restore();

        $message = __('Tenant') . ' ' . $tenant->name . ' ' . __('has been restored successfully');

        return redirect()->route('admin.tenants.index')->with('success', $message);
    }
}
