<?php

namespace App\Http\Livewire\Tenant;

use App\Models\Invoice;
use Livewire\Component;

class PaybillInstructionsComponent extends Component
{
    public $invoice;
    public $paybillNumber;
    public $accountNumber;
    public $amountToPay;
    public $reference;

    public function mount($invoiceId)
    {
        $this->invoice = Invoice::with(['tenant', 'property', 'house'])->findOrFail($invoiceId);
        
        $this->paybillNumber = config('mpesa.paybill');
        $this->accountNumber = $this->invoice->getAccountNumber(); // Use lease reference (e.g., CsBvzmgAmM)
        $this->amountToPay = ceil($this->invoice->balance_due);
        $this->reference = $this->invoice->lease_reference ?? 'Invoice-' . $this->invoice->invoice_id;
    }

    public function render()
    {
        return view('livewire.tenant.paybill-instructions-component');
    }

    public function copyToClipboard($text, $label)
    {
        $this->dispatchBrowserEvent('copy-to-clipboard', [
            'text' => $text,
            'label' => $label
        ]);
    }
}
