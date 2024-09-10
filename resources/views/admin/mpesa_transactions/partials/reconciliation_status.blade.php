@switch($transaction->reconciliation_status)
    @case(\App\Enums\ReconciliationStatusEnum::PENDING)
        <span class="badge bg-danger">{{ __('Pending')}}</span>
        @break

    @case(\App\Enums\ReconciliationStatusEnum::RECONCILED)
        <span class="badge bg-success">{{ __('Reconciled')}}</span>
        @break

    @case(\App\Enums\ReconciliationStatusEnum::IGNORED)
        <span class="badge bg-gray">{{ __('Ignored')}}</span>
        @break

    @default
        <span class="badge  bg-danger">{{ __('Pending')}}</span>

@endswitch

{{$transaction->reconciliation_status}}
