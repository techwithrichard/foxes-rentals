<?php

namespace App\Http\Livewire\Landlord\Payment;

use App\Models\PaymentProof;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ManagePaymentProofComponent extends Component
{

    public PaymentProof $proof;

    public $status;
    public bool $recordPayment = false;
    public $remarks;

    protected $rules = [
        'status' => 'required|in:approved,rejected',
        'remarks' => 'nullable|required_if:status,==,rejected|string',
        'recordPayment' => 'declined_if:status,==,rejected',
    ];

    public function mount($proof)
    {
        $this->proof = $proof;
    }


    public function render(): Factory|View|Application
    {
        return view('livewire.landlord.payment.manage-payment-proof-component');
    }

    public function submit()
    {
        $this->validate();
        $data = [
            'reference_number' => $this->proof->reference_number,
            'amount' => @setting('currency_symbol') . '' . number_format($this->proof->amount, 2),
            'status' => $this->status,
            'remarks' => $this->remarks,
        ];

        $this->proof->update([
            'status' => $this->status,
            'remarks' => $this->remarks,
        ]);

        $this->proof->tenant->notify(new \App\Notifications\PaymentProofStatusNotification($data));

        return redirect()->route('admin.payments-proof.show', $this->proof->id)
            ->with('success', __('Payment proof status updated successfully'));

    }
}
