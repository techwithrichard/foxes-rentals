<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;

class RejectPaymentComponent extends Component
{
    use LivewireAlert;
    public $paymentId;

    protected $listeners = ['rejectPayment'];

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.admin.invoice.reject-payment-component');
    }

    public function rejectPayment($id): void
    {
        $this->paymentId = $id;
        $this->emit('showRejectPaymentModal');

    }

    public function submit(): void
    {
        $payment = Payment::findOrFail($this->paymentId);
        $payment->status = PaymentStatusEnum::CANCELLED->value;
        $payment->save();
        $this->alert('success', __('Payment rejected successfully!'));
        $this->emit('refreshTable');


    }
}
