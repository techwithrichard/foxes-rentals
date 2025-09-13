<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\C2bRequest;
use App\Services\PaymentReconciliationService;
use App\Enums\PaymentStatusEnum;
use Illuminate\Console\Command;

class SimulatePaybillPayment extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate:paybill-payment {invoice-uuid} {amount} {--trans-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simulate a paybill payment for testing purposes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $invoiceUuid = $this->argument('invoice-uuid');
        $amount = (float) $this->argument('amount');
        $transId = $this->option('trans-id') ?? 'TEST' . time();
        
        $this->info("💳 Simulating Paybill Payment...");
        $this->newLine();

        $invoice = Invoice::with(['tenant'])->find($invoiceUuid);
        
        if (!$invoice) {
            $this->error("❌ Invoice not found: {$invoiceUuid}");
            return;
        }

        $this->info("📋 Before Payment:");
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->newLine();

        // Create C2B request data
        $c2bData = [
            'TransID' => $transId,
            'TransAmount' => $amount,
            'MSISDN' => $invoice->tenant->phone,
            'BillRefNumber' => $invoice->getAccountNumber(),
            'TransTime' => now()->format('YmdHis'),
            'BusinessShortCode' => config('mpesa.paybill'),
            'TransType' => 'Pay Bill',
            'ThirdPartyTransID' => '',
            'OrgAccountBalance' => '0.00',
            'FirstName' => $invoice->tenant->name,
            'MiddleName' => '',
            'LastName' => ''
        ];

        $this->info("📱 C2B Data:");
        $this->info("  Transaction ID: {$transId}");
        $this->info("  Amount: Ksh " . number_format($amount, 2));
        $this->info("  Phone: {$invoice->tenant->phone}");
        $this->info("  Account Number: {$invoice->getAccountNumber()}");
        $this->newLine();

        // Create C2B request record
        $c2bRequest = C2bRequest::create($c2bData);
        $this->info("✅ C2B Request Created: {$c2bRequest->id}");
        $this->newLine();

        // Process the payment using PaymentReconciliationService
        $reconciliationService = new PaymentReconciliationService();
        $result = $reconciliationService->processC2bCallback($c2bData);

        $this->info("🔄 Reconciliation Result:");
        if ($result['success']) {
            $this->info("  ✅ Success: {$result['message']}");
        } else {
            $this->error("  ❌ Failed: {$result['message']}");
        }
        $this->newLine();

        // Refresh invoice to get updated data
        $invoice->refresh();

        $this->info("📋 After Payment:");
        $this->info("  Balance Due: Ksh " . number_format($invoice->balance_due, 2));
        $this->info("  Status: {$invoice->status->value}");
        $this->newLine();

        // Show payment history
        $this->info("💳 Payment History:");
        $payments = Payment::where('invoice_id', $invoice->id)->orderBy('created_at', 'desc')->get();
        foreach ($payments as $payment) {
            $status = is_object($payment->status) ? $payment->status->value : $payment->status;
            $this->info("  - Ksh " . number_format($payment->amount, 2) . 
                       " | {$status} | {$payment->created_at->format('Y-m-d H:i:s')}" .
                       " | {$payment->reference_number}");
        }
        $this->newLine();

        // Calculate next payment scenarios
        $this->info("🧮 Next Payment Scenarios:");
        $scenarios = [100, 200, 500, 1000];
        foreach ($scenarios as $scenarioAmount) {
            $newBalance = $invoice->balance_due - $scenarioAmount;
            if ($newBalance <= 0) {
                $overpayment = abs($newBalance);
                $this->info("  Payment Ksh " . number_format($scenarioAmount) . ": " . 
                           ($newBalance == 0 ? "Exact payment" : "Overpayment of Ksh " . number_format($overpayment)));
            } else {
                $this->info("  Payment Ksh " . number_format($scenarioAmount) . ": Remaining balance Ksh " . number_format($newBalance));
            }
        }
    }
}

