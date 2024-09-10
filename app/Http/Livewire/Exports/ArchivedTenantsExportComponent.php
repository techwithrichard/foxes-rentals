<?php

namespace App\Http\Livewire\Exports;

use App\Exports\ArchivedTenantsExport;
use App\Exports\TenantsExport;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ArchivedTenantsExportComponent extends Component
{
    public function render(): Factory|View|Application
    {
        return view('livewire.exports.archived-tenants-export-component');
    }

    public function submit(): BinaryFileResponse
    {
        return Excel::download(new ArchivedTenantsExport, 'archived_tenants.xlsx');

    }
}
