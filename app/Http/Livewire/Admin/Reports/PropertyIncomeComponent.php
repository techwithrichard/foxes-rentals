<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Expense;
use App\Models\Payment;
use App\Models\Property;
use Livewire\Component;

class PropertyIncomeComponent extends Component
{
    public $property;
    public $month_year;
    public $payments = [];
    public $expenses = [];

    protected $rules = [
        'property' => 'required',
        'month_year' => 'required',
    ];


    public function render()
    {
        $properties = Property::select('id', 'name')->get();
        return view('livewire.admin.reports.property-income-component', compact('properties'));
    }


    public function submit()
    {
        $this->validate();

        //get month and year from $month_year in yyyy-mm separated by -
        $month = explode('-', $this->month_year)[1];
        $year = explode('-', $this->month_year)[0];

        //get payments for the selected month and year and landlord
        $this->payments = Payment::query()
            ->with('house:id,name')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('property_id', $this->property)
            ->get();

        //get expenses for the selected month and year and landlord
        $this->expenses = Expense::query()
            ->with('house:id,name', 'category')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->where('property_id', $this->property)
            ->get();

    }
}
