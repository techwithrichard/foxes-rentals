<?php

namespace App\Http\Livewire\Admin\Ticket;

use App\Models\SupportTicket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Jantinnerezo\LivewireAlert\LivewireAlert;
use Livewire\Component;
use Livewire\WithFileUploads;

class ReplyTicketComponent extends Component
{

    use WithFileUploads;
    use LivewireAlert;

    public $ticketId;
    public $ticket_status;
    public User $ticket_owner;

    public $reply_message;
    public $attachments = [];

    protected $rules = [
        'reply_message' => 'required|string',
        'attachments.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10024',
    ];

    protected $messages = [
        'attachments.*.mimes' => 'Attachments must be a file of type: pdf, jpg, jpeg, png.',
        'attachments.*.max' => 'Attachment may not be greater than 10MB.',
    ];

    public function mount()
    {
        $ticket = SupportTicket::with('user')->findOrFail($this->ticketId);

        $this->ticket_status = $ticket->status;
        $this->ticket_owner = $ticket->user;
    }


    public function render()
    {
        return view('livewire.admin.ticket.reply-ticket-component');
    }

    public function submit()
    {
        $this->validate();

        $attachment_names = [];
        if (isset($this->attachments)) {


            foreach ($this->attachments as $attachment) {

                $path = Storage::url(Storage::putFile('public/attachments', $attachment));

                array_push($attachment_names, ['name' => $attachment->getClientOriginalName(), 'path' => $path]);


            }
        }

        DB::beginTransaction();
        try {
            $reply = TicketReply::create([
                'message' => $this->reply_message,
                'ticket_id' => $this->ticketId,
                'user_id' => auth()->id(),
            ]);
            if (isset($attachment_names)) {
                $reply->attachments()->createMany($attachment_names);
            }

            DB::commit();
        } catch (\Exception $e) {

            Log::error($e);
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return null;
        }

        session()->flash('success', __('Reply sent successfully'));

        return redirect()->route('admin.support-tickets.show', $this->ticketId);
    }
}
