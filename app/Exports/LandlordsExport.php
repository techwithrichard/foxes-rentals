<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LandlordsExport implements
    FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{
    public function collection(): Collection
    {
        return $users = User::query()
            ->role('landlord')
            ->latest('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            __('ID'),
            __('Name'),
            __('Email'),
            __('Phone'),
            __('Address'),
            __('Next Of Kin'),
        ];
    }

    public function map($user): array
    {
        return [
            $user->identity_no ?? 'N/a',
            $user->name,
            $user->email,
            $user->phone,
            $user->address,
            $user->next_of_kin,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
