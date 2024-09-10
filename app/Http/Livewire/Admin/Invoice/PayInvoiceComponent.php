<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Enums\PaymentStatusEnum;
use App\Events\InvoicePaidEvent;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class PayInvoiceComponent extends Component
{
    use LivewireAlert;

    use WithFileUploads;

    public $invoice;


    public $amount;
    public $paid_at;
    public $payment_method = 'CASH';
    public $reference_number;
    public $recorded_by;
    public $receipt;
    public $payment_status = PaymentStatusEnum::PENDING;


    protected $listeners = ['payInvoice'];

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'paid_at' => 'required|date',
        'payment_method' => 'required|string',
        'reference_number' => 'nullable|string',
        'receipt' => 'nullable|file|max:4096|mimes:jpeg,png,jpg,pdf',
    ];

    public function render()
    {
        $paymentMethods = PaymentMethod::pluck('name');
        return view('livewire.admin.invoice.pay-invoice-component', compact('paymentMethods'));
    }

    public function payInvoice($id)
    {

        $this->invoice = Invoice::findOrFail($id);
        $this->emit('showPayInvoiceModal');
    }

    public function submit()
    {

//        dd($this->invoice);
        $this->validate();

        //if receipt is uploaded,store it in storage
        if ($this->receipt) {
            $receipt = Storage::url(Storage::putFile('public/receipts', $this->receipt));
        } else {
            $receipt = null;
        }
        DB::transaction(function () use ($receipt) {


            Payment::create([
                'amount' => $this->amount,
                'paid_at' => $this->paid_at,
                'payment_method' => $this->payment_method,
                'reference_number' => $this->reference_number,
                'tenant_id' => $this->invoice->tenant_id,
                'payment_receipt' => $receipt,
                'invoice_id' => $this->invoice->id,
                'recorded_by' => auth()->id(),
                'landlord_id' => $this->invoice->landlord_id,
                'commission' => $this->invoice->commission,
                'property_id' => $this->invoice->property_id,
                'house_id' => $this->invoice->house_id,
                'status' => $this->payment_status,

            ]);

            //if payment_status is paid,update invoice status to paid
            if ($this->payment_status == PaymentStatusEnum::PAID) {
//                dd('Pay invoice');
                $this->invoice->pay($this->amount);
                InvoicePaidEvent::dispatch($this->invoice);
            }


        });

        $this->alert('success', __('Payment recorded successfully!'));
        $this->emit('refreshTable');
        $this->dispatchBrowserEvent('file-pond-clear', ['id' => $this->id]);

        $this->reset(['amount', 'paid_at', 'payment_method', 'reference_number', 'recorded_by', 'receipt']);

    }

}
