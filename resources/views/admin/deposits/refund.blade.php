@if($deposit->refund_paid!=0)
    <span class="badge  bg-success">{{ __('Refunded')}}</span>
@else
    <span class="badge  bg-danger">{{ __('Not Refunded')}}</span>
@endif
