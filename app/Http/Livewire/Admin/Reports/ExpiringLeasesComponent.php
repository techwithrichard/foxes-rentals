<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Lease;
use Livewire\Component;
use Livewire\WithPagination;

class ExpiringLeasesComponent extends Component
{
    use WithPagination;

    //use bootstrap pagination
    protected $paginationTheme = 'bootstrap';




    public function render()
    {
        $leases = Lease::query()
            ->with('tenant:id,name', 'property:id,name', 'house:id,name')
            ->select('leases.*')
            ->withSum('bills', 'amount')
            ->where('end_date', '<=', now()->addDays(60))
            ->latest('id')->simplePaginate(10);
        return view('livewire.admin.reports.expiring-leases-component',compact('leases'));
    }
}
