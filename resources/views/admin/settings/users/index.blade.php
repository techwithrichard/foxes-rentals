@extends('layouts.main')

@section('content')
    <div class="nk-content">
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
                                                <h5 class="nk-block-title">{{ __('User Settings') }}</h5>
                                                <p>{{ __('Manage user roles, permissions, profiles, and account settings') }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="nk-block">
                                        <div class="row g-3">
                                            <!-- Roles & Permissions Card -->
                                            <div class="col-lg-6">
                                                <div class="card card-bordered h-100">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">{{ __('Roles & Permissions') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-text">
                                                            <p class="text-soft">{{ __('Configure user roles, permissions, and access controls') }}</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <a href="{{ route('admin.settings.users.roles') }}" class="btn btn-primary btn-sm me-2">
                                                                <em class="icon ni ni-users"></em>
                                                                <span>{{ __('Manage Roles') }}</span>
                                                            </a>
                                                            <a href="{{ route('admin.settings.users.permissions') }}" class="btn btn-outline-primary btn-sm">
                                                                <em class="icon ni ni-shield-check"></em>
                                                                <span>{{ __('Manage Permissions') }}</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- User Profiles Card -->
                                            <div class="col-lg-6">
                                                <div class="card card-bordered h-100">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">{{ __('User Profiles') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-text">
                                                            <p class="text-soft">{{ __('Configure profile requirements and user information settings') }}</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <a href="{{ route('admin.settings.users.profiles') }}" class="btn btn-primary btn-sm">
                                                                <em class="icon ni ni-user-fill"></em>
                                                                <span>{{ __('Profile Settings') }}</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Account Security Card -->
                                            <div class="col-lg-6">
                                                <div class="card card-bordered h-100">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">{{ __('Account Security') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-text">
                                                            <p class="text-soft">{{ __('Configure password policies and account security settings') }}</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <a href="{{ route('admin.settings.users.security') }}" class="btn btn-primary btn-sm">
                                                                <em class="icon ni ni-lock-alt"></em>
                                                                <span>{{ __('Security Settings') }}</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- User Registration Card -->
                                            <div class="col-lg-6">
                                                <div class="card card-bordered h-100">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">{{ __('User Registration') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-text">
                                                            <p class="text-soft">{{ __('Configure registration process and verification requirements') }}</p>
                                                        </div>
                                                        <div class="mt-3">
                                                            <a href="{{ route('admin.settings.users.registration') }}" class="btn btn-primary btn-sm">
                                                                <em class="icon ni ni-user-add"></em>
                                                                <span>{{ __('Registration Settings') }}</span>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Quick Stats -->
                                        <div class="row g-3 mt-4">
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="card card-bordered">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">{{ __('Total Users') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-amount">
                                                            <span class="amount">{{ \App\Models\User::count() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="card card-bordered">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">{{ __('Active Roles') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-amount">
                                                            <span class="amount">{{ \Spatie\Permission\Models\Role::count() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="card card-bordered">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">{{ __('Permissions') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-amount">
                                                            <span class="amount">{{ \Spatie\Permission\Models\Permission::count() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-3 col-sm-6">
                                                <div class="card card-bordered">
                                                    <div class="card-inner">
                                                        <div class="card-title-group">
                                                            <div class="card-title">
                                                                <h6 class="title">{{ __('Settings') }}</h6>
                                                            </div>
                                                        </div>
                                                        <div class="card-amount">
                                                            <span class="amount">{{ \App\Models\SettingsItem::whereHas('group', function($q) { $q->where('slug', 'like', '%user%'); })->count() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @include('includes.user_settings_menu')
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
