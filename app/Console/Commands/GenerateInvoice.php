<?php

namespace App\Console\Commands;

use App\Enums\PaymentStatusEnum;
use App\Models\Invoice;
use App\Models\Lease;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class GenerateInvoice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates an invoice for the current month and specified day on the lease.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        /**
         * Invoice Generation Steps
         * Step 1: Get all leases with tenant.overpayment, bills in chunks of 100
         * Step 2: For each lease, generate invoice
         * Step 3: Check if tenant has overpayment, if so, add to invoice
         * Step 4: Update overpayment to balance of overpayment
         * Step 5: Update invoice status to either partiallY or fully paid depending on amount+bills_amount - paid_amount
         * Step 6: Update new lease invoice generation date
         * Step 7: Notify tenant of invoice generation
         *
         */

        //info('Generating invoices for the current month and specified day on the lease.');
        $this->info('Generating invoices for the current month and specified day on the lease.');

        $currentDate = now();
        $leases = Lease::query()
            ->with('tenant.overpayment', 'bills')
            ->where('next_billing_date', $currentDate->format('Y-m-d'))
            ->chunkById(100, function ($leases) use ($currentDate) {
                foreach ($leases as $lease) {

                    $data = [];

                    DB::transaction(function () use ($lease, $currentDate, &$data) {
                        //lease_bills collect only name and amount

                        $invoice = Invoice::create([
                            'amount' => $lease->rent,
                            'bills_amount' => $lease->bills->sum('amount'),
                            'tenant_id' => $lease->tenant_id,
                            'property_id' => $lease->property_id,
                            'house_id' => $lease->house_id,
                            'lease_reference' => $lease->lease_id,
                            'status' => PaymentStatusEnum::PENDING->value,
                            'bills' => $lease->bills->map(function ($bill) {
                                return [
                                    'name' => $bill->name,
                                    'amount' => $bill->amount,
                                ];
                            }),
                        ]);

                        //total amount to be paid
                        $totalAmount = $invoice->amount + $invoice->bills_amount;
                        //check tenant overpayment,else default to 0
                        $overpayment = $lease->tenant->overpayment ? $lease->tenant->overpayment->amount : 0;
                        if ($overpayment > 0) {
                            //check if overpayment is greater than invoice total
                            if ($overpayment >= $totalAmount) {
                                $invoice->update([
                                    'paid_amount' => $totalAmount,
                                    'status' => \App\Enums\PaymentStatusEnum::PAID->value,
                                ]);
                                $lease->tenant->overpayment->update([
                                    'amount' => $overpayment - $totalAmount,
                                ]);
                            } else {
                                $invoice->update([
                                    'paid_amount' => $overpayment,
                                    'status' => \App\Enums\PaymentStatusEnum::PARTIALLY_PAID->value,
                                ]);
                                $lease->tenant->overpayment->update([
                                    'amount' => 0,
                                ]);
                            }
                        }

                        //update lease invoice generation date
                        $lease->update([
                            'next_billing_date' => now()
                                ->startOfMonth()
                                ->addMonthsWithNoOverflow($lease->rent_cycle)
                                ->day($lease->invoice_generation_day),
                        ]);

                        $data = array_merge($data, [
                            'invoice_id' => $invoice->invoice_id,
                            'invoice_amount' => $invoice->amount,
                        ]);


                    });

                    //Notify tenant of invoice generation,if transaction is successful
                    if (isset($data['invoice_id']) && isset($data['invoice_amount'])) {
                        $lease->tenant->notify(new \App\Notifications\NewInvoiceNotification($data));
                    }


                }
            });

        $this->info('Invoice generation completed successfully');

        return Command::SUCCESS;
    }
}
