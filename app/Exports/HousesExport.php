<?php

namespace App\Exports;

use App\Models\House;
use App\Models\User;
use Illuminate\Support\Collection;
use JetBrains\PhpStorm\ArrayShape;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class HousesExport implements
    FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{
    public function collection(): Collection
    {
        return House::with('property:id,name', 'property.address', 'landlord:id,name')->get();
    }

    public function headings(): array
    {
        return [
            __('Unit Name'),
            __('Building Name'),
            __('House Type'),
            __('Status'),
            __('Landlord'),
            __('Address'),
            __('City'),
            __('State'),
            __('Rent'),
            __('Agent Commission'),

        ];
    }

    public function map($house): array
    {
        return [
            $house->name,
            $house->property->name,
            $house->type,
            $house->house_status,
            $house->landlord->name,
            $house->property->address->address,
            $house->property->address->city,
            $house->property->address->state,
            $house->rent,
            $house->commission / 100,
        ];
    }

    public function columnFormats(): array
    {
        return [

            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_PERCENTAGE_00,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
