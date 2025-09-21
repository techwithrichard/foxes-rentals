<?php

namespace App\View\Components\Tenant;

use App\Models\Lease;
use Illuminate\View\Component;

class LeaseInfoWidget extends Component
{
    public $leaseInfo;
    public $hasActiveLease;

    public function __construct($leaseInfo = null)
    {
        $user = auth()->user();
        
        $this->leaseInfo = $leaseInfo ?? $user->leases()->with(['property', 'house'])->latest()->first();
        $this->hasActiveLease = $this->leaseInfo && $this->leaseInfo->status === 'active';
    }

    public function render()
    {
        return view('components.tenant.lease-info-widget');
    }
}
