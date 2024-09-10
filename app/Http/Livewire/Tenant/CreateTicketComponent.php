<?php

namespace App\Http\Livewire\Tenant;

use App\Models\Lease;
use App\Models\SupportTicket;
use App\Models\TicketCount;
use App\Notifications\DynamicAdminsNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateTicketComponent extends Component
{

    use WithFileUploads;

    public $subject, $description;
    public $attachments = [];

    public $leased_properties_and_units = [];

    public $activeUnit;

    public $activeUnitId;
    public $activePropertyId;


    protected $rules = [
        'subject' => 'required',
        'description' => 'required',
        'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',

    ];

    public function mount()
    {
        //from the auth user, get the tenant and get the leased properties and units
        $activeLeases = Lease::with(['property', 'house'])
            ->where('tenant_id', auth()->user()->id)
            ->get();

        foreach ($activeLeases as $lease) {
            //if lease->house_id is not null, then the lease is for a unit, else it is for a property
            if ($lease->house_id) {
                $this->leased_properties_and_units[] = [
                    'id' => $lease->house_id,
                    'name' => $lease->property->name . ' - ' . $lease->house->name,
                    'is_unit' => true
                ];
            } else {
                $this->leased_properties_and_units[] = [
                    'id' => $lease->property_id,
                    'name' => $lease->property->name,
                    'is_unit' => false
                ];
            }

        }

    }


    public function render()
    {
        return view('livewire.tenant.create-ticket-component');
    }

    //when activeUnit is updated, this method is called
    public function updatedActiveUnit($value)
    {
        if ($value == null || $value == '') {
            $this->resetActiveIds();
            return;
        }

        $item = $this->leased_properties_and_units[$value];
        $id = $item['id'];
        $isUnit = $item['is_unit'];

        if ($isUnit) {
            $this->activeUnitId = $id;
        } else {
            $this->activePropertyId = $id;
        }

    }

    private function resetActiveIds()
    {
        $this->activeUnitId = null;
        $this->activePropertyId = null;
    }

    public function submitTicket()
    {
        $this->validate();

        $attachment_names = [];
        if (isset($this->attachments)) {
            foreach ($this->attachments as $attachment) {
                $path = Storage::url(Storage::putFile('public/attachments', $attachment));
                array_push($attachment_names, ['name' => $attachment->getClientOriginalName(), 'path' => $path]);
            }
        }

        DB::transaction(function () {
            $ticket = SupportTicket::create([
                'ticket_id' => $this->generateNextTicketId(),
                'subject' => $this->subject,
                'message' => $this->description,
                'user_id' => auth()->id(),
                'property_id' => $this->activePropertyId,
                'house_id' => $this->activeUnitId
            ]);

            if (isset($attachment_names)) {
                $ticket->attachments()->createMany($attachment_names);
            }

            $tickets_count = TicketCount::where('day', now()->format('Y-m-d'))->first();
            if ($tickets_count) {
                $tickets_count->increment('count');
            } else {
                TicketCount::create([
                    'day' => now()->format('Y-m-d'),
                    'count' => 1
                ]);
            }

            $details = [
                'title' => __('New Ticket'),
                'message' => __('You have a new ticket from ') . auth()->user()->name,
            ];

            //send notification to admins
            DynamicAdminsNotification::sendNotification($details);

        });

        return redirect()->route('tenant.support-tickets.index')
            ->with('success', __('Ticket created successfully'));


    }

    private function generateNextTicketId(): int
    {
        $ticketCount = SupportTicket::count();
        return $ticketCount + 1;
    }


}
