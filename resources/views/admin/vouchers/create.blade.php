@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Create Voucher')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <ul class="list-inline">
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.vouchers.index') }}"></x-back_link>
                                  
                           
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    @livewire('admin.accounting.create-voucher-component')
                </div>
            </div>
        </div>
    </div>

@endsection

