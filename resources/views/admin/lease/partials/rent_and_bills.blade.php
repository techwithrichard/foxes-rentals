<span class="sub-text">{{ __('Rent')}}: {{ setting('currency_symbol') . ' ' . number_format($lease->rent, 2) }}</span>
@if($lease->bills_sum_amount > 0)
    <span
        class="sub-text">{{ __('Bills')}}: {{ setting('currency_symbol') . ' ' . number_format($lease->bills_sum_amount, 2) }}</span>
    <span
        class="sub-text">{{ __('Total')}}: {{ setting('currency_symbol') . ' ' . number_format($lease->rent + $lease->bills_sum_amount, 2) }}</span>

@endif
