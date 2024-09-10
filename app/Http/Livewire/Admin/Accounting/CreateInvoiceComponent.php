<?php

namespace App\Http\Livewire\Admin\Accounting;

use App\Models\CustomInvoice;
use App\Models\House;
use App\Models\Property;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateInvoiceComponent extends Component
{


    public $dueDate, $invoice_date;
    public $landlord, $property, $unit;
    public $items = [];
    public $properties = [];
    public $units = [];
    public float $totalAmount = 0.0;
    public $invoiceNotes;
    public $companyDetails;

    protected $rules = [
        'items.*.description' => 'required',
        'items.*.amount' => 'required|numeric',
        'landlord' => 'required',
        'invoice_date' => 'required|date',
    ];

    //messages
    protected function getMessages()
    {
        return [
            'items.*.description.required' => __('Description is required'),
            'items.*.amount.required' => __('Amount is required'),
            'items.*.amount.numeric' => __('Amount must be numeric'),
        ];
    }

    public function mount()
    {

        $this->dueDate = date('Y-m-d');
        $this->invoice_date = date('Y-m-d');

        $this->items = [
            [
                'description' => 'Item Description',
                'quantity' => '1 Item',
                'amount' => 0,
            ]
        ];

        $this->companyDetails = 'Company Name \\n Company Address \\n Company City \\n Company State \\n Company Zip \\n Company Phone \\n Company Email';

    }


    public function render()
    {
        $landlords = User::query()
            ->role('landlord')
            ->select('id', 'name', 'email')->get();
        return view('livewire.admin.accounting.create-invoice-component', compact('landlords'));
    }

    public function updatedItems()
    {

        $this->totalAmount = 0;
        foreach ($this->items as $item) {
            $amount = is_numeric($item['amount']) ? $item['amount'] : 0;
            $this->totalAmount += $amount;
        }
    }

    public function updatedLandlord()
    {
        //if landlord is not null,get properties for landlord
        if ($this->landlord) {
            $this->properties = Property::query()
                ->where('landlord_id', $this->landlord)
                ->select('id', 'name')
                ->get();
        }
        //reset units
        $this->units = [];
    }

    public function updatedProperty()
    {
        $this->units = House::where('property_id', $this->property)->select('id', 'name')->get();

    }

    public function addItem()
    {
        $this->items[] = [
            'description' => 'Item Description',
            'quantity' => '1',
            'amount' => 0,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        //update total amount
        $this->updatedItems();
    }

    public function saveInvoice()
    {
        $this->validate();
        DB::beginTransaction();

        try {
            $invoice = CustomInvoice::create([
                'notes' => $this->invoiceNotes,
                'invoice_id' => $this->generateNextInvoiceId(),
                'due_date' => $this->dueDate,
                'invoice_date' => $this->invoice_date,
                'landlord_id' => $this->landlord,
                'property_id' => $this->property,
                'house_id' => $this->unit,
            ]);

            //save invoice items
            foreach ($this->items as $item) {
                $invoice->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'cost' => $item['amount'],
                ]);
            }

            DB::commit();

            $data = [
                'amount' => $this->totalAmount,
                'date' => $this->invoice_date,
            ];

            $landlord = User::find($this->landlord);
            $landlord->notify(new \App\Notifications\LandlordInvoiceCreatedNotification($data));
            return redirect()->route('admin.custom-invoice.index')->with('success', __('Custom landlord invoice created successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return null;
        }


    }

    private function generateNextInvoiceId(): int
    {
        $ticketCount = CustomInvoice::count();
        return $ticketCount + 1;
    }

}


