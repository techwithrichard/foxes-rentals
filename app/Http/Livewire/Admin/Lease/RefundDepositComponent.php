<?php

namespace App\Http\Livewire\Admin\Lease;

use App\Models\Deposit;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class RefundDepositComponent extends Component
{
    use LivewireAlert, WithFileUploads;

    public $depositId;

    public $amount, $refund_date, $refund_receipt;

    protected $rules = [
        'amount' => 'required|numeric',
        'refund_date' => 'required|date',
        'refund_receipt' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:12048',

    ];

    protected $listeners = ['refundDeposit'];

    public function render()
    {
        return view('livewire.admin.lease.refund-deposit-component');
    }

    public function refundDeposit($id)
    {
        $this->depositId = $id;
        $this->emit('showRefundModal');
    }


    public function submit()
    {
        $this->validate();


        $deposit = Deposit::with('tenant')->findOrFail($this->depositId);

        if ($this->refund_receipt) {
            $receipt = Storage::url(Storage::putFile('public/documents', $this->refund_receipt));
        } else {
            $receipt = null;
        }

        $deposit->update([
            'refund_amount' => $this->amount,
            'refund_date' => $this->refund_date,
            'refund_receipt' => $receipt,
            'refund_paid' => 1,
        ]);

        $details = [
            'date' => $this->refund_date,
            'amount' => $this->amount,
//            'tenant' => $deposit->tenant->name,
//            'property' => $deposit->lease->house->property->name,
//            'house' => $deposit->lease->house->name
        ];

        //notify tenant of deposit refund
        $deposit->tenant->notify(new \App\Notifications\DepositRefundedNotification($details));


        $this->alert('success', __('Deposit Refunded Successfully'));

        $this->reset(['amount', 'refund_date', 'refund_receipt', 'depositId']);

        $this->emit('refreshTable');

    }
}
