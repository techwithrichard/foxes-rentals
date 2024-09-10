@if($property->is_multi_unit==false)
    <p class="mt-0 mb-0 sub-text-sm">
        <strong>{{ __('Rent')}}:</strong> {{setting('currency_symbol')}} {{ number_format($property->rent,2) }}
    </p>

    <p class="mt-0 mb-0 sub-text-sm">
        <strong>{{ __('Commission')}}:</strong> {{ number_format($property->commission,2) }} %
    </p>

@else
    <p class="mt-0 mb-0 sub-text-sm">
        <strong>{{ __('Total Units')}}: {{ $property->houses_count }}</strong>
    </p>

@endif

