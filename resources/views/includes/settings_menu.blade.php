<!-- .card-inner -->
<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg"
     data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <div class="card-inner">
            <h3 class="nk-block-title page-title">{{ __('Settings')}}</h3>
            <div class="nk-block-des text-soft">
                <p>{{ __('Here you can change and edit your needs')}}</p>
            </div>
        </div><!-- .card-inner -->
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <li class="{{ active('admin.settings.index') }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <em class="icon ni ni-user-fill-c"></em>
                        <span>{{ __('General')}}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.appearance') }}">
                    <a href="{{ route('admin.settings.appearance') }}">
                        <em
                            class="icon ni ni-lock-alt-fill"></em>
                        <span>{{ __('Appearance')}}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.property_types') }}">
                    <a href="{{ route('admin.settings.property_types') }}">
                        <em
                            class="icon ni ni-shield-star-fill"></em>
                        <span>{{ __('Property Types')}}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.house_types') }}">
                    <a href="{{ route('admin.settings.house_types') }}">
                        <em
                            class="icon ni ni-activity-round-fill"></em>
                        <span>{{ __('House Types')}}</span>
                    </a>
                </li>

                <li class="{{ active('admin.settings.expense_types') }}">
                    <a href="{{ route('admin.settings.expense_types') }}">
                        <em
                            class="icon ni ni-activity-round-fill"></em>
                        <span>{{ __('Expense Types')}}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.payment_methods') }}">
                    <a href="{{ route('admin.settings.payment_methods') }}">
                        <em
                            class="icon ni ni-bitcoin-cash"></em>
                        <span>{{ __('Payment Methods')}}</span>
                    </a>
                </li>

                <li class="{{ active('admin.settings.company_details') }}">
                    <a href="{{ route('admin.settings.company_details') }}">
                        <em
                            class="icon ni ni-list-index-fill"></em>
                        <span>{{ __('Company Details')}}</span>
                    </a>
                </li>
            </ul>
        </div><!-- .card-inner -->
    </div><!-- .card-inner-group -->
</div>
<!-- card-aside -->
