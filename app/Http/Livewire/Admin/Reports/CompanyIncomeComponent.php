<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Expense;
use App\Models\Payment;
use Livewire\Component;

class CompanyIncomeComponent extends Component
{

    public $month_year;
    public $payments = [];
    public $expenses = [];

    protected $rules = [
        'month_year' => 'required',
    ];


    public function render()
    {
        return view('livewire.admin.reports.company-income-component');
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
            ->get();

        //get expenses for the selected month and year and landlord
        $this->expenses = Expense::query()
            ->with('property:id,name', 'house:id,name', 'category')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->whereNull('landlord_id')
            ->get();

    }
}
