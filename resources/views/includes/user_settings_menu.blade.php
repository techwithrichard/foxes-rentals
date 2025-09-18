<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg"
     data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <div class="card-inner">
            <h3 class="nk-block-title page-title">{{ __('User Settings') }}</h3>
            <div class="nk-block-des text-soft">
                <p>{{ __('Manage user roles, permissions, and account settings') }}</p>
            </div>
        </div><!-- .card-inner -->
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <li class="{{ active('admin.settings.users.index') }}">
                    <a href="{{ route('admin.settings.users.index') }}">
                        <em class="icon ni ni-dashboard"></em>
                        <span>{{ __('Overview') }}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.users.roles') }}">
                    <a href="{{ route('admin.settings.users.roles') }}">
                        <em class="icon ni ni-users"></em>
                        <span>{{ __('Roles') }}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.users.permissions') }}">
                    <a href="{{ route('admin.settings.users.permissions') }}">
                        <em class="icon ni ni-shield-check"></em>
                        <span>{{ __('Permissions') }}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.users.profiles') }}">
                    <a href="{{ route('admin.settings.users.profiles') }}">
                        <em class="icon ni ni-user-fill"></em>
                        <span>{{ __('Profiles') }}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.users.security') }}">
                    <a href="{{ route('admin.settings.users.security') }}">
                        <em class="icon ni ni-lock-alt"></em>
                        <span>{{ __('Security') }}</span>
                    </a>
                </li>
                <li class="{{ active('admin.settings.users.registration') }}">
                    <a href="{{ route('admin.settings.users.registration') }}">
                        <em class="icon ni ni-user-add"></em>
                        <span>{{ __('Registration') }}</span>
                    </a>
                </li>
            </ul>
        </div><!-- .card-inner -->
    </div><!-- .card-inner-group -->
</div>
<!-- card-aside -->
