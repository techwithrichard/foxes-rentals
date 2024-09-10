<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\C2bRequest;
use App\Models\StkRequest;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class MpesaHistoryController extends Controller
{
    //customer to business transactions function
    public function c2bTransactions()
    {

        if (\request()->ajax()) {
            $transactions = C2bRequest::latest();

            return DataTables::of($transactions)
                ->filter(function ($query) {
                    if (request()->filled('status_filter')) {
                        $query->where('reconciliation_status', request()->get('status_filter'));
                    }

                }, true)
                ->addIndexColumn()
                ->addColumn('actions', function ($transaction) {
                    return view('admin.mpesa_transactions.c2b_actions', compact('transaction'));
                })
                ->editColumn('TransTime', function ($transaction) {
                    return date('d-m-Y H:i:s', strtotime($transaction->TransTime));
                })
                ->editColumn('reconciliation_status', function ($transaction) {
                    return view('admin.mpesa_transactions.partials.reconciliation_status', compact('transaction'));
                })
                ->rawColumns(['actions', 'reconciliation_status'])
                ->make(true);

        }

        return view('admin.mpesa_transactions.c2b_transactions');

    }

    //stk push transactions function
    public function stkPushTransactions()
    {

        if (\request()->ajax()) {
            $transactions = StkRequest::latest();

            return DataTables::of($transactions)
                ->addIndexColumn()
                ->addColumn('actions', function ($transaction) {
                    return view('admin.mpesa_transactions.stk_actions', compact('transaction'));
                })
                ->editColumn('TransactionDate', function ($transaction) {
                    //format of TransactionDate is available in the $transaction variable
                    if ($transaction->TransactionDate) {
                        return date('d-m-Y H:i:s', strtotime($transaction->TransactionDate));
                    }
                    return '';
                })
                ->rawColumns(['actions'])
                ->make(true);

        }
        return view('admin.mpesa_transactions.stk_transactions');
    }

    public function deleteTransaction($id)
    {

        $transaction = C2bRequest::findOrFail($id);
        $transaction->delete();

        return redirect()->back()->with('success', 'Transaction deleted successfully');

    }

    public function reconcileTransaction($id)
    {
        $transaction = C2bRequest::findOrFail($id);
        return view('admin.mpesa_transactions.reconcile', compact('transaction'));

    }


}
