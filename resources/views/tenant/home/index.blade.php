@extends('layouts.tenant_layout')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Tenant Dashboard')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>{{ __('Hello ')}}{{ auth()->user()->name }} ,{{ __('welcome to your Foxes Rental Systems portal.')}}</p>
                                </div>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">

                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->


                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-inner-group">
                                <div class="card-inner">
                                    <div class="row gy-gs">
                                        <div class="col-lg-6">
                                            <div class="nk-iv-wg3">
                                                <div class="nk-iv-wg3-title">{{ __('Credits')}}</div>
                                                <div class="nk-iv-wg3-group  flex-lg-nowrap gx-4">
                                                    <div class="nk-iv-wg3-sub">
                                                        <div class="nk-iv-wg3-amount">
                                                            <div
                                                                class="number">{{ setting('currency_symbol') }} {{ number_format(auth()->user()->overpayment()->amount??0,2) }}

                                                            </div>
                                                        </div>
                                                        <div class="nk-iv-wg3-subtitle">{{ __('Current Overpayment')}}</div>
                                                    </div>
                                                    <div class="nk-iv-wg3-sub">
                                                        <span class="nk-iv-wg3-plus text-soft"><em
                                                                class="icon ni ni-plus"></em></span>
                                                        <div class="nk-iv-wg3-amount">
                                                            <div
                                                                class="number-sm">{{ setting('currency_symbol') }} {{ number_format(auth()->user()->overpayment()->amount??0.0,1) }}</div>
                                                        </div>
                                                        <div class="nk-iv-wg3-subtitle">{{ __('Pending proofs')}} <em
                                                                class="icon ni ni-info-fill" data-bs-toggle="tooltip"
                                                                data-bs-placement="right"
                                                                title="pending payment proofs"></em>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- .col -->
                                        <div class="col-lg-6">
                                            <div class="nk-iv-wg3">
                                                <div class="nk-iv-wg3-title">{{ __('Outstanding Payments')}}</div>
                                                <div class="nk-iv-wg3-group  flex-lg-nowrap gx-4">
                                                    <div class="nk-iv-wg3-sub">
                                                        <div class="nk-iv-wg3-amount">
                                                            <div
                                                                class="number">{{ setting('currency_symbol') }} {{ number_format($outstanding_balances,2) }}

                                                            </div>
                                                        </div>
                                                        <div class="nk-iv-wg3-subtitle">{{ __('Total arrears')}}</div>
                                                    </div>
                                                    <div class="nk-iv-wg3-sub">
                                                        <span class="nk-iv-wg3-plus text-soft"><em
                                                                class="icon ni ni-plus"></em></span>
                                                        <div class="nk-iv-wg3-amount">
                                                            <div
                                                                class="number-sm">{{ setting('currency_symbol') }} {{ number_format($outstanding_balances_due_this_month,2) }}</div>
                                                        </div>
                                                        <div class="nk-iv-wg3-subtitle">{{ __('Due this month')}} <em
                                                                class="icon ni ni-info-fill" data-bs-toggle="tooltip"
                                                                data-bs-placement="right"
                                                                title="{{ __('Total arrears due this month')}}"></em>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- .col -->

                                    </div><!-- .row -->
                                </div><!-- .card-inner -->
                                <div class="card-inner">
                                    <ul class="nk-iv-wg3-nav">
                                        <li>
                                            <a href="{{ route('tenant.invoices.index') }}"><em
                                                    class="icon ni ni-notes-alt"></em>
                                                <span>{{ __('My Invoices')}}</span>
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{ route('tenant.payments.index') }}"><em
                                                    class="icon ni ni-report-profit"></em>
                                                <span>{{ __('Payment History')}}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('tenant.support-tickets.index') }}"><em
                                                    class="icon ni ni-help"></em>
                                                <span>{{ __('Support Ticket')}}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div><!-- .card-inner -->
                            </div><!-- .card-inner-group -->
                        </div><!-- .card -->
                    </div><!-- .nk-block -->


                    <!-- Active leases for the tenant -->
                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head-sm">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h5 class="nk-block-title">{{ __('Active Leases')}} <span class="count text-base">({{ count($activeLeases) }})</span>
                                    </h5>
                                </div>

                            </div>
                        </div>
                        <div class="nk-iv-scheme-list">
                            @foreach($activeLeases as $lease)
                                <div class="nk-iv-scheme-item">
                                    <div class="nk-iv-scheme-icon is-done">
                                        <em class="icon ni ni-offer"></em>
                                    </div>
                                    <div class="nk-iv-scheme-info">
                                        <div class="nk-iv-scheme-name">{{ __('Lease for')}} {{ $lease->property->name }} {{ $lease->house->name }}</div>
                                        <div class="nk-iv-scheme-desc">{{ __('Recurring Rent')}} - <span
                                                class="amount">{{ number_format($lease->rent,2) }}</span>
                                        </div>
                                    </div>
                                    <div class="nk-iv-scheme-term">
                                        <div class="nk-iv-scheme-start nk-iv-scheme-order">
                                            <span class="nk-iv-scheme-label text-soft">{{ __('Start Date')}}</span>
                                            <span
                                                class="nk-iv-scheme-value date">{{ $lease->start_date->format('M d, Y') }}</span>
                                        </div>
                                        <div class="nk-iv-scheme-end nk-iv-scheme-order">
                                            <span class="nk-iv-scheme-label text-soft">{{ __('End Date')}}</span>
                                            <span class="nk-iv-scheme-value date">{{ __('Still Active')}} </span>
                                        </div>
                                    </div>
                                    <div class="nk-iv-scheme-amount">
                                        <div class="nk-iv-scheme-amount-a nk-iv-scheme-order">
                                            <span class="nk-iv-scheme-label text-soft">{{ __('Recurring Rent')}}</span>
                                            <span
                                                class="nk-iv-scheme-value amount">{{ number_format($lease->rent,2) }}</span>
                                        </div>
                                        <div class="nk-iv-scheme-amount-b nk-iv-scheme-order">
                                            <span class="nk-iv-scheme-label text-soft">{{ __('Static Bills')}}</span>
                                            <span
                                                class="nk-iv-scheme-value amount">{{ number_format($lease->bills_sum_amount,2) }} </span>
                                        </div>
                                    </div>
                                    <div class="nk-iv-scheme-more">
                                        <a class="btn btn-icon btn-lg btn-round btn-trans"
                                           href="">
                                            <em class="icon ni ni-forward-ios"></em>
                                        </a>
                                    </div>
                                </div><!-- .nk-iv-scheme-item -->
                            @endforeach


                        </div>
                    </div><!-- .nk-block -->

                    <!-- Archived leases for the tenant -->

                    <div class="nk-block nk-block-lg">
                        <div class="nk-block-head-sm">
                            <div class="nk-block-between">
                                <div class="nk-block-head-content">
                                    <h5 class="nk-block-title">{{ __('Past Leases')}} <span class="count text-base">({{ count($archivedLeases) }})</span>
                                    </h5>
                                </div>

                            </div>
                        </div>
                        <div class="nk-iv-scheme-list">
                            @foreach($archivedLeases as $archivedLease)
                                <div class="nk-iv-scheme-item">
                                    <div class="nk-iv-scheme-icon is-done">
                                        <em class="icon ni ni-offer"></em>
                                    </div>
                                    <div class="nk-iv-scheme-info">
                                        <div
                                            class="nk-iv-scheme-name">{{ $archivedLease->property->name }} {{ $archivedLease->house->name }}</div>
                                        <div class="nk-iv-scheme-desc">


                                            {{ $archivedLease->deleted_at->diffForHumans() }}


                                            <span class="amount"></span>
                                        </div>
                                    </div>
                                    <div class="nk-iv-scheme-term">
                                        <div class="nk-iv-scheme-start nk-iv-scheme-order">
                                            <span class="nk-iv-scheme-label text-soft">{{ __('Start Date')}}</span>
                                            <span
                                                class="nk-iv-scheme-value date">{{ $archivedLease->start_date->format('M d ,Y') }}</span>
                                        </div>
                                        <div class="nk-iv-scheme-end nk-iv-scheme-order">
                                            <span class="nk-iv-scheme-label text-soft">{{ __('End Date')}}</span>
                                            <span
                                                class="nk-iv-scheme-value date">{{ $archivedLease->deleted_at->format('M d ,Y') }}</span>
                                        </div>
                                    </div>
                                    <div class="nk-iv-scheme-amount">
                                        <div class="nk-iv-scheme-amount-a nk-iv-scheme-order">
                                            <span class="nk-iv-scheme-label text-soft">{{ __('Rent')}}</span>
                                            <span
                                                class="nk-iv-scheme-value amount">{{ number_format($archivedLease->rent,2) }}</span>
                                        </div>
                                        <div class="nk-iv-scheme-amount-b nk-iv-scheme-order">
                                            <span class="nk-iv-scheme-label text-soft">{{ __('Static Bills')}}</span>
                                            <span
                                                class="nk-iv-scheme-value amount">{{ number_format($archivedLease->bills_sum_amount,2) }} </span>
                                        </div>
                                    </div>
                                    <div class="nk-iv-scheme-more">
                                        <a class="btn btn-icon btn-lg btn-round btn-trans"
                                           href=""><em
                                                class="icon ni ni-forward-ios"></em></a>
                                    </div>
                                </div><!-- .nk-iv-scheme-item -->
                            @endforeach


                        </div>
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>

@endsection
