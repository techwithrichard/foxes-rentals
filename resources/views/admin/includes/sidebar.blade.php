<div class="nk-sidebar nk-sidebar-fixed {{ setting('sidebar_color_style')}}" data-content="sidebarMenu">
    <div class="nk-sidebar-element nk-sidebar-head">
        <div class="nk-menu-trigger">
            <a href="#" class="nk-nav-toggle nk-quick-nav-icon d-xl-none" data-target="sidebarMenu"><em
                    class="icon ni ni-arrow-left"></em></a>
            <a href="#" class="nk-nav-compact nk-quick-nav-icon d-none d-xl-inline-flex" data-target="sidebarMenu"><em
                    class="icon ni ni-menu"></em></a>
        </div>
        <div class="nk-sidebar-brand">
            <a href="{{ route('admin.home') }}" class="logo-link nk-sidebar-logo">
                <img class="logo-light logo-img" src="{{ asset('assets/images/logo.png')}}"
                     srcset="{{ asset('assets/images/logo2x.png')}} 2x" alt="logo">
                <img class="logo-dark logo-img" src="{{ asset('assets/images/logo-dark.png')}}"
                     srcset="{{ asset('assets/images/logo-dark2x.png')}} 2x"
                     alt="logo-dark">
            </a>
        </div>
    </div><!-- .nk-sidebar-element -->
    <div class="nk-sidebar-element nk-sidebar-body">
        <div class="nk-sidebar-content">
            <div class="nk-sidebar-menu" data-simplebar>
                <ul class="nk-menu">

                    <li class="nk-menu-item {{ active('admin.home') }}" id="menu-home">
                        <a href="{{ route('admin.home') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                            <span class="nk-menu-text">{{ __('Dashboard')}}</span>
                        </a>
                    </li>

