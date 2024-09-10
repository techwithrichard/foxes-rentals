@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Dashboard Overview')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>{{ __('Welcome to Rentals management Dashboard.')}}</p>
                                </div>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                       data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li>
                                            </li>
                                            <li class="nk-block-tools-opt">
                                                <a href="{{ route('admin.reports.company_income') }}"
                                                   class="btn btn-primary">
                                                    <em class="icon ni ni-reports"></em>
                                                    <span>{{ __('Reports')}}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->
                    <div class="nk-block">
                        <div class="row g-gs">
                            @can('view property')
                                <div class="col-md-4">
                                    <x-admin.properties-statistics-widget/>
                                </div><!-- .col -->
                            @endcan

                            @can('view house')
                                <div class="col-md-4">
                                    <x-admin.houses-statistics-widget/>
                                </div><!-- .col -->
                            @endcan

                            @can('view lease')
                                <div class="col-md-4">
                                    <x-admin.leases-statistics-widget/>
                                </div><!-- .col -->
                            @endcan


                            <div class="col-md-6 col-xxl-4">
                                <x-admin.latest-notifications-widget/>
                            </div><!-- .col -->

                            @can('view support ticket')
                                <div class="col-md-6 col-xxl-5">
                                    <x-admin.latest-support-tickets-widget/>
                                </div>
                            @endcan

                            @can('view outstanding payments report')
                                <div class="col-xxl-8">
                                    <x-admin.outstanding-payments-widget/>
                                </div>
                            @endcan


                            @can('view expiring leases report')
                                <div class="col-xxl-12">
                                    <livewire:admin.reports.expiring-leases-component/>
                                </div>
                            @endcan

                        </div><!-- .row -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>



@endsection
