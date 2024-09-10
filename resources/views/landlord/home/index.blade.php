@extends('layouts.landlord_layout')


@push('css')
    <style>
        .nk-wg-card.is-dark {
            background: #2c3782;
            color: #fff;
        }

        .nk-wg-card:after {
            content: "";
            position: absolute;
            height: 0.25rem;
            background-color: transparent;
            left: 0;
            bottom: 0;
            right: 0;
            border-radius: 0 0 3px 3px;
        }

        .nk-wg-card.is-s1:after {
            background-color: #364a63;
        }

        .nk-wg-card.is-s2:after {
            background-color: #6576ff;
        }

        .nk-wg-card.is-s3:after {
            background-color: #1ee0ac;
        }

        .nk-iv-wg2 {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .nk-iv-wg2-text:not(:last-child) {
            margin-bottom: 2.5rem;
        }

        .nk-iv-wg2-title {
            margin-bottom: 0.75rem;
        }

        .nk-iv-wg2-title .title {
            font-size: 0.875rem;
            line-height: 1.25rem;
            font-weight: 500;
            color: #8094ae;
            font-family: Roboto, sans-serif;
        }

        .nk-iv-wg2-title .title .icon {
            font-size: 13px;
            margin-left: 0.2rem;
        }

        .is-dark .nk-iv-wg2-title .title {
            color: #c4cefe;
        }

        .nk-iv-wg2-amount {
            font-size: 2.25rem;
            letter-spacing: -0.03em;
            line-height: 1.15em;
            display: flex;
            justify-content: space-between;
            align-items: baseline;
        }

        .nk-iv-wg2-amount .change, .nk-iv-wg2-amount .sub {
            padding-left: 0.5rem;
            line-height: 1;
        }

        .nk-iv-wg2-amount .change, .nk-iv-wg2-amount .sub > span {
            font-size: 0.875rem;
            color: #6576ff;
            font-weight: 500;
            letter-spacing: normal;
        }

        .nk-iv-wg2-amount .sub {
            font-size: 0.875rem;
        }

        .nk-iv-wg2-amount .sub span {
            padding-right: 2px;
        }

        .nk-iv-wg2-amount.ui-v2 {
            font-size: 1.875rem;
            border-bottom: 2px solid #6576ff;
            padding-bottom: 1.25rem;
            margin-bottom: 1rem;
            display: block;
        }

        .nk-iv-wg2-amount.ui-v2 .change, .nk-iv-wg2-amount.ui-v2 .sub > span {
            font-size: 1rem;
        }

        .nk-iv-wg2-cta {
            text-align: center;
            margin-top: auto;
            margin-bottom: -0.5rem;
        }

        .nk-iv-wg2-cta .cta-extra {
            margin-top: 1rem;
            min-height: 28px;
        }

        .nk-iv-wg2-list li {
            padding-top: 0.3rem;
            padding-bottom: 0.3rem;
        }

        .nk-iv-wg2-list small, .nk-iv-wg2-list .small {
            font-size: 0.86em;
        }

        .nk-iv-wg2-list .item-value {
            font-weight: 500;
            font-size: 0.8125rem;
            color: #364a63;
            float: right;
        }

        .nk-iv-wg2-list .total {
            border-top: 1px solid #dbdfea;
            margin-top: 0.3rem;
            padding-top: 0.55rem;
            font-weight: 700;
        }

        .nk-iv-wg2-list .total .item-value {
            font-weight: 700;
        }

    </style>

@endpush

@section('content')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Overview')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>{{ __('Welcome to your personalized landlord dashboard,')}}{{ auth()->user()->name }}
                                        .</p>
                                </div>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                       data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li><a href="{{ route('landlord.notifications') }}"
                                                   class="btn btn-white btn-dim btn-outline-primary"><em
                                                        class="icon ni ni-bell"></em><span>{{ __('Notifications')}}</span></a>
                                            </li>
                                            <li><a href="{{ route('landlord.profile') }}"
                                                   class="btn btn-white btn-dim btn-outline-primary"><em
                                                        class="icon ni ni-account-setting"></em><span>{{ __('My Account')}}</span></a>
                                            </li>

                                        </ul>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div>

                    <div class="nk-block">
                        <div class="row gy-gs">
                            <div class="col-md-6 col-lg-4">
                                <div class="nk-wg-card is-dark card card-bordered">
                                    <div class="card-inner">
                                        <div class="nk-iv-wg2">
                                            <div class="nk-iv-wg2-title">
                                                <h6 class="title">{{ __('Total Remittances')}} <em
                                                        class="icon ni ni-info"></em>
                                                </h6>
                                            </div>
                                            <div class="nk-iv-wg2-text">
                                                <div class="nk-iv-wg2-amount">
                                                   {{ setting('currency_symbol') }} {{ number_format($total_remittances,2) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card -->
                            </div><!-- .col -->
                            <div class="col-md-6 col-lg-4">
                                <div class="nk-wg-card is-s1 card card-bordered">
                                    <div class="card-inner">
                                        <div class="nk-iv-wg2">
                                            <div class="nk-iv-wg2-title">
                                                <h6 class="title">{{ __('Owned Properties')}} <em
                                                        class="icon ni ni-info"></em>
                                                </h6>
                                            </div>
                                            <div class="nk-iv-wg2-text">
                                                <div class="nk-iv-wg2-amount">{{ number_format($owned_properties) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card -->
                            </div><!-- .col -->
                            <div class="col-md-12 col-lg-4">
                                <div class="nk-wg-card is-s3 card card-bordered">
                                    <div class="card-inner">
                                        <div class="nk-iv-wg2">
                                            <div class="nk-iv-wg2-title">
                                                <h6 class="title">{{ __('Owned Houses')}} <em
                                                        class="icon ni ni-info"></em></h6>
                                            </div>
                                            <div class="nk-iv-wg2-text">
                                                <div class="nk-iv-wg2-amount"> {{ number_format($owned_houses) }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- .card -->
                            </div><!-- .col -->
                        </div><!-- .row -->
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
