@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Landlord Income')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>{{ __('Breakdown of landlord monthly income,including rental income,commission deductions and associated expenses deductions.')}}</p>
                                </div>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">

                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->
                    @livewire('admin.reports.landlord-income-component')
                </div>
            </div>
        </div>
    </div>

@endsection
