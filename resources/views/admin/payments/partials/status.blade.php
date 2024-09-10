@switch($payment->status)
    @case(\App\Enums\PaymentStatusEnum::PENDING->value)
    <span class="badge bg-warning">{{ __('Pending')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::PAID->value)
    <span class="badge bg-success">{{ __('Approved')}}</span>
    @break

    @case(\App\Enums\PaymentStatusEnum::CANCELLED->value)
    <span class="badge bg-danger">{{ __('Cancelled')}}</span>
    @break


    @default
    <span class="badge badge-dot bg-info">{{ __('Unknown')}}</span>

@endswitch
