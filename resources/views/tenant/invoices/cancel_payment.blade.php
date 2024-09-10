@extends('layouts.tenant_layout')

@section('content')

    <div class="nk-content ">
        <div class="nk-block nk-block-middle wide-xs mx-auto">
            <div class="nk-block-content nk-error-ld text-center">
                <em class="icon icon-circle icon-circle-xxl ni ni-cross bg-danger mb-3"></em>
                <h3 class="nk-error-title">{{ __('Payment Failed')}}</h3>

                <p class="nk-error-text">
                    @if(isset($error_message))
                        {{ $error_message }}
                    @else
                        {{ __('Your payment has been cancelled. You can try again later.')}}
                    @endif

                </p>

                <a href="{{ route('tenant.invoices.index') }}"
                   class="btn btn-lg btn-primary mt-2">
                    {{ __('Back To My Invoice')}}
                </a>
            </div>
        </div><!-- .nk-block -->
    </div>
@endsection