<!-- Tenants -->
                    <li class="nk-menu-item has-sub {{ active(['admin.tenants.*','admin.archived-tenants.index']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list"></em></span>
                            <span class="nk-menu-text">{{ __('Tenants')}}</span>
                        </a>
                        <ul class="nk-menu-sub">
                            @can('create tenant')
                                <li class="nk-menu-item {{ active('admin.tenants.create') }}" id="menu1">
                                    <a href="{{ route('admin.tenants.create') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">
                                        {{ __('Add Tenant')}}
                                    </span>
                                    </a>
                                </li>
                            @endcan

                            @can('view tenant')
                                <li class="nk-menu-item {{ active('admin.tenants.index') }}" id="menu2">
                                    <a href="{{ route('admin.tenants.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">
                                        {{ __('Active Tenants')}}
                                    </span>
                                    </a>
                                </li>
                            @endcan

                            @can('view archived tenant')
                                <li class="nk-menu-item {{ active('admin.archived-tenants.index') }}" id="menu3">
                                    <a href=" {{ route('admin.archived-tenants.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Archived Tenants')}}</span></a>
                                </li>
                            @endcan
                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <li class="nk-menu-item has-sub {{ active(['admin.landlords.*']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-list"></em></span>
                            <span class="nk-menu-text">{{ __('Landlords')}}</span>
                        </a>
                        <ul class="nk-menu-sub">

                            @can('create landlord')
                                <li class="nk-menu-item {{ active(['admin.landlords.create','admin.landlords.edit']) }}">
                                    <a href="{{ route('admin.landlords.create') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">
                                        {{ __('Create Landlord')}}
                                    </span>
                                    </a>
                                </li>
                            @endcan

                            @can('view landlord')
                                <li class="nk-menu-item {{ active('admin.landlords.index') }}">
                                    <a href="{{ route('admin.landlords.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">
                                        {{ __('Landlords Listing')}}
                                    </span>
                                    </a>
                                </li>
                            @endcan

                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <!-- Enhanced Properties Menu -->
                    <li class="nk-menu-item has-sub {{ active(['admin.properties.*','admin.houses.*','admin.property-dashboard.*','admin.properties-for-rent.*','admin.properties-for-sale.*','admin.properties-for-lease.*']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-building"></em></span>
                            <span class="nk-menu-text">{{ __('Properties')}}</span>
                        </a>
                        <ul class="nk-menu-sub">
                            <!-- Property Dashboard -->
                            <li class="nk-menu-item {{ active('admin.property-dashboard.*') }}">
                                <a href="{{ route('admin.property-dashboard.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">{{ __('📊 Dashboard')}}</span>
                                </a>
                            </li>

                            <!-- All Properties -->
                            <li class="nk-menu-item {{ active('admin.properties.*') }}">
                                <a href="{{ route('admin.properties.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">{{ __('🏘️ All Properties')}}</span>
                                </a>
                            </li>

                            <!-- For Rent -->
                            <li class="nk-menu-item {{ active('admin.properties-for-rent.*') }}">
                                <a href="{{ route('admin.properties-for-rent.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">{{ __('🏠 For Rent')}}</span>
                                </a>
                            </li>

                            <!-- For Sale -->
                            <li class="nk-menu-item {{ active('admin.properties-for-sale.*') }}">
                                <a href="{{ route('admin.properties-for-sale.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">{{ __('💰 For Sale')}}</span>
                                </a>
                            </li>

                            <!-- For Lease -->
                            <li class="nk-menu-item {{ active('admin.properties-for-lease.*') }}">
                                <a href="{{ route('admin.properties-for-lease.index') }}" class="nk-menu-link">
                                    <span class="nk-menu-text">{{ __('📋 For Lease')}}</span>
                                </a>
                            </li>

                            <!-- Create Property -->
                            @can('create property')
                                <li class="nk-menu-item {{ active('admin.properties.create') }}">
                                    <a href="{{ route('admin.properties.create') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('➕ Create Property')}}</span>
                                    </a>
                                </li>
                            @endcan

                            <!-- Property Settings -->
                            @can('view settings')
                                <li class="nk-menu-item {{ active('admin.property-settings.*') }}">
                                    <a href="{{ route('admin.property-settings.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('⚙️ Settings')}}</span>
                                    </a>
                                </li>
                            @endcan

                            <!-- Houses (Legacy) -->
                            @can('view house')
                                <li class="nk-menu-item {{ active('admin.houses.*') }}">
                                    <a href="{{ route('admin.houses.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('🏠 Houses')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <!-- Leases -->
                    <li class="nk-menu-item has-sub {{ active(['admin.leases.*','admin.leases-history.*']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-contact"></em></span>
                            <span class="nk-menu-text">{{ __('Leases')}}</span>
                        </a>
                        <ul class="nk-menu-sub">

                            @can('create lease')
                                <li class="nk-menu-item {{ active('admin.leases.create') }}">
                                    <a href="{{ route('admin.leases.create') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('New Lease')}}</span></a>
                                </li>

                            @endcan

                            @can('view lease')
                                <li class="nk-menu-item {{ active(['admin.leases.index','admin.leases.edit','admin.leases.show']) }}">
                                    <a href="{{ route('admin.leases.index') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Active Leases')}}</span></a>
                                </li>
                            @endcan

                            @can('view lease history')
                                <li class="nk-menu-item {{ active('admin.leases-history.index') }}">
                                    <a href="{{ route('admin.leases-history.index') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Lease History')}}</span></a>
                                </li>

                            @endcan

                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <!-- Accounting -->
                    <!-- Accounting -->
<li class="nk-menu-item has-sub {{ active(['admin.rent-invoice.*', 'admin.custom-invoice.*', 'admin.vouchers.*', 'admin.expenses.*']) }}">
    <a href="#" class="nk-menu-link nk-menu-toggle">
        <span class="nk-menu-icon"><em class="icon ni ni-file-doc"></em></span>
        <span class="nk-menu-text">{{ __('Accounting') }}</span>
    </a>
    <ul class="nk-menu-sub">
        @can('view invoice')
            <li class="nk-menu-item {{ active('admin.rent-invoice.*') }}">
                <a href="{{ route('admin.rent-invoice.index') }}" class="nk-menu-link">
                    <span class="nk-menu-text">{{ __('Rent Invoices') }}</span>
                </a>
            </li>
        @endcan

        @can('view custom invoice')
            <li class="nk-menu-item {{ active('admin.custom-invoice.*') }}">
                <a href="{{ route('admin.custom-invoice.index') }}" class="nk-menu-link">
                    <span class="nk-menu-text">{{ __('Custom Invoices') }}</span>
                </a>
            </li>
        @endcan

        @can('view voucher')
            <li class="nk-menu-item {{ active('admin.vouchers.*') }}">
                <a href="{{ route('admin.vouchers.index') }}" class="nk-menu-link">
                    <span class="nk-menu-text">{{ __('Landlord Vouchers') }}</span>
                </a>
            </li>
        @endcan

        @can('view expense')
            <li class="nk-menu-item {{ active('admin.expenses.*') }}">
                <a href="{{ route('admin.expenses.index') }}" class="nk-menu-link">
                    <span class="nk-menu-text">{{ __('Expenses') }}</span>
                </a>
            </li>
        @endcan
    </ul><!-- .nk-menu-sub -->
