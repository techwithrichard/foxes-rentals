<?php

namespace App\Http\Livewire\Exports;

use App\Exports\PastLeasesExport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PastLeasesExportComponent extends Component
{
    public function render(): Factory|View|Application
    {
        return view('livewire.exports.past-leases-export-component');
    }

    public function submit(): BinaryFileResponse
    {
        return Excel::download(new PastLeasesExport, 'past-leases.xlsx');

    }
}
