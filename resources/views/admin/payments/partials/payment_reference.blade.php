@if($payment->reference_number && $payment->payment_receipt)
    <a href="{{ url($payment->payment_receipt) }}" target="_blank" download>
        <em class="icon ni ni-download"></em>
        {{ $payment->reference_number }}
    </a>
@elseif($payment->reference_number)
    {{ $payment->reference_number }}
@elseif($payment->payment_receipt)
    <a href="{{ url($payment->payment_receipt) }}" target="_blank" download>
        <em class="icon ni ni-download"></em>
        {{ __('Download Receipt') }}
    </a>
@endif
