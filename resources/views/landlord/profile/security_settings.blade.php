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
                                                <h4 class="nk-block-title">{{ __('Security Settings')}}</h4>
                                                <div class="nk-block-des">
                                                    <p>{{ __('These settings are helps you keep your account secure')}}.</p>
                                                </div>
                                            </div>
                                            <div class="nk-block-head-content align-self-start d-lg-none">
                                                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                                                   data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    @if(session()->has('success'))
                                        <div class="my-2">
                                            <div class="alert alert-success alert-icon">
                                                <em class="icon ni ni-check-circle"></em>
                                                <strong>{{ __('Completed') }}</strong>.
                                                {{ session()->get('success') }}
                                            </div>
                                        </div>
                                    @endif

                                    <div class="nk-block">
                                        <div class="card card-bordered">
                                            <div class="card-inner-group">


                                                <div class="card-inner">
                                                    <div class="between-center flex-wrap g-3">
                                                        <div class="nk-block-text">
                                                            <h6>{{ __('Change Password')}}</h6>
                                                            <p>{{ __('Set a unique password to protect your account')}}.</p>
                                                        </div>
                                                        <div class="nk-block-actions flex-shrink-sm-0">
                                                            <ul class="align-center flex-wrap flex-sm-nowrap gx-3 gy-2">
                                                                <li class="order-md-last">
                                                                    <button data-bs-toggle="modal"
                                                                            data-bs-target="#modalChangePassword"
                                                                            type="button"
                                                                            class="btn btn-primary">
                                                                        {{ __('Change Password')}}
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <em class="text-soft text-date fs-12px">
                                                                        {{ __('Last changed')}}:
                                                                        <span>{{ auth()->user()->password_changed_at?->toDayDateTimeString()??'Never changed' }}</span>
                                                                    </em>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </div><!-- .card-inner -->

                                            </div><!-- .card-inner-group -->
                                        </div><!-- .card -->
                                    </div><!-- .nk-block -->
                                </div><!-- .card-inner -->

                                @include('landlord.profile.partials.aside')
                            </div><!-- .card-aside-wrap -->
                        </div><!-- .card -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>


    <!-- Modal Change Password -->

    <div class="modal fade" id="modalChangePassword" wire:ignore.self>
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <a href="#" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <em class="icon ni ni-cross"></em>
                    </a>
                </div>
                @livewire('widgets.update-password-component')

            </div>
        </div>
    </div>




@endsection

