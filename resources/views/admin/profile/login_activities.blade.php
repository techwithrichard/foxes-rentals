@extends('layouts.main')

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
                                                <h4 class="nk-block-title">{{ __('Login Activity')}}</h4>
                                                <div class="nk-block-des">
                                                    <p>{{ __('Here is your last 20 login activities log.')}} <span
                                                            class="text-soft"><em class="icon ni ni-info"></em></span>
                                                    </p>
                                                </div>
                                            </div>
                                            <div class="nk-block-head-content align-self-start d-lg-none">
                                                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                                                   data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                            </div>
                                        </div>
                                    </div><!-- .nk-block-head -->
                                    <div class="nk-block card card-bordered">
                                        <table class="table table-ulogs">
                                            <thead class="table-light">
                                            <tr>
                                                <th class="tb-col-os"><span class="overline-title">{{ __('Browser')}} <span
                                                            class="d-sm-none">/ IP</span></span></th>
                                                <th class="tb-col-ip"><span class="overline-title">{{ __('IP')}}</span></th>
                                                <th class="tb-col-time"><span class="overline-title">{{ __('Login')}}</span></th>
                                                <th class="tb-col-time"><span class="overline-title">{{ __('Logout')}}</span></th>
                                            </tr>
                                            </thead>
                                            <tbody>

                                            @foreach($login_activities as $activity)
                                                <tr>
                                                    <td class="tb-col-os">{{ $activity->browser }}</td>
                                                    <td class="tb-col-ip"><span
                                                            class="sub-text">{{ $activity->ip_address }}</span></td>
                                                    <td class="tb-col-time"><span
                                                            class="sub-text">{{ $activity->login_at?->toDayDateTimeString() }}</span></td>
                                                    <td class="tb-col-time"><span
                                                            class="sub-text">{{ $activity->logout_at?->toDayDateTimeString() }}</span></td>

                                                </tr>
                                            @endforeach

                                            </tbody>
                                        </table>
                                    </div><!-- .nk-block-head -->
                                </div><!-- .card-inner -->

                                @include('admin.profile.partials.aside')
                            </div><!-- card-aside-wrap -->
                        </div><!-- .card -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>



@endsection

