<?php

namespace App\Http\Livewire\Admin\Invoice;

use App\Enums\PaymentStatusEnum;
use App\Enums\ReconciliationStatusEnum;
use App\Events\InvoicePaidEvent;
use App\Models\C2bRequest;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use JetBrains\PhpStorm\NoReturn;
use Livewire\Component;
use SebastianBergmann\Type\MixedType;

class ReconcilePaymentComponent extends Component
{
    public $transactionId;

    public $transaction;

    public $selectedTenant;

    public $tenants;

    public $invoices = [];

    public $selectedInvoice;


    protected $rules = [
        'selectedTenant' => 'required',
        'selectedInvoice' => 'required',
    ];

    protected $messages = [
        'selectedTenant.required' => 'Please select a tenant',
        'selectedInvoice.required' => 'Please select an invoice',
    ];


    public function mount(): void
    {
        try {
            // Ensure we have the transaction data
            if (!$this->transaction) {
                $this->transaction = C2bRequest::findOrFail($this->transactionId);
            }

            $this->tenants = User::query()
                ->role('tenant')
                ->get()
                ->map(function ($tenant) {
                    return [
                        'value' => $tenant->id,
                        'label' => $tenant->name,
                        'description' => $tenant->email,
                    ];
                });

            //get tenant_id from lease whose lease_id is equal to the lease_id of the transaction
            if ($this->transaction && $this->transaction->BillRefNumber) {
                $lease = Lease::where('lease_id', $this->transaction->BillRefNumber)->first();
                $this->selectedTenant = $lease?->tenant_id;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error loading transaction data: ' . $e->getMessage());
        }
    }

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.admin.invoice.reconcile-payment-component');
    }

    //when selected tenant changes
    public function updatedSelectedTenant(): void
    {

        $unpaidEnums = [PaymentStatusEnum::PENDING, PaymentStatusEnum::PARTIALLY_PAID, PaymentStatusEnum::OVERDUE];

        $this->invoices = Invoice::query()
            ->with(['property:id,name', 'house:id,name'])
            ->where('tenant_id', $this->selectedTenant)
            ->whereIn('status', $unpaidEnums)
            ->get();

        $this->reset('selectedInvoice');
    }

    public function commitReconciliation()
    {
        $this->validate();


        //get the invoice details from DB
        $invoice = Invoice::findOrFail($this->selectedInvoice);
        //get transaction
        $transaction = C2bRequest::findOrFail($this->transactionId);


        \DB::beginTransaction();

        try {
            //Create payment entry
            Payment::create([
                'amount' => $this->transaction->TransAmount,
                'paid_at' => $this->transaction->created_at,
                'payment_method' => 'MPESA C2B',
                'reference_number' => $this->transaction->TransID,
                'tenant_id' => $invoice->tenant_id,
                'invoice_id' => $invoice->id,
                'recorded_by' => auth()->id(),
                'landlord_id' => $invoice->landlord_id,
                'commission' => $invoice->commission,
                'property_id' => $invoice->property_id,
                'house_id' => $invoice->house_id,
                'status' => PaymentStatusEnum::PAID,

            ]);

            //reconcile payment with invoice
            $invoice->pay($this->transaction->TransAmount);
            InvoicePaidEvent::dispatch($invoice);

            //mark transaction as reconciled

            $transaction->reconciliation_status = ReconciliationStatusEnum::RECONCILED->value;
            $transaction->save();

            \DB::commit();
        } catch (\Exception $exception) {
            \DB::rollBack();
            session()->flash('error', $exception->getMessage());
        }

        return redirect()->route('admin.mpesa-c2b-transactions')
            ->with('success', 'Transaction reconciled successfully');


    }

    public function ignoreReconciliation()
    {
        $transaction = C2bRequest::findOrFail($this->transactionId);
        $transaction->reconciliation_status = ReconciliationStatusEnum::IGNORED->value;
        $transaction->save();


        return redirect()->route('admin.mpesa-c2b-transactions')
            ->with('success', 'Transaction ignored successfully');

    }
}
