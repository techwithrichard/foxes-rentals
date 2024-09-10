<?php

namespace App\Listeners;

use App\Enums\PaymentStatusEnum;
use App\Events\InvoicePaidEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateInvoiceStatusListener
{

    public function __construct()
    {
        //
    }

    public function handle(InvoicePaidEvent $event): void
    {
        $invoice = $event->invoice;
        $invoice->fresh();
        $paidAmount = $invoice->verified_payments()->sum('amount');
        $totalPayable = $invoice->amount + $invoice->bills_amount;
        $unpaidAmount = $totalPayable - $paidAmount;


        switch (true) {
            case $unpaidAmount == 0:
                $invoice->update([
                    'status' => PaymentStatusEnum::PAID,
                ]);
                break;
            case $unpaidAmount > 0:
                $invoice->update([
                    'status' => PaymentStatusEnum::PARTIALLY_PAID,
                ]);
                break;
            case $unpaidAmount < 0:
                $invoice->update([
                    'status' => PaymentStatusEnum::OVER_PAID,
                ]);
                $overpaidAmount = abs($unpaidAmount);
                $tenant = $invoice->tenant;
                $overpayment = $tenant->overpayment;

                if ($overpayment) {
                    $overpayment->update(['amount' => $overpayment->amount + $overpaidAmount]);
                } else {
                    $tenant->overpayment()->create(['amount' => $overpaidAmount]);
                }
                break;
        }
    }
}
