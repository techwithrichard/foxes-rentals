<?php

namespace App\View\Components\Admin;

use Illuminate\View\Component;

class LatestSupportTicketsWidget extends Component
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
        $latestSupportTickets = \App\Models\SupportTicket::query()
            ->with('user:id,name')
            ->latest()
            ->take(3)
            ->get();
        return view('components.admin.latest-support-tickets-widget', compact('latestSupportTickets'));
    }
}
