@extends('layouts.tenant_layout')

@section('content')

    <div class="nk-content ">
        <div class="nk-block nk-block-middle wide-xs mx-auto">
            <div class="nk-block-content nk-error-ld text-center">
                <em class="icon icon-circle icon-circle-xxl ni ni-check bg-success mb-3"></em>
                <h3 class="nk-success-title">{{ __('Payment Received.')}}</h3>

                <p class="nk-error-text">
                    {{ __('Your payment has been processed successfully. You can view your invoice in your account.')}}
                </p>

                <a href="{{ route('tenant.invoices.index') }}"
                   class="btn btn-lg btn-primary mt-2">
                    {{ __('Back To My Invoice')}}
                </a>
            </div>
        </div><!-- .nk-block -->
    </div>
@endsection

