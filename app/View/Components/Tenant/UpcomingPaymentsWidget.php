<?php

namespace App\View\Components\Tenant;

use App\Models\Payment;
use Illuminate\View\Component;

class UpcomingPaymentsWidget extends Component
{
    public $payments;
    public $totalAmount;

    public function __construct($payments = null)
    {
        $user = auth()->user();
        
        $this->payments = $payments ?? $user->payments()
            ->where('due_date', '>=', now())
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->get();
        $this->totalAmount = $this->payments->sum('amount');
    }

    public function render()
    {
        return view('components.tenant.upcoming-payments-widget');
    }
}
