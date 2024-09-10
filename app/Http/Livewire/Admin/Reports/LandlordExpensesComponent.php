<?php

namespace App\Http\Livewire\Admin\Reports;

use App\Models\Expense;
use App\Models\User;
use PDF;
//use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LandlordExpensesComponent extends Component
{

    public $from_date, $to_date, $landlord_id, $landlord_name;
    public $landlords = [];
    public Collection $expenses;

    protected $rules = [
        'from_date' => 'required',
        'to_date' => 'required|after_or_equal:from_date',
        'landlord_id' => 'required',
    ];

    public function mount()
    {
        $this->expenses = collect();
        $this->landlords = User::role('landlord')->select('id', 'name')->get();

    }

    //updatedLandlordId
    public function updatedLandlordId()
    {
        //get the landlord from the collection
        $landlord = $this->landlords->where('id', $this->landlord_id)->first();
        //set the landlord name
        $this->landlord_name = $landlord->name ?? '';


    }

    public function render(): Factory|View|Application
    {
        return view('livewire.admin.reports.landlord-expenses-component');
    }

    public function fetchReport()
    {
        $this->validate();

        $this->expenses = Expense::query()
            ->with('category')
            ->where('landlord_id', $this->landlord_id)
            ->whereBetween('incurred_on', [$this->from_date, $this->to_date])
            ->get();


    }

    public function printReport(): StreamedResponse
    {
        $expenses = $this->expenses;
        $start_date = $this->from_date;
        $end_date = $this->to_date;
        $landlord_name = $this->landlord_name;
        $pdf = PDF::loadView('admin.reports.print.landlord_expenses_print',
            compact('expenses', 'start_date', 'end_date', 'landlord_name'));

        return \response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'landlord-expenses.pdf');
    }
}
