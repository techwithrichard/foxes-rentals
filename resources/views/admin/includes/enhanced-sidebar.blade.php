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

                    <!-- Dashboard -->
                    <li class="nk-menu-item {{ active('admin.home') }}" id="menu-home">
                        <a href="{{ route('admin.home') }}" class="nk-menu-link" data-bs-original-title=""
                           title="">
                            <span class="nk-menu-icon"><em class="icon ni ni-dashboard"></em></span>
                            <span class="nk-menu-text">{{ __('Dashboard')}}</span>
                        </a>
                    </li>

                    <!-- User Management - First Priority -->
                    @can('manage users')
                        <li class="nk-menu-item has-sub {{ active(['admin.users-management.*','admin.roles-management.*','admin.user-activity.*']) }}">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon"><em class="icon ni ni-user-check"></em></span>
                                <span class="nk-menu-text">{{ __('User Management')}}</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item {{ active(['admin.users-management.*']) }}">
                                    <a href="{{ route('admin.users-management.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('All Users')}}</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item {{ active(['admin.roles-management.*']) }}">
                                    <a href="{{ route('admin.roles-management.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Roles & Permissions')}}</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item {{ active(['admin.user-activity.*']) }}">
                                    <a href="{{ route('admin.user-activity.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('User Activity')}}</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item {{ active(['admin.user-roles.*']) }}">
                                    <a href="{{ route('admin.user-roles.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Role Assignments')}}</span>
                                    </a>
                                </li>
                            </ul><!-- .nk-menu-sub -->
                        </li><!-- .nk-menu-item -->
                    @endcan

                    <!-- Property Management - Enhanced -->
                    <li class="nk-menu-item has-sub {{ active(['admin.properties.*','admin.rental-properties.*','admin.sale-properties.*','admin.lease-properties.*','admin.property-types.*']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-building"></em></span>
                            <span class="nk-menu-text">{{ __('Property Management')}}</span>
                        </a>
                        <ul class="nk-menu-sub">
                            <!-- Property Types -->
                            @can('manage property types')
                                <li class="nk-menu-item {{ active(['admin.property-types.*']) }}">
                                    <a href="{{ route('admin.property-types.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Property Types')}}</span>
                                    </a>
                                </li>
                            @endcan

                            <!-- For Rent -->
                            <li class="nk-menu-item has-sub {{ active(['admin.rental-properties.*','admin.rental-units.*']) }}">
                                <a href="#" class="nk-menu-link nk-menu-toggle">
                                    <span class="nk-menu-icon"><em class="icon ni ni-home"></em></span>
                                    <span class="nk-menu-text">{{ __('For Rent')}}</span>
                                </a>
                                <ul class="nk-menu-sub">
                                    @can('view rental property')
                                        <li class="nk-menu-item {{ active(['admin.rental-properties.index']) }}">
                                            <a href="{{ route('admin.rental-properties.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Rent Dashboard')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.rental-properties.all']) }}">
                                            <a href="{{ route('admin.rental-properties.all') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('All Rent Properties')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.rental-units.*']) }}">
                                            <a href="{{ route('admin.rental-units.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Manage Units')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.rental-properties.create']) }}">
                                            <a href="{{ route('admin.rental-properties.create') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Add Property')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.rental-properties.vacant']) }}">
                                            <a href="{{ route('admin.rental-properties.vacant') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Vacant Properties')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.rental-properties.occupied']) }}">
                                            <a href="{{ route('admin.rental-properties.occupied') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Occupied Properties')}}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul><!-- .nk-menu-sub -->
                            </li><!-- .nk-menu-item -->

                            <!-- For Sale -->
                            <li class="nk-menu-item has-sub {{ active(['admin.sale-properties.*','admin.property-offers.*']) }}">
                                <a href="#" class="nk-menu-link nk-menu-toggle">
                                    <span class="nk-menu-icon"><em class="icon ni ni-camera"></em></span>
                                    <span class="nk-menu-text">{{ __('For Sale')}}</span>
                                </a>
                                <ul class="nk-menu-sub">
                                    @can('view sale property')
                                        <li class="nk-menu-item {{ active(['admin.sale-properties.index']) }}">
                                            <a href="{{ route('admin.sale-properties.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Sale Dashboard')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.sale-properties.all']) }}">
                                            <a href="{{ route('admin.sale-properties.all') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('All Sale Properties')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.sale-properties.create']) }}">
                                            <a href="{{ route('admin.sale-properties.create') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Add Sale Property')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.property-offers.*']) }}">
                                            <a href="{{ route('admin.property-offers.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Property Offers')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.sale-properties.featured']) }}">
                                            <a href="{{ route('admin.sale-properties.featured') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Featured Properties')}}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul><!-- .nk-menu-sub -->
                            </li><!-- .nk-menu-item -->

                            <!-- For Lease -->
                            <li class="nk-menu-item has-sub {{ active(['admin.lease-properties.*','admin.lease-agreements.*']) }}">
                                <a href="#" class="nk-menu-link nk-menu-toggle">
                                    <span class="nk-menu-icon"><em class="icon ni ni-file-doc"></em></span>
                                    <span class="nk-menu-text">{{ __('For Lease')}}</span>
                                </a>
                                <ul class="nk-menu-sub">
                                    @can('view lease property')
                                        <li class="nk-menu-item {{ active(['admin.lease-properties.index']) }}">
                                            <a href="{{ route('admin.lease-properties.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Lease Dashboard')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.lease-properties.all']) }}">
                                            <a href="{{ route('admin.lease-properties.all') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('All Lease Properties')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.lease-properties.create']) }}">
                                            <a href="{{ route('admin.lease-properties.create') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Add Lease Property')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.lease-agreements.*']) }}">
                                            <a href="{{ route('admin.lease-agreements.index') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Lease Agreements')}}</span>
                                            </a>
                                        </li>
                                        <li class="nk-menu-item {{ active(['admin.lease-properties.available']) }}">
                                            <a href="{{ route('admin.lease-properties.available') }}" class="nk-menu-link">
                                                <span class="nk-menu-text">{{ __('Available Properties')}}</span>
                                            </a>
                                        </li>
                                    @endcan
                                </ul><!-- .nk-menu-sub -->
                            </li><!-- .nk-menu-item -->

                            <!-- Legacy Properties (Current System) -->
                            @can('view property')
                                <li class="nk-menu-item {{ active(['admin.properties.*']) }}">
                                    <a href="{{ route('admin.properties.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Legacy Properties')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view house')
                                <li class="nk-menu-item {{ active(['admin.houses.*']) }}">
                                    <a href="{{ route('admin.houses.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Legacy Houses')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <!-- Tenant Management -->
                    <li class="nk-menu-item has-sub {{ active(['admin.tenants.*','admin.archived-tenants.index']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-users"></em></span>
                            <span class="nk-menu-text">{{ __('Tenant Management')}}</span>
                        </a>
                        <ul class="nk-menu-sub">
                            @can('create tenant')
                                <li class="nk-menu-item {{ active('admin.tenants.create') }}">
                                    <a href="{{ route('admin.tenants.create') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Add Tenant')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view tenant')
                                <li class="nk-menu-item {{ active(['admin.tenants.index','admin.tenants.edit','admin.tenants.show']) }}">
                                    <a href="{{ route('admin.tenants.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('All Tenants')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view archived tenant')
                                <li class="nk-menu-item {{ active('admin.archived-tenants.index') }}">
                                    <a href="{{ route('admin.archived-tenants.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Archived Tenants')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <!-- Landlord Management -->
                    <li class="nk-menu-item has-sub {{ active(['admin.landlords.*']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-user-circle"></em></span>
                            <span class="nk-menu-text">{{ __('Landlord Management')}}</span>
                        </a>
                        <ul class="nk-menu-sub">
                            @can('create landlord')
                                <li class="nk-menu-item {{ active('admin.landlords.create') }}">
                                    <a href="{{ route('admin.landlords.create') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Add Landlord')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view landlord')
                                <li class="nk-menu-item {{ active('admin.landlords.index') }}">
                                    <a href="{{ route('admin.landlords.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Landlords Listing')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <!-- Lease Management -->
                    <li class="nk-menu-item has-sub {{ active(['admin.leases.*','admin.leases-history.*']) }}">
                        <a href="#" class="nk-menu-link nk-menu-toggle">
                            <span class="nk-menu-icon"><em class="icon ni ni-contact"></em></span>
                            <span class="nk-menu-text">{{ __('Lease Management')}}</span>
                        </a>
                        <ul class="nk-menu-sub">
                            @can('create lease')
                                <li class="nk-menu-item {{ active('admin.leases.create') }}">
                                    <a href="{{ route('admin.leases.create') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('New Lease')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view lease')
                                <li class="nk-menu-item {{ active(['admin.leases.index','admin.leases.edit','admin.leases.show']) }}">
                                    <a href="{{ route('admin.leases.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Active Leases')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view lease history')
                                <li class="nk-menu-item {{ active('admin.leases-history.index') }}">
                                    <a href="{{ route('admin.leases-history.index') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Lease History')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <!-- Maintenance Management -->
                    @can('manage maintenance')
                        <li class="nk-menu-item has-sub {{ active(['admin.maintenance.*']) }}">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon"><em class="icon ni ni-tools"></em></span>
                                <span class="nk-menu-text">{{ __('Maintenance')}}</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item {{ active(['admin.maintenance.requests']) }}">
                                    <a href="{{ route('admin.maintenance.requests') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Maintenance Requests')}}</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item {{ active(['admin.maintenance.schedule']) }}">
                                    <a href="{{ route('admin.maintenance.schedule') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Maintenance Schedule')}}</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item {{ active(['admin.maintenance.history']) }}">
                                    <a href="{{ route('admin.maintenance.history') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Maintenance History')}}</span>
                                    </a>
                                </li>
                            </ul><!-- .nk-menu-sub -->
                        </li><!-- .nk-menu-item -->
                    @endcan

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
                                    <a href="{{ route('admin.reports.landlord_income') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Landlord Income')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view property income report')
                                <li class="nk-menu-item {{ active('admin.reports.property_income') }}">
                                    <a href="{{ route('admin.reports.property_income') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Property Income')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view company income report')
                                <li class="nk-menu-item {{ active('admin.reports.company_income') }} ">
                                    <a href="{{ route('admin.reports.company_income') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Company Commission')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view landlord expenses report')
                                <li class="nk-menu-item {{ active('admin.reports.landlord_expenses') }}">
                                    <a href="{{ route('admin.reports.landlord_expenses') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Landlord Expenses')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view company expenses report')
                                <li class="nk-menu-item {{ active('admin.reports.company_expenses') }}">
                                    <a href="{{ route('admin.reports.company_expenses') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Company Expenses')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view expiring leases report')
                                <li class="nk-menu-item {{ active('admin.reports.expiring_leases') }}">
                                    <a href="{{ route('admin.reports.expiring_leases') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Expiring Leases')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view outstanding payments report')
                                <li class="nk-menu-item {{ active('admin.reports.outstanding_payments') }}">
                                    <a href="{{ route('admin.reports.outstanding_payments') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Outstanding Payments')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view maintenance reports')
                                <li class="nk-menu-item {{ active('admin.reports.maintenance') }}">
                                    <a href="{{ route('admin.reports.maintenance') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Maintenance Reports')}}</span>
                                    </a>
                                </li>
                            @endcan

                            @can('view occupancy reports')
                                <li class="nk-menu-item {{ active('admin.reports.occupancy') }}">
                                    <a href="{{ route('admin.reports.occupancy') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Occupancy Reports')}}</span>
                                    </a>
                                </li>
                            @endcan
                        </ul><!-- .nk-menu-sub -->
                    </li><!-- .nk-menu-item -->

                    <!-- Settings -->
                    @can('view settings')
                        <li class="nk-menu-item has-sub {{ active(['admin.settings.*']) }}">
                            <a href="#" class="nk-menu-link nk-menu-toggle">
                                <span class="nk-menu-icon"><em class="icon ni ni-setting"></em></span>
                                <span class="nk-menu-text">{{ __('Settings')}}</span>
                            </a>
                            <ul class="nk-menu-sub">
                                <li class="nk-menu-item {{ active('admin.settings.general') }}">
                                    <a href="{{ route('admin.settings.general') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('General Settings')}}</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item {{ active('admin.settings.property') }}">
                                    <a href="{{ route('admin.settings.property') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Property Settings')}}</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item {{ active('admin.settings.financial') }}">
                                    <a href="{{ route('admin.settings.financial') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('Financial Settings')}}</span>
                                    </a>
                                </li>
                                <li class="nk-menu-item {{ active('admin.settings.system') }}">
                                    <a href="{{ route('admin.settings.system') }}" class="nk-menu-link">
                                        <span class="nk-menu-text">{{ __('System Settings')}}</span>
                                    </a>
                                </li>
                            </ul><!-- .nk-menu-sub -->
                        </li><!-- .nk-menu-item -->
                    @endcan

                </ul><!-- .nk-menu -->
            </div><!-- .nk-sidebar-menu -->
        </div><!-- .nk-sidebar-content -->
    </div><!-- .nk-sidebar-element -->
</div>
