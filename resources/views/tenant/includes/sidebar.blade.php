<div class="nk-sidebar nk-sidebar-fixed {{ setting('sidebar_color_style')}}" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                    class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em
                    class="icon ni ni-menu"></em></a>
        </div>
        <div class="nk-sidebar-brand">
            <a href="{{ route('tenant.home') }}" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset('assets/images/logo.png')}}" srcset="{{ asset('assets/images/logo2x.png')}} 2x" alt="logo">
                <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png')}}" srcset="{{ asset('assets/images/logo-dark2x.png')}} 2x"
                     alt="logo-dark">
            </a>
        </div>
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">
                    <li class="nk-menu-item {{ active('tenant.home') }}">
                        <a href="{{ route('tenant.home') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                            <span class="nk-menu-text">{{ __('Dashboard')}}</span>
                        </a>
                    </li>

                    <li class="nk-menu-item {{ active(['tenant.invoices.*']) }}">
                        <a href="{{ route('tenant.invoices.index') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                            <span class="nk-menu-text">{{ __('Invoices')}}</span>
                        </a>
                    </li>


                    <li class="nk-menu-item {{ active('tenant.payments.index') }}">
                        <a href="{{ route('tenant.payments.index') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-reports"></em></span>
                            <span class="nk-menu-text">{{ __('Payment History')}}</span>
                        </a>
                    </li>

                    <li class="nk-menu-item {{ active(['tenant.support-tickets.*']) }}">
                        <a href="{{ route('tenant.support-tickets.index') }}" class="nk-menu-link"
                           data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-notice"></em></span>
                            <span class="nk-menu-text">{{ __('Support Tickets')}}</span>
                        </a>
                    </li>


                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>
