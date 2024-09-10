<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class DepositsController extends Controller
{

    public function index()
    {
        abort_unless(auth()->user()->can('view deposit'), 403);
        if (\request()->ajax()) {
            $deposits = Deposit::query()
                ->with('lease', 'lease.house:id,name', 'lease.property:id,name', 'tenant:id,name')
                ->latest('id');

            return DataTables::of($deposits)
                ->addIndexColumn()
                ->addColumn('tenant', function ($deposit) {
                    return $deposit->tenant->name;
                })
                ->addColumn('property', function ($deposit) {
                    return $deposit->lease?->property?->name??'-';
                })
                ->addColumn('house', function ($deposit) {
                    return $deposit->lease?->house?->name??'-';
                })
                ->addColumn('amount', function ($deposit) {
                    return setting('currency_symbol') . ' ' . number_format($deposit->amount, 2);
                })
                ->addColumn('actions', function ($deposit) {
                    return view('admin.deposits.action', compact('deposit'));
                })
                ->editColumn('created_at', function ($deposit) {
                    return $deposit->created_at->format('d M Y');
                })
                ->editColumn('refund_paid', function ($deposit) {
                    return view('admin.deposits.refund', compact('deposit'));
                })
                ->rawColumns(['actions','refund_paid'])
                ->make(true);
        }
        return view('admin.deposits.index');
    }


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


    public function show($id)
    {
        abort_unless(auth()->user()->can('view deposit'), 403);
        $deposit = Deposit::query()
            ->with('lease', 'lease.house:id,name', 'lease.property:id,name', 'tenant:id,name')
            ->findOrFail($id);

        return view('admin.deposits.show', compact('deposit'));
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
