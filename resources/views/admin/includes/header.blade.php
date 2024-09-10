<div class="nk-header nk-header-fixed  {{ setting('header_color_style') }}">
    <div class="container-fluid">
        <div class="nk-header-wrap">
            <div class="nk-menu-trigger d-xl-none ms-n1">
                <a href="#" class="nk-nav-toggle nk-quick-nav-icon" data-target="sidebarMenu"><em
                        class="icon ni ni-menu"></em></a>
            </div>
            <div class="nk-header-brand d-xl-none">
                <a href="{{ route('admin.home') }}" class="logo-link">
                    <img class="logo-light logo-img" src="{{ asset('assets/images/logo.png')}}"
                         srcset="{{ asset('assets/images/logo2x.png')}} 2x"
                         alt="logo">
                    <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png')}}"
                         srcset="{{ asset('assets/images/logo-dark2x.png')}} 2x" alt="logo-dark">
                </a>
            </div><!-- .nk-header-brand -->
            <div class="nk-header-news d-none d-xl-block">
                <div class="nk-news-list">
                    <a class="nk-news-item" href="{{ route('notifications') }}">
                        <div class="nk-news-icon">
                            <em class="icon ni ni-card-view"></em>
                        </div>

                        @php
                            $latestAlert = auth()->user()->unreadNotifications()->latest()->first();
                        @endphp
                        <div class="nk-news-text">
                            {{ $latestAlert ? $latestAlert->data['title'] : __('No new unread notifications.') }}
                        </div>
                    </a>
                </div>
            </div><!-- .nk-header-news -->
            <div class="nk-header-tools">
                <ul class="nk-quick-nav">

                    <li class="dropdown language-dropdown d-none d-sm-block me-n1">
                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                            <div class="quick-icon border border-light">
                                {{--                                {{ Config::get('languages')[App::getLocale()]['display'] }}--}}
                                <img class="icon"
                                     src="{{ asset('assets/images/flags/'.Config::get('languages')[App::getLocale()]['flag-icon'].'-sq.png')}}"
                                     alt="">
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end dropdown-menu-s1">
                            <ul class="language-list">

                                @foreach(Config::get('languages') as $lang => $language)
                                    <li>
                                        <a href="{{ route('lang.switch', $lang) }}" class="language-item">
                                            <img
                                                src="{{ asset('assets/images/flags/'.$language['flag-icon'].'.png')}}"
                                                alt="" class="language-flag">
                                            <span class="language-name">{{ $language['display'] }}</span>
                                        </a>
                                    </li>
                                @endforeach


                            </ul>
                        </div>
                    </li><!-- .dropdown -->

                    <li class="dropdown user-dropdown">
                        <a href="#" class="dropdown-toggle" data-bs-toggle="dropdown">
                            <div class="user-toggle">
                                <div class="user-avatar sm">
                                    <em class="icon ni ni-user-alt"></em>
                                </div>
                                <div class="user-info d-none d-md-block">
                                    <div class="user-status">{{ __('Administrator')}}</div>
                                    <div class="user-name dropdown-indicator">{{ auth()->user()->name }}</div>
                                </div>
                            </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end dropdown-menu-s1">
                            <div class="dropdown-inner user-card-wrap bg-lighter d-none d-md-block">
                                <div class="user-card">
                                    <div class="user-avatar">
                                        <span>
                                            {{ auth()->user()->initials }}
                                        </span>
                                    </div>
                                    <div class="user-info">
                                        <span class="lead-text">{{ auth()->user()->name }}</span>
                                        <span class="sub-text">{{ auth()->user()->email }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">

                                    <li>
                                        <a href="{{ route('admin.profile') }}">
                                            <em class="icon ni ni-setting-alt"></em>
                                            <span>{{ __('Account Setting')}}</span>
                                        </a>
                                    </li>

                                </ul>
                            </div>
                            <div class="dropdown-inner">
                                <ul class="link-list">
                                    <li>

                                        <a href="javascript:void(0);" onclick="$('#logout-form').submit();">
                                            <em class="icon ni ni-signout"></em>
                                            <span> {{ __('Sign out')}}</span>
                                        </a>

                                        <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                              style="display: none;">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </li><!-- .dropdown -->
                    <li class="dropdown notification-dropdown me-n1">
                        <a href="#" class="dropdown-toggle nk-quick-nav-icon" data-bs-toggle="dropdown">
                            @if(auth()->user()->unreadNotifications->count() > 0)
                                <div class="icon-status icon-status-info">
                                    <em class="icon ni ni-bell"></em>
                                </div>
                            @endif
                        </a>

                        <livewire:widgets.notification-dropdown-component/>

                    </li><!-- .dropdown -->
                </ul><!-- .nk-quick-nav -->
            </div><!-- .nk-header-tools -->
        </div><!-- .nk-header-wrap -->
    </div><!-- .container-fliud -->
</div>
