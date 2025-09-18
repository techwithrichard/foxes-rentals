<!-- .card-inner -->
<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg"
     data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <div class="card-inner">
            <h3 class="nk-block-title page-title">{{ __('Advanced Settings')}}</h3>
            <div class="nk-block-des text-soft">
                <p>{{ __('Comprehensive system configuration and management')}}</p>
            </div>
        </div><!-- .card-inner -->
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <li class="{{ active('admin.settings.advanced.index') }}">
                    <a href="{{ route('admin.settings.advanced.index') }}">
                        <em class="icon ni ni-settings"></em>
                        <span>{{ __('Dashboard')}}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.advanced.category', 'security-authentication') }}">
                    <a href="{{ route('admin.settings.advanced.category', 'security-authentication') }}">
                        <em class="icon ni ni-shield-check"></em>
                        <span>{{ __('Security & Authentication')}}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.advanced.category', 'business-configuration') }}">
                    <a href="{{ route('admin.settings.advanced.category', 'business-configuration') }}">
                        <em class="icon ni ni-building"></em>
                        <span>{{ __('Business Configuration')}}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.advanced.category', 'notification-management') }}">
                    <a href="{{ route('admin.settings.advanced.category', 'notification-management') }}">
                        <em class="icon ni ni-notification"></em>
                        <span>{{ __('Notification Management')}}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.advanced.category', 'system-performance') }}">
                    <a href="{{ route('admin.settings.advanced.category', 'system-performance') }}">
                        <em class="icon ni ni-speedometer"></em>
                        <span>{{ __('System Performance')}}</span>
                    </a>
                </li>
                <li class="divider"></li>
                <li>
                    <a href="{{ route('admin.settings.index') }}">
                        <em class="icon ni ni-arrow-left"></em>
                        <span>{{ __('Back to Basic Settings')}}</span>
                    </a>
                </li>
            </ul>
        </div><!-- .card-inner -->
    </div><!-- .card-inner-group -->
</div>
<!-- card-aside -->
