<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Enums\PaymentStatusEnum;
use App\Events\InvoicePaidEvent;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Services\EnhancedPaymentService;
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

    protected $enhancedPaymentService;

    protected $listeners = ['payInvoice'];

    protected $rules = [
        'amount' => 'required|numeric|min:1',
        'paid_at' => 'required|date',
        'payment_method' => 'required|string',
        'reference_number' => 'nullable|string',
        'receipt' => 'nullable|file|max:4096|mimes:jpeg,png,jpg,pdf',
    ];

    public function mount()
    {
        $this->enhancedPaymentService = app(EnhancedPaymentService::class);
    }

    public function render()
    {
        // Get payment methods from database or use default enhanced methods
        $paymentMethods = PaymentMethod::pluck('name')->toArray();
        
        // If no payment methods in database, use enhanced service methods
        if (empty($paymentMethods)) {
            $paymentMethods = array_keys($this->enhancedPaymentService->getAvailablePaymentMethods());
        }
        
        return view('livewire.admin.invoice.pay-invoice-component', compact('paymentMethods'));
    }

    public function payInvoice($id)
    {

        $this->invoice = Invoice::findOrFail($id);
        $this->emit('showPayInvoiceModal');
    }

    public function submit()
    {
        $this->validate();

        try {
            // Store receipt if uploaded
            $receipt = null;
            if ($this->receipt) {
                $receipt = Storage::url(Storage::putFile('public/receipts', $this->receipt));
            }

            // Prepare payment data with enhanced synchronization
            $paymentData = [
                'amount' => $this->amount,
                'paid_at' => $this->paid_at,
                'payment_method' => $this->payment_method,
                'reference_number' => $this->reference_number,
                'invoice_id' => $this->invoice->id,
                'recorded_by' => auth()->id(),
                'status' => $this->payment_status,
                'payment_receipt' => $receipt,
                'verified_at' => $this->payment_status === PaymentStatusEnum::PAID ? now() : null,
                'verified_by' => $this->payment_status === PaymentStatusEnum::PAID ? auth()->id() : null,
            ];

            // Use enhanced payment service to ensure invoice synchronization
            $this->enhancedPaymentService->createPayment($paymentData);

            $this->alert('success', __('Payment recorded successfully with enhanced invoice synchronization!'));
            $this->emit('refreshTable');
            $this->dispatchBrowserEvent('file-pond-clear', ['id' => $this->id]);

            $this->reset(['amount', 'paid_at', 'payment_method', 'reference_number', 'recorded_by', 'receipt']);

        } catch (\Exception $e) {
            $this->alert('error', __('Error recording payment: ') . $e->getMessage());
        }
    }

}