</li><!-- .nk-menu-item -->

<!-- Payments -->
<li class="nk-menu-item has-sub {{ active(['admin.payments.*', 'admin.deposits.*', 'admin.overpayments.*', 'admin.landlord-remittance.*', 'admin.mpesa-stk-transactions', 'admin.mpesa-c2b-transactions', 'admin.mpesa-c2b-transactions.reconcile']) }}">
    <a href="#" class="nk-menu-link nk-menu-toggle">
        <span class="nk-menu-icon"><em class="icon ni ni-tranx"></em></span>
        <span class="nk-menu-text">{{ __('Payments') }}</span>
    </a>
    <ul class="nk-menu-sub">
        @can('view payment')
            <li class="nk-menu-item {{ active('admin.payments.*') }}">
                <a href="{{ route('admin.payments.list') }}" class="nk-menu-link">
                    <span class="nk-menu-text">{{ __('Payments History') }}</span>
                </a>
            </li>
        @endcan

        @can('view deposit')
            <li class="nk-menu-item {{ active('admin.deposits.*') }}">
                <a href="{{ route('admin.deposits.index') }}" class="nk-menu-link">
                    <span class="nk-menu-text">{{ __('Deposits Payment') }}</span>
                </a>
            </li>
        @endcan

        @can('view overpayment')
            <li class="nk-menu-item {{ active('admin.overpayments.*') }}">
                <a href="{{ route('admin.overpayments.index') }}" class="nk-menu-link">
                    <span class="nk-menu-text">{{ __('Overpayments') }}</span>
                </a>
            </li>
        @endcan

        @can('view landlord remittance')
            <li class="nk-menu-item {{ active('admin.landlord-remittance.*') }}">
                <a href="{{ route('admin.landlord-remittance.index') }}" class="nk-menu-link">
                    <span class="nk-menu-text">{{ __('Landlord Remittances') }}</span>
                </a>
            </li>
        @endcan

        <!-- MPesa Transactions -->
        <li class="nk-menu-item has-sub {{ active(['admin.mpesa-stk-transactions', 'admin.mpesa-c2b-transactions', 'admin.mpesa-c2b-transactions.reconcile']) }}">
            <a href="#" class="nk-menu-link nk-menu-toggle">
                <span class="nk-menu-icon"><em class="icon ni ni-tranx"></em></span>
                <span class="nk-menu-text">{{ __('MPesa Transactions') }}</span>
            </a>
            <ul class="nk-menu-sub">
                @can('view mpesa c2b transactions')
                    <li class="nk-menu-item {{ active('admin.mpesa-c2b-transactions') }}">
                        <a href="{{ route('admin.mpesa-c2b-transactions') }}" class="nk-menu-link">
                            <span class="nk-menu-text">{{ __('C2B Transactions') }}</span>
                        </a>
                    </li>
                @endcan

                @can('view mpesa stk transactions')
                    <li class="nk-menu-item {{ active('admin.mpesa-stk-transactions') }}">
                        <a href="{{ route('admin.mpesa-stk-transactions') }}" class="nk-menu-link">
                            <span class="nk-menu-text">{{ __('STK Push Transactions') }}</span>
                        </a>
                    </li>
                @endcan

                @can('reconcile mpesa c2b transactions')
                    <li class="nk-menu-item {{ active('admin.mpesa-c2b-transactions.reconcile') }}">
                        <a href="{{ route('admin.mpesa-c2b-transactions.reconcile', ['id' => 1]) }}" class="nk-menu-link">
                            <span class="nk-menu-text">{{ __('Reconcile C2B Transactions') }}</span>
                        </a>
                    </li>
                @endcan
            </ul><!-- .nk-menu-sub -->
        </li><!-- .nk-menu-item -->
    </ul><!-- .nk-menu-sub -->
