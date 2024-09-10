<?php

namespace App\Exports;

use App\Enums\PaymentStatusEnum;
use App\Models\Payment;
use Illuminate\Support\Collection;
use LaravelIdea\Helper\App\Models\_IH_Payment_C;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AllPaymentsExport implements
    FromCollection, ShouldAutoSize, WithHeadings, WithMapping, WithStyles, WithColumnFormatting
{
    protected $paymentStatus;
    protected $from_date;
    protected $to_date;

    //constructor where propertyId and paymentStatus are passed and they can be null
    public function __construct($paymentStatus, $from_date, $to_date)
    {
        $this->paymentStatus = $paymentStatus;
        $this->from_date = $from_date;
        $this->to_date = $to_date;
    }


    public function collection(): \Illuminate\Database\Eloquent\Collection|array|Collection|_IH_Payment_C
    {
        return Payment::with(['tenant', 'invoice.property', 'invoice.house'])
            ->when($this->paymentStatus, function ($query) {
                return $query->where('status', $this->paymentStatus);
            })
            ->when(($this->from_date && $this->to_date), function ($query) {
                $query->whereBetween('paid_at', [$this->from_date, $this->to_date]);
            })
            ->
            get();


    }

    public function headings(): array
    {
        return [
            __('Tenant'),
            __('Payment date'),
            __('Amount'),
            __('Payment Method'),
            __('Payment Reference'),
            __('Building'),
            __('House'),
            __('Status'),

        ];
    }

    public function map($payment): array
    {
        return [
            $payment->tenant?->name ?? 'N/A',
            $payment->paid_at->format('d-m-Y'),
            $payment->amount,
            $payment->payment_method,
            $payment->payment_reference,
            $payment->invoice?->property?->name,
            $payment->invoice?->house?->name,
            $this->getPaymentStatus($payment->status),
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_DATE_DATETIME,
            'C' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }

    protected function getPaymentStatus($paymentStatus)
    {

        switch ($paymentStatus) {
            case PaymentStatusEnum::PENDING:
                return __('Pending');
            case PaymentStatusEnum::PAID->value:
                return __('Paid');
            case PaymentStatusEnum::PARTIALLY_PAID->value:
                return __('Partially Paid');
            case PaymentStatusEnum::CANCELLED->value:
                return __('Cancelled');
            case PaymentStatusEnum::OVER_PAID->value:
                return __('Over Paid');
            case PaymentStatusEnum::OVERDUE->value:
                return __('Overdue');
            default:
                return __('N/A');
        }

    }
}
