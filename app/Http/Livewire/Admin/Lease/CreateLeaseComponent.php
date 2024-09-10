<?php

namespace App\Http\Livewire\Admin\Lease;

use App\Enums\HouseStatusEnum;
use App\Enums\InvoicableTypeEnum;
use App\Enums\PaymentStatusEnum;
use App\Enums\PropertyStatusEnum;
use App\Models\House;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\Property;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\RequiredIf;
use Livewire\Component;
use Livewire\WithFileUploads;

class CreateLeaseComponent extends Component
{
    use WithFileUploads;

    public $tenant_id;
    public $property_id, $house_id;
    public bool $is_property_multi_unit = false;
    public bool $shouldGenerateInvoice = false;
    public $invoice_generation_day;
    public $next_billing_month_year;
    public $start_date, $end_date;
    public $rent_cycle;
    public $rent_amount;
    public $bills = [];
    public $houses = [];
    public $deposit;
    public $lease_id;
    public $lease_documents = [];

    protected function rules(): array
    {
        return [
            'tenant_id' => 'required',
            'property_id' => 'required',
            'house_id' => 'required_if:is_property_multi_unit,true',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after:start_date',
            'rent_amount' => 'required|numeric|min:1',
            'rent_cycle' => 'required|numeric|min:1|max:12',
            'deposit' => 'nullable|numeric|gt:0',
            'lease_id' => 'nullable|unique:leases,lease_id',
            'invoice_generation_day' => 'required|numeric|min:1|max:28',
            'bills.*.name' => [Rule::requiredIf(count($this->bills) > 0), 'string'],
            'bills.*.amount' => [Rule::requiredIf(count($this->bills) > 0), 'numeric', 'min:0'],

        ];
    }

    //messages
    public function getMessages(): array
    {
        return [
            'tenant_id.required' => __('Tenant is required'),
            'property_id.required' => __('Property is required'),
            'house_id.required_if' => __('House is required'),
            'start_date.required' => __('Start date is required'),
            'start_date.date' => __('Start date must be a date'),
            'rent_amount.required' => __('Rent amount is required'),
            'rent_amount.numeric' => __('Rent amount must be a number'),
            'rent_amount.min' => __('Rent amount must be greater than 0'),
            'bills.*.name.required_if' => __('Bill name is required'),
            'bills.*.name.string' => __('Bill name must be a string'),
            'bills.*.amount.required_if' => __('Bill amount is required'),
            'bills.*.amount.numeric' => __('Bill amount must be a number'),
            'bills.*.amount.min' => __('Bill amount must be greater than 0'),
        ];
    }

    public function mount()
    {
        $this->rent_cycle = 1;
    }

    public function render(): Factory|View|Application
    {
        $tenants = User::role('tenant')->select('id', 'name', 'email')->get();
        $properties = Property::where('is_multi_unit', true)
            ->orWhere(function ($query) {
                $query->where('is_multi_unit', false)
                    ->where('is_vacant', true);
            })->get();
        return view('livewire.admin.lease.create-lease-component', compact('tenants', 'properties'));
    }

    public function updatedPropertyId($value): void
    {

        if (!empty($value)) {

            $property = Property::with(['houses' => function ($query) {
                $query->where('is_vacant', true);
            }])->find($value);
            $this->is_property_multi_unit = $property->is_multi_unit;
            $this->rent_amount = $property->rent;
            $this->houses = $property->houses;

        } else {
            $this->is_property_multi_unit = false;
            $this->rent_amount = 0;
            $this->houses = [];
        }


    }

    public function updatedHouseId($value): void
    {

        if (!empty($value)) {
            $house = House::find($value);
            $this->rent_amount = $house->rent;
        } else {
            $this->rent_amount = 0;
        }

    }

    public function addBill(): void
    {
        $this->bills[] = [
            'name' => '',
            'amount' => '',
        ];
    }

    public function removeBill($index): void
    {
        unset($this->bills[$index]);
        $this->bills = array_values($this->bills);
    }

    public function submit()
    {
        $this->validate();


        if ($this->nextBillingDate()->isPast()) {
            $this->addError('next_billing_month_year', __('Next billing date must be in the future.Adjust the billing start month or the start date.'));

            return null;
        }


        //if lease_documents is not empty, upload the files,and get original file name and file path
        $uploaded_documents = [];
        if (!empty($this->lease_documents)) {

            foreach ($this->lease_documents as $document) {
                $uploaded_documents[] = [
                    'name' => $document->getClientOriginalName(),
                    'path' => Storage::url(Storage::putFile('public/documents', $document))
                ];
            }
        }

        DB::beginTransaction();

        try {

            $lease = Lease::create([
                'tenant_id' => $this->tenant_id,
                'property_id' => $this->property_id,
                'house_id' => $this->house_id,
                'start_date' => $this->start_date,
                'end_date' => $this->end_date,
                'rent' => $this->rent_amount,
                'rent_cycle' => $this->rent_cycle,
                'lease_id' => $this->lease_id ?? Str::random(10),
                'invoice_generation_day' => $this->invoice_generation_day,
                'next_billing_date' => $this->nextBillingDate(),
            ]);

            foreach ($this->bills as $bill) {
                $lease->bills()->create([
                    'name' => $bill['name'],
                    'amount' => $bill['amount'],
                ]);
            }

            $lease->documents()->createMany($uploaded_documents);

            //if deposit,create lease deposit
            if (!empty($this->deposit)) {
                $lease->deposit()->create([
                    'amount' => $this->deposit,
                    'tenant_id' => $this->tenant_id,
                ]);
            }

            //set property or house to not vacant
            $this->updateVacantStatus();


            if ($this->shouldGenerateInvoice) {
                $this->generateLeaseInvoices($lease->lease_id);
            }

            DB::commit();

            session()->flash('success', 'Lease created successfully');
            return redirect()->route('admin.leases.index')->with('success', __('Lease created successfully'));

        } catch (\Exception $e) {
            DB::rollBack();
//            dd($e);
            session()->flash('error', $e->getMessage());
            return redirect()->back();
        }


    }

    protected function generateLeaseInvoices($lease_id): void
    {
        $bills_amount = collect($this->bills)->sum('amount');


        Invoice::create([
            'amount' => $this->rent_amount,
            'bills_amount' => $bills_amount,
            'tenant_id' => $this->tenant_id,
            'property_id' => $this->property_id,
            'house_id' => $this->house_id,
            'status' => PaymentStatusEnum::PENDING->value,
            'bills' => $this->bills,
            'lease_reference' => $lease_id,
        ]);

    }

    protected function updateVacantStatus(): void
    {
        if ($this->is_property_multi_unit) {
            $house = House::find($this->house_id);
            $house->is_vacant = false;
            $house->status = HouseStatusEnum::OCCUPIED->value;
            $house->save();
        } else {
            $property = Property::find($this->property_id);
            $property->is_vacant = false;
            $property->status = PropertyStatusEnum::OCCUPIED->value;
            $property->save();
        }

    }

    protected function nextBillingDate(): Carbon
    {
        if ($this->next_billing_month_year) {
            //billing month is in format 2021-01
            $month_year = explode('-', $this->next_billing_month_year);
            return Carbon::create($month_year[0], $month_year[1], $this->invoice_generation_day);

        } else {
            return Carbon::parse($this->start_date)
                ->startOfMonth()
                ->addMonthsWithNoOverflow($this->rent_cycle)
                ->day($this->invoice_generation_day);
        }
    }


}
