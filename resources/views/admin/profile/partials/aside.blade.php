<div
    class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg"
    data-toggle-body="true" data-content="userAside" data-toggle-screen="lg"
    data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <div class="card-inner">
            <div class="user-card">
                <div class="user-avatar bg-primary">
                    <span>{{ auth()->user()->initials }}</span>
                </div>
                <div class="user-info">
                    <span class="lead-text">{{ auth()->user()->name }}</span>
                    <span class="sub-text">{{ auth()->user()->email }}</span>
                </div>
                <div class="user-action">
                    <div class="dropdown">
                        <a class="btn btn-icon btn-trigger me-n2"
                           data-bs-toggle="dropdown" href="#"><em
                                class="icon ni ni-more-v"></em></a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <ul class="link-list-opt no-bdr">
                                {{--                                <li><a href="#"><em class="icon ni ni-camera-fill"></em><span>Change Photo</span></a>--}}
                                {{--                                </li>--}}
                                {{--                                <li><a href="#"><em--}}
                                {{--                                            class="icon ni ni-edit-fill"></em><span>Update Profile</span></a>--}}
                                {{--                                </li>--}}
                            </ul>
                        </div>
                    </div>
                </div>
            </div><!-- .user-card -->
        </div><!-- .card-inner -->
        <div class="card-inner">
            <div class="user-account-info py-0">
                <h6 class="overline-title-alt">{{ __('Admin Account')}}</h6>

                <div class="user-balance-sub">{{ __('Active since')}}
                    <span>{{ auth()->user()->created_at->format('M d Y') }} </span></div>
            </div>
        </div><!-- .card-inner -->
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <li><a class="{{ active('admin.profile') }}" href="{{ route('admin.profile') }}">
                        <em class="icon ni ni-user-fill-c"></em>
                        <span>{{ __('Personal Infomation')}}</span>
                    </a>
                </li>

                <li>
                    <a class="{{ active('admin.login_activities') }}" href="{{ route('admin.login_activities') }}">
                        <em class="icon ni ni-activity-round-fill"></em>
                        <span>{{ __('Account Activity')}}</span>
                    </a>
                </li>
                <li>
                    <a class="{{ active('admin.security_settings') }}" href="{{ route('admin.security_settings') }}">
                        <em class="icon ni ni-lock-alt-fill"></em>
                        <span>{{ __('Security Settings')}}</span>
                    </a>
                </li>

            </ul>
        </div><!-- .card-inner -->
    </div><!-- .card-inner-group -->
</div><!-- card-aside -->
