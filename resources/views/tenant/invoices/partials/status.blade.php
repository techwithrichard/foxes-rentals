@switch($invoice->status)
    @case(\App\Enums\PaymentStatusEnum::PENDING)
    <span class="badge bg-warning">{{ __('Pending')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::PAID)
    <span class="badge bg-success">{{ __('Paid')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::OVERDUE)
    <span class="badge  bg-danger">{{ __('Overdue')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::PARTIALLY_PAID)
    <span class="badge  bg-info">{{ __('Partially Paid')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::OVER_PAID)
    <span class="badge  bg-success">{{ __('Over Paid')}}</span>
    @break

    @default
    <span class="badge bg-info">{{ __('Default')}}</span>

@endswitch
