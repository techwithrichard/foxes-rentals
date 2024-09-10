@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Add House(s)')}}</h3>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">

                                </div><!-- .toggle-wrap -->
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->
                    <div class="nk-block">
                        <div class="card card-bordered card-stretch">
                            <div class="card-inner">
                                <h5 class="title">{{ __('House Details')}}</h5>
                                <ul class="nk-nav nav nav-tabs">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#singleHouse">
                                            {{ __('Single House')}}
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#bulkHouses">{{ __('Add In Bulk')}}</a>
                                    </li>
                                </ul><!-- .nav-tabs -->
                                <div class="tab-content">
                                    <div class="tab-pane active" id="singleHouse">
                                        @livewire('admin.house.create-house-component')

                                    </div><!-- .tab-pane -->
                                    <div class="tab-pane" id="bulkHouses">
                                        @livewire('admin.house.create-bulk-houses-component')
                                    </div><!-- .tab-pane -->
                                </div><!-- .tab-content -->
                            </div>
                            <!--card inner-->
                        </div><!-- .card -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>
@endsection
