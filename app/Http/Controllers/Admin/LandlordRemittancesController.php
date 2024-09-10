<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LandlordRemittance;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class LandlordRemittancesController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view landlord remittance'), 403);
        if (request()->ajax()) {
            $remittances = LandlordRemittance::with('landlord');
            return DataTables::of($remittances)
                //filter by month
                ->filter(function ($query) {
                    if (request()->filled('landlord_filter')) {
                        $query->where('landlord_id', request('landlord_filter'));
                    }
                }, true)
                ->addIndexColumn()
                ->editColumn('paid_on', function ($remittance) {
                    return $remittance->paid_on->format('M d, Y');
                })
                ->addColumn('landlord', function ($remittance) {
                    return $remittance->landlord?->name;
                })
                ->editColumn('amount', function ($remittance) {
                    return setting('currency-symbol') . ' ' . number_format($remittance->amount, 2);
                })
                ->addColumn('period', function ($remittance) {
                    return view('admin.landlord_remittances.partials.period', compact('remittance'))->render();

                })
                ->addColumn('action', function ($remittance) {
                    return view('admin.landlord_remittances.partials.actions', compact('remittance'))->render();
                })
                ->rawColumns(['action', 'period'])
                ->make(true);
        }

        $landlords = User::role('landlord')
            ->orderBy('name')
            ->pluck('name', 'id');
        return view('admin.landlord_remittances.index', compact('landlords'));
    }


    public function create()
    {
        abort_unless(auth()->user()->can('create landlord remittance'), 403);
        return view('admin.landlord_remittances.create');
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }

    public function destroy($id)
    {
        abort_unless(auth()->user()->can('delete landlord remittance'), 403);
        $remittance = LandlordRemittance::findOrFail($id);
        $remittance->delete();
        return redirect()->back()->with('success', __('Landlord remittance deleted successfully'));
    }
}
