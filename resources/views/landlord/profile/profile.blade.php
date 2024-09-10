@extends('layouts.landlord_layout')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block">
                        <div class="card card-bordered">
                            <div class="card-aside-wrap">
                                <div class="card-inner card-inner-lg">
                                    <div class="nk-block-head nk-block-head-lg">
                                        <div class="nk-block-between">
                                            <div class="nk-block-head-content">
                                                <h4 class="nk-block-title">{{ __('Personal Information')}}</h4>
                                                <div class="nk-block-des">
                                                
                                                </div>
                                            </div>
                                            <div class="nk-block-head-content align-self-start d-lg-none">
                                                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                                                   data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    <div class="nk-block">
                                        <div class="nk-data data-list">
                                            <div class="data-head">
                                                <h6 class="overline-title">{{ __('Basics')}}</h6>
                                            </div>
                                            <div class="data-item" data-bs-toggle="modal"
                                                 data-bs-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label">{{ __('Full Name')}}</span>
                                                    <span class="data-value">{{ $user->name }}</span>
                                                </div>
                                            </div><!-- data-item -->

                                            <div class="data-item">
                                                <div class="data-col">
                                                    <span class="data-label">{{ __('Email')}}</span>
                                                    <span class="data-value">{{ $user->email }}</span>
                                                </div>
                                            </div><!-- data-item -->
                                            <div class="data-item" data-bs-toggle="modal"
                                                 data-bs-target="#profile-edit">
                                                <div class="data-col">
                                                    <span class="data-label">{{ __('Phone Number')}}</span>
                                                    <span class="data-value text-soft">{{ $user->phone }}</span>
                                                </div>

                                            </div><!-- data-item -->

                                            <div class="data-item" data-bs-toggle="modal" data-bs-target="#profile-edit"
                                                 data-tab-target="#address">
                                                <div class="data-col">
                                                    <span class="data-label">{{ __('Address')}}</span>
                                                    <span
                                                        class="data-value">
                                                        {{ $user->address }}
                                                    </span>
                                                </div>

                                            </div><!-- data-item -->
                                        </div><!-- data-list -->
                                        <div class="nk-data data-list">
                                            <div class="data-head">
                                                <h6 class="overline-title">{{ __('Preferences')}}</h6>
                                            </div>
                                        


                                        </div><!-- data-list -->
                                    </div><!-- .nk-block -->
                                </div>

                                @include('landlord.profile.partials.aside')

                            </div><!-- .card-aside-wrap -->
                        </div><!-- .card -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>



@endsection

