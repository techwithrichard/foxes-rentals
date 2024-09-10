<?php

namespace App\Http\Livewire\Widgets;

use Livewire\Component;

class TicketsBadgeWidget extends Component
{

    public function render()
    {
        $today = \Carbon\Carbon::today()->format('Y-m-d');
        $new_tickets = \App\Models\TicketCount::where('day', $today)->value('count', 0);
        //if there
        return view('livewire.widgets.tickets-badge-widget',compact('new_tickets'));
    }
}
