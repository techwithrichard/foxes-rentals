<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class TenantsExport implements
    FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles
{

    public function collection()
    {
        return $users = User::query()
            ->withoutTrashed()
            ->role('tenant')
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
            __('Occupation Status'),
            __('Occupation Place'),
            __('Next Of Kin'),
            __('Emergency Contact'),
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
            $user->occupation_status,
            $user->occupation_place,
            $user->next_of_kin,
            $user->emergency_contact,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }


}
