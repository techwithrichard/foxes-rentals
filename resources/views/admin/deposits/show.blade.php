@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Deposit Details.')}}

                                </h3>


                            </div>


                            <div class="nk-block-head-content">

                            <x-back_link href="route('admin.deposits.index')" ></x-back_link>
                            
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->

                    @if (session()->has('success'))
                        <div class="nk-block">
                            <div class="alert alert-info alert-icon"><em class="icon ni ni-alert-circle"></em>
                                <strong>
                                    {{ session()->get('success') }}
                                </strong>
                            </div>
                        </div>
                    @endif

                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered">
                            <div class="card-aside-wrap">
                                <div class="card-content">
                                    <div class="card-inner">
                                        <div class="nk-block">

                                            <div class="profile-ud-list">

                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span class="profile-ud-label">{{ __('Deposit Amount')}}</span>
                                                        <span class="profile-ud-value">{{ setting('currency_symbol').' '.number_format($deposit->amount,2)}}</span>
                                                    </div>
                                                </div>
                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span class="profile-ud-label">{{ __('Created Date')}}</span>
                                                        <span class="profile-ud-value">{{ $deposit->created_at->format('M d Y')}}</span>
                                                    </div>
                                                </div>

                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span class="profile-ud-label">{{ __('Tenant')}}</span>
                                                        <span class="profile-ud-value">{{ $deposit->lease->tenant->name}}</span>
                                                    </div>
                                                </div>

                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span class="profile-ud-label">{{ __('Property')}}</span>
                                                        <span class="profile-ud-value">{{ $deposit->lease?->property->name}}</span>
                                                    </div>
                                                </div>
                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span class="profile-ud-label">{{ __('House')}}</span>
                                                        <span class="profile-ud-value">{{ $deposit->lease?->house->name}}</span>
                                                    </div>
                                                </div>

                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span class="profile-ud-label">{{ __('Refund Status')}}</span>
                                                        <span class="profile-ud-value">
                                                            @if($deposit->refund_paid==true)
                                                                <span class="badge  bg-success">{{ __('Refunded')}}</span>
                                                            @else
                                                                <span class="badge  bg-danger">{{ __('Not Refunded')}}</span>
                                                            @endif

                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span class="profile-ud-label">{{ __('Refunded Amount')}}</span>
                                                        <span class="profile-ud-value">{{ setting('currency_symbol').' '.number_format($deposit->refund_amount,2)}}</span>
                                                    </div>
                                                </div>
                                                <div class="profile-ud-item">
                                                    <div class="profile-ud wider">
                                                        <span class="profile-ud-label">{{ __('Refunded On')}}</span>
                                                        <span class="profile-ud-value">{{ $deposit->refund_date?->format('M d Y')}}</span>
                                                    </div>
                                                </div>

                                                @if($deposit->refund_receipt)
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span class="profile-ud-label">{{ __('Refund Receipt')}}</span>
                                                            <span class="profile-ud-value">
                                                                <a href="{{ url($deposit->refund_receipt) }}" target="_blank" download class="btn btn-sm btn-primary">View Receipt</a>
                                                            </span>
                                                        </div>
                                                    </div>

                                                @endif



                                            </div><!-- .profile-ud-list -->
                                        </div><!-- .nk-block -->
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div><!-- .nk-block -->


            </div>
        </div>
    </div>
@endsection
