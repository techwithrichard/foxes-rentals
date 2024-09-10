<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Enums\PaymentStatusEnum;
use App\Events\InvoicePaidEvent;
use App\Models\Payment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class ApprovePaymentComponent extends Component
{
    use LivewireAlert;

    public $paymentId;

    protected $listeners = ['approvePayment'];

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.admin.invoice.approve-payment-component');
    }

    public function approvePayment($id): void
    {
        $this->paymentId = $id;
        $this->emit('showApprovePaymentModal');


    }

    public function submit(): void
    {
        $payment = Payment::with('invoice')->findOrFail($this->paymentId);

        DB::transaction(function () use ($payment) {
            $payment->status = PaymentStatusEnum::PAID->value;
            $payment->save();
            $payment->invoice->pay($payment->amount);
            InvoicePaidEvent::dispatch($payment->invoice);
        });
        $this->alert('success', __('Payment approved successfully!'));
        $this->emit('refreshTable');


    }
}
