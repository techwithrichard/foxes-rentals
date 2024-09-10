<?php

namespace App\Http\Livewire\Admin\Lease;

use App\Models\Lease;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;

class UpdateLeaseComponent extends Component
{

    public $start_date, $end_date;
    public $termination_date_notice;
    public $rent_amount;
    public $rent_cycle;
    public $invoice_generation_day;
    public $bills = [];

    public $deposit, $deposit_paid_months;
    public $lease_id;


    public $leaseId;

    protected function rules(): array
    {
        return [
            'start_date' => 'required|date',
            'rent_amount' => 'required|numeric|gt:0',
            'deposit' => 'nullable|numeric|min:0',
            'lease_id' => ['nullable', Rule::unique('leases', 'lease_id')->ignore($this->leaseId)],
            'rent_cycle' => 'required|numeric|min:1|max:12',
            'invoice_generation_day' => 'required|numeric|min:1|max:28',
            'bills.*.name' => [Rule::requiredIf(count($this->bills) > 0), 'string'],
            'bills.*.amount' => [Rule::requiredIf(count($this->bills) > 0), 'numeric', 'gt:0'],

        ];
    }

    public function getMessages(): array
    {
        return [

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
        $lease = Lease::with('bills', 'deposit')->findOrFail($this->leaseId);
        $this->start_date = $lease->start_date->format('Y-m-d');
        $this->end_date = $lease->end_date?->format('Y-m-d');
        $this->rent_amount = $lease->rent;
        $this->bills = $lease->bills->toArray();
        $this->deposit = $lease->deposit->amount ?? null;
        $this->deposit_paid_months = $lease->deposit->duration_in_months ?? null;
        $this->rent_cycle = $lease->rent_cycle;
        $this->lease_id = $lease->lease_id;
        $this->invoice_generation_day = $lease->invoice_generation_day;
    }

    public function render()
    {
        return view('livewire.admin.lease.update-lease-component');
    }

    public function addBill()
    {
        $this->bills[] = [
            'name' => '',
            'amount' => '',
        ];

    }

    public function removeBill($index)
    {
//        unset($this->bills[$index]);
        unset($this->bills[$index]);
        $this->bills = array_values($this->bills);
    }

    public function submit()
    {
        $this->validate();
        $lease = Lease::with('bills', 'deposit')->findOrFail($this->leaseId);

        DB::beginTransaction();

        try {

            $lease->start_date = $this->start_date;
            $lease->end_date = $this->end_date;
            $lease->rent_cycle = $this->rent_cycle;
            $lease->invoice_generation_day = $this->invoice_generation_day;
            $lease->rent = $this->rent_amount;
            $lease->lease_id = $this->lease_id;
            $lease->save();
            $lease->bills()->delete();
            foreach ($this->bills as $bill) {
                $lease->bills()->create([
                    'name' => $bill['name'],
                    'amount' => $bill['amount'],
                ]);
            }

            //if $this->deposit is null,do nothing,else update or create lease bill
            if ($this->deposit) {
                $lease->deposit()->updateOrCreate(
                    ['lease_id' => $lease->id],
                    [
                        'amount' => $this->deposit,
                        'tenant_id' => $lease->tenant_id,
                    ]
                );
            }


            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            throw $e;
        }

        return redirect()->route('admin.leases.index')
            ->with('success', __('Lease updated successfully.'));


    }
}
