<?php

namespace App\Http\Livewire\Admin\Accounting;

use App\Models\CustomInvoice;
use App\Models\Property;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateVoucherComponent extends Component
{

    public $voucher_date;
    public $voucher_type;
    public $landlord, $property, $notes;
    public $items = [];
    public $properties = [];
    public float $totalAmount = 0.0;

    protected $rules = [
        'items.*.description' => 'required',
        'items.*.amount' => 'required|numeric',
        'landlord' => 'required',
        'voucher_date' => 'required|date',
        'voucher_type' => 'required',
    ];

    //messages
    protected function getMessages(): array
    {
        return [
            'items.*.description.required' => __('Description is required'),
            'items.*.amount.required' => __('Amount is required'),
            'items.*.amount.numeric' => __('Amount must be numeric'),
        ];
    }

    public function mount()
    {

        $this->voucher_date = date('Y-m-d');

        $this->items = [
            [
                'description' => 'Item Description',
                'quantity' => '1',
                'amount' => 0,
            ]
        ];


    }


    public function render()
    {
        $landlords = User::query()
            ->role('landlord')
            ->select('id', 'name', 'email')->get();
        return view('livewire.admin.accounting.create-voucher-component', compact('landlords'));
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

    public function saveVoucher()
    {
        $this->validate();
        DB::beginTransaction();

        try {

            $voucher = Voucher::create([
                'type' => $this->voucher_type,
                'voucher_id' => $this->generateNextVoucherId(),
                'voucher_date' => $this->voucher_date,
                'notes' => $this->notes,
                'landlord_id' => $this->landlord,
                'property_id' => $this->property,
            ]);

            //save invoice items
            foreach ($this->items as $item) {
                $voucher->items()->create([
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'cost' => $item['amount'],
                ]);
            }

            DB::commit();

            $data = [
                'amount' => $this->totalAmount,
                'date' => $this->voucher_date,
            ];

            $landlord = User::find($this->landlord);
            $landlord->notify(new \App\Notifications\LandlordVoucherCreatedNotification($data));
            return redirect()->route('admin.vouchers.index')->with('success', __('Voucher has been created successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return null;
        }


    }

    private function generateNextVoucherId(): int
    {
        $ticketCount = Voucher::count();
        return $ticketCount + 1;
    }

}
