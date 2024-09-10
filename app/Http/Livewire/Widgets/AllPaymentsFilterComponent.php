<?php

namespace App\Http\Livewire\Widgets;

use App\Exports\AllPaymentsExport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AllPaymentsFilterComponent extends Component
{

    public $paymentStatus;
    public $from_date;
    public $to_date;

    public function render(): View|\Illuminate\Foundation\Application|Factory|Application
    {
        return view('livewire.widgets.all-payments-filter-component');
    }

    public function exportPayments(): BinaryFileResponse
    {
        return Excel::download(new AllPaymentsExport($this->paymentStatus, $this->from_date, $this->to_date), 'payments.xlsx');
    }

}
