<?php

namespace App\Http\Controllers\Landlord;

use App\Http\Controllers\Controller;
use App\Models\LandlordRemittance;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PayoutsController extends Controller
{

    public function index()
    {
        if (request()->ajax()) {
            $remittances = LandlordRemittance::query()
                ->where('landlord_id', auth()->id())
                ->latest();

            return DataTables::of($remittances)
                ->addIndexColumn()
                ->editColumn('paid_on', function ($remittance) {
                    return $remittance->paid_on->format('M d, Y');
                })
                ->addColumn('period', function ($remittance) {
                    return view('admin.landlord_remittances.partials.period', compact('remittance'))->render();

                })
                ->addColumn('reference', function ($payout) {
                    return view('landlord.payouts.reference', compact('payout'))->render();
                })
                ->editColumn('amount', function ($remittance) {
                    return setting('currency-symbol') . ' ' . number_format($remittance->amount, 2);
                })
                ->rawColumns(['reference'])
                ->make(true);
        }
        return view('landlord.payouts.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
