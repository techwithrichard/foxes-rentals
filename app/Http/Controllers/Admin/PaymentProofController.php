<?php

namespace App\Http\Controllers\Admin;

use App\Enums\PaymentProofStatusEnum;
use App\Http\Controllers\Controller;
use App\Models\PaymentProof;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PaymentProofController extends Controller
{

    public function index()
    {
        $pendingProofsCount = PaymentProof::where('status', PaymentProofStatusEnum::PENDING->value)->count();
        if (\request()->ajax()) {
            $proofs = PaymentProof::with('tenant:id,name', 'invoice')->latest();

            return DataTables::of($proofs)
                ->addIndexColumn()
                ->addColumn('actions', function ($proof) {
                    return view('landlord.payment_proofs.partials.actions', compact('proof'));
                })
                ->editColumn('status', function ($proof) {
                    return view('landlord.payment_proofs.partials.status', compact('proof'));
                })
                ->addColumn('tenant', function ($proof) {
                    return $proof->tenant->name;
                })
                ->editColumn('receipt_document', function ($proof) {
                    return view('landlord.payment_proofs.partials.document', compact('proof'));
                })
                ->editColumn('amount', function ($proof) {
                    return setting('currency_symbol') . ' ' . number_format($proof->amount, 2);
                })
                ->editColumn('invoice_id', function ($proof) {
                    return view('landlord.payment_proofs.partials.invoice', compact('proof'));

                })
                ->editColumn('created_at', function ($proof) {
                    return $proof->created_at->format('d M Y, h:i A');
                })
                ->rawColumns(['actions', 'status', 'invoice_id', 'receipt_document'])
                ->make(true);
        }

        return view('landlord.payment_proofs.index', compact('pendingProofsCount'));
    }


    public function create()
    {
        //
    }


    public function store(Request $request)
    {
        //
    }


    public function show($id)
    {
        $proof = PaymentProof::with('tenant', 'invoice')->findOrFail($id);
        return view('landlord.payment_proofs.show', compact('proof'));
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        $proof = PaymentProof::findOrFail($id);
        $proof->delete();
        //remove file
        if (file_exists(public_path('storage/' . $proof->receipt_document))) {
            unlink(public_path('storage/' . $proof->receipt_document));
        }
        return redirect()->route('admin.payments-proof.index')->with('success', __('Payment proof deleted successfully'));
    }
}
