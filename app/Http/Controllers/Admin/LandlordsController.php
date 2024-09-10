<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CustomInvoice;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LandlordsController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view landlord'), 403);
        if (\request()->ajax()) {
            $landlords = User::withoutTrashed()
                ->role('landlord')
                ->withCount('properties', 'houses')
                ->latest('id');

            return DataTables::of($landlords)
                ->addIndexColumn()
                ->addColumn('actions', function ($landlord) {
                    return view('admin.landlords.partials.actions', compact('landlord'))->render();
                })
                ->addColumn('ownership', function ($landlord) {

                    return view('admin.landlords.partials.ownership', compact('landlord'))->render();
                })
                ->rawColumns(['actions', 'ownership'])
                ->make(true);
        }
        return view('admin.landlords.index');
    }


    public function create()
    {
        abort_unless(auth()->user()->can('create landlord'), 403);
        return view('admin.landlords.create');
    }

    public function show($id)
    {
        abort_unless(auth()->user()->can('view landlord'), 403);
        $landlord = User::query()
            ->withoutTrashed()
            ->role('landlord')
            ->with('properties', 'houses')
            ->findOrFail($id);
        $invoices = CustomInvoice::query()
            ->where('landlord_id', $id)
            ->get();
        $vouchers = Voucher::query()
            ->with('property:id,name', 'house:id,name')
            ->withSum('items', 'cost')
            ->where('landlord_id', $id)
            ->get();
        return view('admin.landlords.show',
            compact('landlord', 'invoices', 'vouchers'));

    }

    public function edit($id)
    {
        abort_unless(auth()->user()->can('edit landlord'), 403);
        $landlord = User::query()
            ->withoutTrashed()
            ->role('landlord')
            ->findOrFail($id);


        return view('admin.landlords.edit', compact('id'));
    }


    public function destroy($id)
    {
         //force delete the user with id
         $landlord = User::query()
         ->withTrashed()
         ->role('landlord')
         ->findOrFail($id);
     $landlord->forceDelete();

     return redirect()->route('admin.landlords.index')
         ->with('success', __('Landlord deleted successfully'));
    }
}
