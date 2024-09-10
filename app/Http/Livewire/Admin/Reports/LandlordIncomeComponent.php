<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\User;
use Livewire\Component;

class LandlordIncomeComponent extends Component
{
    public $landlord;
    public $month_year;
    public $payments = [];
    public $expenses = [];

    public $landlord_name;

    protected $rules = [
        'landlord' => 'required',
        'month_year' => 'required',
    ];

    public function render()
    {
        $landlords = User::role('landlord')->select('id', 'name', 'email')->get();
        return view('livewire.admin.reports.landlord-income-component', compact('landlords'));
    }

    public function updatedLandlord()
    {
        $this->landlord_name = User::find($this->landlord)?->name;
        //clear the payments and expenses
        $this->payments = [];
        $this->expenses = [];
    }

    public function submit()
    {
        $this->validate();
        //get month and year from $month_year in yyyy-mm separated by -
        $month = explode('-', $this->month_year)[1];
        $year = explode('-', $this->month_year)[0];

        //get payments for the selected month and year and landlord
        $this->payments = Payment::query()
            ->with('property:id,name', 'house:id,name')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('landlord_id', $this->landlord)
            ->get();

        //get expenses for the selected month and year and landlord
        $this->expenses = Expense::query()
            ->with('property:id,name', 'house:id,name', 'category')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('landlord_id', $this->landlord)
            ->get();


    }
}