</li><!-- .nk-menu-item -->



                    <!-- Reports -->
                    <li class="nk-menu-item has-sub {{ active(['admin.reports.*']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-tranx"></em></span>
                            <span class="nk-menu-text">{{ __('Reports')}}</span>
                        </a>
                        <ul class="nk-menu-sub">
                            @can('view landlord income report')
                                <li class="nk-menu-item {{ active('admin.reports.landlord_income') }}">
                                    <a href="{{ route('admin.reports.landlord_income') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Landlord Income')}}</span></a>
                                </li>
                            @endcan

                            @can('view property income report')
                                <li class="nk-menu-item {{ active('admin.reports.property_income') }}">
                                    <a href="{{ route('admin.reports.property_income') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Property Income')}}</span></a>
                                </li>
                            @endcan

                            @can('view company income report')
                                <li class="nk-menu-item {{ active('admin.reports.company_income') }} ">
                                    <a href="{{ route('admin.reports.company_income') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Company Commission')}}</span></a>
                                </li>
                            @endcan

                            @can('view outstanding payments report')
                                <li class="nk-menu-item {{ active('admin.reports.outstanding_payments') }}">
                                    <a href="{{ route('admin.reports.outstanding_payments') }}"
                                       class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Outstanding Payments')}}</span></a>
                                </li>

                            @endcan

                            @can('view landlord expenses report')
                                <li class="nk-menu-item {{ active('admin.reports.landlord_expenses') }}">
                                    <a href="{{ route('admin.reports.landlord_expenses') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Landlord Expenses')}}</span></a>
                                </li>
                            @endcan

                            @can('view company expenses report')
                                <li class="nk-menu-item {{ active('admin.reports.company_expenses') }}">
                                    <a href="{{ route('admin.reports.company_expenses') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Company Expenses')}}</span></a>
                                </li>
                            @endcan

                            @can('view expiring leases report')
                                <li class="nk-menu-item {{ active('admin.reports.expiring_leases') }}">
                                    <a href="{{ route('admin.reports.expiring_leases') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Expiring Leases')}}</span></a>
                                </li>
                            @endcan


                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->


                    <!-- Support/Maintenance -->
                    @can('view support ticket')
                        <li class="nk-menu-item {{ active('admin.support-tickets.*') }}">
                            <a href="{{ route('admin.support-tickets.index') }}" class="nk-menu-link"
                               data-bs-original-title="" title="">
                                <span class="nk-menu-icon"><em class="icon ni ni-building"></em></span>
                                <span class="nk-menu-text">{{ __('Support Tickets')}}</span>
                                @livewire('widgets.tickets-badge-widget')

                            </a>
                        </li>
                    @endcan


                    <!-- Administration -->
                    <li class="nk-menu-item has-sub {{ active(['admin.settings.*','admin.backups.*','admin.activity-log.index','admin.deleted-records.*']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-setting"></em></span>
                            <span class="nk-menu-text">{{ __('Administration')}}</span>
                        </a>

                        <ul class="nk-menu-sub">
                            @can('view settings')
                                <li class="nk-menu-item {{ active('admin.settings.*') }}">
                                    <a href="{{ route('admin.settings.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Settings')}}</span></a>
                                </li>
                            @endcan

                            @can('view deleted records')
                                <li class="nk-menu-item {{ active('admin.deleted-records.*') }}">
                                    <a href="{{ route('admin.deleted-records.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Deleted Records')}}</span></a>
                                </li>
                            @endcan

{{--                            @can('view backup')--}}
{{--                                <li class="nk-menu-item {{ active('admin.backups.*') }}">--}}
{{--                                    <a href="{{ route('admin.backups.index') }}" class="nk-menu-link">--}}
{{--                                        <span class="nk-menu-text">{{ __('Back Ups')}}</span>--}}
{{--                                    </a>--}}
{{--                                </li>--}}
{{--                            @endcan--}}

                            @can('view activity log')
                                <li class="nk-menu-item {{ active('admin.activity-log.index') }}">
                                    <a href="{{ route('admin.activity-log.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Activity Log')}}</span>
                                    </a>
                                </li>
                            @endcan

                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

<!-- \User anagement -->
                    @can('manage users')
                        <li class="nk-menu-item has-sub {{ active(['admin.users-management.*','admin.roles-management.*']) }}">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon"><em class="icon ni ni-user-check"></em></span>
                                <span class="nk-menu-text">{{ __('User Management')}}</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item {{ active(['admin.users-management.*']) }}">
                                    <a href="{{ route('admin.users-management.index') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Manage Users')}}</span></a>
                                </li>
                                <li class="nk-menu-item {{ active(['admin.roles-management.*']) }}">
                                    <a href="{{ route('admin.roles-management.index') }}" class="nk-menu-link"><span
                                            class="nk-menu-text">{{ __('Roles & Permissions')}}</span></a>
                                </li>

                            </ul><!-- .nk-menu-sub -->
                        </li><!-- .nk-menu-item -->

                    @endcan


                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>
