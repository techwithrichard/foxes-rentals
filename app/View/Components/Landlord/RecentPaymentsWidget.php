<?php

namespace App\View\Components\Landlord;

use App\Models\Payment;
use Illuminate\View\Component;

class RecentPaymentsWidget extends Component
{
    public $payments;
    public $totalAmount;

    public function __construct($payments = null)
    {
        $user = auth()->user();
        
        $this->payments = $payments ?? $user->payments()->latest()->limit(5)->get();
        $this->totalAmount = $this->payments->sum('amount');
    }

    public function render()
    {
        return view('components.landlord.recent-payments-widget');
    }
}
