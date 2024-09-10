@switch($proof->status)
    @case(\App\Enums\PaymentProofStatusEnum::PENDING)
    <span class="badge  bg-warning">{{ __('Pending')}}</span>
    @break
    @case(\App\Enums\PaymentProofStatusEnum::APPROVED)
    <span class="badge  bg-success">{{ __('Approved')}}</span>
    @break
    @case(\App\Enums\PaymentProofStatusEnum::REJECTED)
    <span class="badge  bg-danger">{{ __('Rejected')}}</span>
    @break
    @default
    <span class="badge bg-warning">{{ __('Pending')}}</span>

@endswitch
