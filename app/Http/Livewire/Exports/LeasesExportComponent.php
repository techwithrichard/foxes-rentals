<?php

namespace App\Http\Livewire\Exports;

use App\Exports\LeasesExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LeasesExportComponent extends Component
{
    public function render()
    {
        return view('livewire.exports.leases-export-component');
    }

    public function submit(): BinaryFileResponse
    {
        return Excel::download(new LeasesExport, 'leases.xlsx');
    }
}
