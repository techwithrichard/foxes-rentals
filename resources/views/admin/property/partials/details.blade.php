@if($property->electricity_id)
    <p class="mt-0 mb-0 sub-text-sm">
        <strong>{{ __('Electricity ID')}}:</strong> {{ $property->electricity_id }}
    </p>
@endif


@if(!$property->is_multi_unit)
    <p class="mt-0 mb-0 sub-text-sm">
        <strong>{{ __('Rent')}}:</strong> {{setting('currency_symbol')}} {{ number_format($property->rent,2) }}
    </p>

{{--    <p class="mt-0 mb-0 sub-text-sm">--}}
{{--        <strong>{{ __('Commission')}}:</strong> {{ number_format($property->commission,2) }} %--}}
{{--    </p>--}}

    @if($property->lease?->tenant)
        <p class="mt-0 mb-0 sub-text-sm">
            <strong>{{ __('Tenant')}}:</strong> {{ $property->lease->tenant->name ?? 'N/A' }}
        </p>
    @endif



@else


@endif

