<?php

namespace App\View\Components\Admin;

use Illuminate\Support\Facades\Cache;
use Illuminate\View\Component;

class OutstandingPaymentsWidget extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {

        // outstanding payments are from Invoices where status is either 'pending' or 'partially paid'
        // get the latest 10 of them and balance_due as (amount+bills_amount)-paid_amount


        $outstanding_payments = \App\Models\Invoice::query()
            ->with('tenant:id,name')
            ->whereIn('status', [
                \App\Enums\PaymentStatusEnum::PENDING,
                \App\Enums\PaymentStatusEnum::PARTIALLY_PAID
            ])
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get() ;

        // cache the results for 5 minutes, so we don't have to query the database every time.
        //after 5 minutes, the cache will be cleared and the query will be run again.
        $outstanding_payments = Cache::remember('outstanding_payments', 5, function () use ($outstanding_payments) {
            return $outstanding_payments;
        });

        return view('components.admin.outstanding-payments-widget', compact('outstanding_payments'));
    }
}
