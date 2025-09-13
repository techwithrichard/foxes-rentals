<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Models\Invoice;
use Livewire\Component;

class ModifyInvoiceComponent extends Component
{
    public $invoice_id;
    public Invoice $invoice;
    //Bills

    public $bill_description;
    public $bill_amount;

    public $current_bills = [];

    public function mount()
    {
        $this->invoice = Invoice::query()
            ->with('tenant:id,name,email,phone,address', 'property:id,name', 'house:id,name')
            ->findOrFail($this->invoice_id);

        $this->current_bills = $this->invoice->bills ?? [];

    }

    public function render()
    {
        return view('livewire.admin.invoice.modify-invoice-component');
    }

    public function addInvoiceBill()
    {
        $this->validate([
            'bill_description' => 'required|string',
            'bill_amount' => 'required|numeric|min:1',
        ]);

        // Use the new addBill method which automatically updates status
        $this->invoice->addBill($this->bill_description, $this->bill_amount);

        // Clear form fields
        $this->bill_description = '';
        $this->bill_amount = '';

        // Refresh the current bills
        $this->current_bills = $this->invoice->fresh()->bills ?? [];

        session()->flash('success', 'Bill added successfully! Invoice status updated.');
        
        return redirect()->route('admin.rent-invoice.edit', $this->invoice_id);
    }

    public function removeInvoiceBill($index)
    {
        $bill = $this->current_bills[$index];

        $this->invoice->update([
            'bills_amount' => $this->invoice->bills_amount - $bill['amount'],
        ]);

        unset($this->current_bills[$index]);

        $this->invoice->update([
            'bills' => $this->current_bills,
        ]);

        return redirect()->route('admin.rent-invoice.edit', $this->invoice_id);
    }

}
