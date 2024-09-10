<?php

namespace App\Http\Livewire\Admin\Ticket;

use App\Models\SupportTicket;
use Livewire\Component;

class UpdateTicketStatusComponent extends Component
{

    public $ticketId;
    public SupportTicket $ticket;

    public function render()
    {
        return view('livewire.admin.ticket.update-ticket-status-component');
    }

    public function updateStatus($status)
    {

        $this->ticket->status = $status;
        $this->ticket->save();

        //get tenant whose ticket is being updated
        $tenant = $this->ticket->user;

        $data = [
            'subject' => $this->ticket->subject??'Subject',
            'status' => $status??'Status',
        ];

        //send notification to tenant
        $tenant->notify(new \App\Notifications\TicketStatusUpdatedNotification($data));


        return redirect()->route('admin.support-tickets.show', $this->ticket)
            ->with('success', __('Ticket status updated successfully.'));

    }
}
