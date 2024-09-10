@switch($invoice->status)
    @case(\App\Enums\PaymentStatusEnum::PENDING)
    <span class="badge bg-danger">{{ __('Pending')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::PAID)
    <span class="badge bg-success">{{ __('Paid')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::OVERDUE)
    <span class="badge bg-danger">{{ __('Overdue')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::PARTIALLY_PAID)
    <span class="badge bg-warning">{{ __('Partially Paid')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::OVER_PAID)
    <span class="badge bg-blue">{{ __('Over Paid')}}</span>
    @break

    @default
    <span class="badge badge-dot bg-info">{{ __('Refunded')}}</span>

@endswitch
