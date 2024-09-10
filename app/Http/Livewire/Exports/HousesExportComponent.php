<?php

namespace App\Http\Livewire\Exports;

use App\Exports\HousesExport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class HousesExportComponent extends Component
{
    public function render(): Factory|View|Application
    {
        return view('livewire.exports.houses-export-component');
    }

    public function submit(): BinaryFileResponse
    {
        return Excel::download(new HousesExport, 'houses.xlsx');
    }
}
