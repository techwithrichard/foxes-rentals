<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Expense;
//use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use PDF;
use Illuminate\Support\Collection;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CompanyExpensesComponent extends Component
{

    public $from_date;
    public $to_date;

    public Collection $expenses;

    protected $rules = [
        'from_date' => 'required',
        'to_date' => 'required|after_or_equal:from_date',
    ];

    public function mount()
    {
        $this->expenses = collect();
    }

    public function render()
    {
        return view('livewire.admin.reports.company-expenses-component');
    }

    public function fetchExpenses()
    {
//        $this->validate();

        //Get all expenses with related category where landlord_id is null and incurred_on is between from_date and end_date.Group by category so that data can be displayed in blade, and sum the amount of each category.
        $this->expenses = Expense::query()
            ->with('category')
            ->whereNull('landlord_id')
            ->whereBetween('incurred_on', [$this->from_date, $this->to_date])
            ->get();



    }

    public function printReport(): StreamedResponse
    {
        $expenses = $this->expenses;
        $start_date = $this->from_date;
        $end_date = $this->to_date;
        $pdf = PDF::loadView('admin.reports.print.company-expenses_print', compact('expenses', 'start_date', 'end_date'));

        return \response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'company-expenses.pdf');
    }


}
