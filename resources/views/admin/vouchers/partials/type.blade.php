@switch($voucher->type)
    @case(\App\Enums\VoucherTypesEnum::RECEIPT->value)
    <span class="badge bg-primary">{{ __('RECEIPT')}}</span>
    @break

    @case(\App\Enums\VoucherTypesEnum::PAYMENT->value)
    <span class="badge bg-secondary">{{ __('PAYMENT')}}</span>
    @break

    <span class="badge badge-dot bg-info">{{ __('Refunded')}}</span>

@endswitch
