<?php

namespace App\Http\Livewire\Exports;

use App\Exports\LandlordsExport;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class LandlordsExportComponent extends Component
{
    public function render()
    {
        return view('livewire.exports.landlords-export-component');
    }

    public function submit(): BinaryFileResponse
    {
        return Excel::download(new LandlordsExport, 'landlords.xlsx');
    }
}
