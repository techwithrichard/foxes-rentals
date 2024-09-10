@if($payout->payment_reference && $payout->payment_receipt)
    <a href="{{ url($payout->payment_receipt) }}">
        {{ $payout->payment_reference }}

    </a>

@elseif($payout->payment_reference)
    {{ $payout->payment_reference }}

@elseif($payout->payment_receipt)
    <a href="{{ url($payout->payment_receipt) }}" download>
        {{ __('View Receipt')}}
    </a>

@else
    <span class="">{{ __('N/a')}}</span>


@endif
