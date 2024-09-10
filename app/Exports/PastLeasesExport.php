<?php

namespace App\Exports;

use App\Models\Lease;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PastLeasesExport implements
    FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{
    public function collection(): Collection
    {
        return $leases = Lease::query()
            ->onlyTrashed()
            ->with('tenant:id,name', 'property:id,name', 'house:id,name')
            ->select('leases.*')
            ->withSum('bills', 'amount')
            ->latest('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            __('Tenant'),
            __('Building'),
            __('House'),
            __('Start Date'),
            __('End Date'),
            __('Termination Date'),
            __('Total Rent'),
            __('Total Bills'),
            __('Rent Cycle'),

        ];
    }

    public function map($lease): array
    {
        return [
            $lease->tenant->name,
            $lease->property->name,
            $lease->house->name,
            $lease->start_date,
            $lease->end_date,
            $lease->deleted_at,
            $lease->rent,
            $lease->bills_sum_amount,
            $lease->rent_cycle . ' ' . __($lease->rent_cycle > 1 ? 'months' : 'month'),
        ];
    }

    public function columnFormats(): array
    {
        return [

            'D' => NumberFormat::FORMAT_DATE_DATETIME,
            'E' => NumberFormat::FORMAT_DATE_DATETIME,
            'F' => NumberFormat::FORMAT_DATE_DATETIME,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
