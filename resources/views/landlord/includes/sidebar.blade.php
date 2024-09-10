<div class="nk-sidebar nk-sidebar-fixed {{ setting('sidebar_color_style')}}" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                    class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em
                    class="icon ni ni-menu"></em></a>
        </div>
        <div class="nk-sidebar-brand">
            <a href="{{ route('landlord.home') }}" class="logo-link nk-sidebar-logo">
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


                    <li class="nk-menu-item {{ active('landlord.home') }}">
                        <a href="{{ route('landlord.home') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                            <span class="nk-menu-text">{{ __('Dashboard')}}</span>
                        </a>
                    </li>

                    <li class="nk-menu-item {{ active(['landlord.properties.*']) }}">
                        <a href="{{ route('landlord.properties.index') }}" class="nk-menu-link"
                           data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-home"></em></span>
                            <span class="nk-menu-text">{{ __('My Properties')}}</span>
                        </a>
                    </li>

                    <li class="nk-menu-item {{ active(['landlord.houses.*']) }}">
                        <a href="{{ route('landlord.houses.index') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-grid-c"></em></span>
                            <span class="nk-menu-text">{{ __('My Houses')}}</span>
                        </a>
                    </li>


                    <li class="nk-menu-item {{ active(['landlord.invoices.*']) }}">
                        <a href="{{ route('landlord.invoices.index') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-tranx"></em></span>
                            <span class="nk-menu-text">{{ __('Invoices')}}</span>
                        </a>
                    </li>
                    <li class="nk-menu-item {{ active(['landlord.vouchers.*']) }}">
                        <a href="{{ route('landlord.vouchers.index') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-growth-fill"></em></span>
                            <span class="nk-menu-text">{{ __('Vouchers')}}</span>
                        </a>
                    </li>
                    <li class="nk-menu-item {{ active(['landlord.expenses.*']) }}">
                        <a href="{{ route('landlord.expenses.index') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-coins"></em></span>
                            <span class="nk-menu-text">{{ __('Expenses')}}</span>
                        </a>
                    </li>

                    <li class="nk-menu-item {{ active(['landlord.payouts.*']) }}">
                        <a href="{{ route('landlord.payouts.index') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-invest"></em></span>
                            <span class="nk-menu-text">{{ __('Payouts')}}</span>
                        </a>
                    </li>


                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>
