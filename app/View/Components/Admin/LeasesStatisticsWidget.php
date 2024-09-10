<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class LeasesStatisticsWidget extends Component
{

    public int $total_leases_count;
    public int $total_trashed_leases_count;
    public int $total_active_leases_count;

    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {

        $this->total_trashed_leases_count = \App\Models\Lease::onlyTrashed()->count();
        $this->total_active_leases_count = \App\Models\Lease::count();
        $this->total_leases_count = $this->total_trashed_leases_count + $this->total_active_leases_count;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.admin.leases-statistics-widget');
    }
}
