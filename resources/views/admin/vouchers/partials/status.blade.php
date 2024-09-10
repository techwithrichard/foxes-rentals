@switch($invoice->status)
    @case(\App\Enums\PaymentStatusEnum::PENDING->value)
    <span class="badge bg-warning">{{ __('Pending')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::PAID->value)
    <span class="badge bg-success">{{ __('Paid')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::OVERDUE->value)
    <span class="badge  bg-danger">{{ __('Overdue')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::PARTIALLY_PAID->value)
    <span class="badge bg-info">{{ __('Partially Paid')}}</span>
    @break

    @default
    <span class="badge badge-dot bg-info">{{ __('Refunded')}}</span>

@endswitch
