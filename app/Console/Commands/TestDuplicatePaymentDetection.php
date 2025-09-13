<?php

namespace App\Console\Commands;

use App\Services\PaymentReconciliationService;
use Illuminate\Console\Command;

class TestDuplicatePaymentDetection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:duplicate-payment {phone} {amount}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test overpayment detection for a given phone number and amount';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->argument('phone');
        $amount = $this->argument('amount');

        $this->info("Testing balance tracking for phone: {$phone}, amount: {$amount}");

        $reconciliationService = new PaymentReconciliationService();
        $result = $reconciliationService->testDuplicatePaymentDetection($phone, $amount);

        if ($result['is_duplicate']) {
            $this->warn("OVERPAYMENT DETECTED!");
            $this->info("Invoice ID: " . $result['invoice']->invoice_id);
            $this->info("Total invoice amount: " . $result['total_invoice_amount']);
            $this->info("Already paid: " . $result['paid_amount']);
            $this->info("Remaining balance: " . $result['remaining_balance']);
            $this->info("Payment amount: " . $amount);
            $this->info("Overpayment amount: " . $result['overpayment_amount']);
            $this->info("Payment would be split: {$result['remaining_balance']} for invoice, {$result['overpayment_amount']} as overpayment");
        } elseif (isset($result['exact_match']) && $result['exact_match']) {
            $this->info("EXACT PAYMENT MATCH!");
            $this->info("Invoice ID: " . $result['invoice']->invoice_id);
            $this->info("Total invoice amount: " . $result['total_invoice_amount']);
            $this->info("Remaining balance: " . $result['remaining_balance']);
            $this->info("Payment amount: " . $amount);
            $this->info("This payment would complete the invoice exactly.");
        } elseif (isset($result['partial_payment']) && $result['partial_payment']) {
            $this->info("PARTIAL PAYMENT DETECTED!");
            $this->info("Invoice ID: " . $result['invoice']->invoice_id);
            $this->info("Total invoice amount: " . $result['total_invoice_amount']);
            $this->info("Remaining balance: " . $result['remaining_balance']);
            $this->info("Payment amount: " . $amount);
            $this->info("This payment would reduce the balance to: " . ($result['remaining_balance'] - $amount));
        } else {
            $this->info("No matching invoice found - payment would be recorded as overpayment.");
        }

        return 0;
    }
}
