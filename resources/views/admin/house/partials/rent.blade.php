<p class="mt-0 mb-0 sub-text-sm">
    <strong>{{ __('Rent')}}</strong> {{setting('currency_symbol')}} {{ number_format($house->rent,2) }}
</p>
<p class="mt-0 mb-0 sub-text-sm">
    <strong>{{ __('Commission')}}</strong> {{ number_format($house->commission,2) }} %
</p>
