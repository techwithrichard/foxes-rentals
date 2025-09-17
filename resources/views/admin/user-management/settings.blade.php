@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <!-- Page Header -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ __('User Management Settings') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Configure user registration, verification, and security settings') }}</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-menu-alt-r"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        <li>
                                            <a href="{{ route('admin.user-management.dashboard') }}" class="btn btn-outline-primary">
                                                <em class="icon ni ni-dashboard"></em>
                                                <span>{{ __('Back to Dashboard') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(session()->has('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <em class="icon ni ni-check-circle me-2"></em>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Settings Form -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('User Settings') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.user-management.settings.update') }}" method="POST">
                                @csrf
                                
                                <!-- User Registration Settings -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('User Registration Settings') }}</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Enable User Registration') }}</label>
                                            <div class="form-control-wrap">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="user_registration_enabled" 
                                                           name="user_registration_enabled" 
                                                           value="1"
                                                           {{ $settings['user_registration_enabled'] ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="user_registration_enabled">
                                                        {{ __('Allow new users to register') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Email Verification Required') }}</label>
                                            <div class="form-control-wrap">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" 
                                                           class="custom-control-input" 
                                                           id="email_verification_required" 
                                                           name="email_verification_required" 
                                                           value="1"
                                                           {{ $settings['email_verification_required'] ? 'checked' : '' }}>
                                                    <label class="custom-control-label" for="email_verification_required">
                                                        {{ __('Require email verification') }}
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Default Settings -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Default Settings') }}</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Default User Role') }}</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" name="default_user_role" required>
                                                    <option value="tenant" {{ $settings['default_user_role'] == 'tenant' ? 'selected' : '' }}>
                                                        {{ __('Tenant') }}
                                                    </option>
                                                    <option value="landlord" {{ $settings['default_user_role'] == 'landlord' ? 'selected' : '' }}>
                                                        {{ __('Landlord') }}
                                                    </option>
                                                    <option value="maintainer" {{ $settings['default_user_role'] == 'maintainer' ? 'selected' : '' }}>
                                                        {{ __('Maintainer') }}
                                                    </option>
                                                    <option value="accountant" {{ $settings['default_user_role'] == 'accountant' ? 'selected' : '' }}>
                                                        {{ __('Accountant') }}
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('User Invitation Expiry (Days)') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" 
                                                       class="form-control" 
                                                       name="user_invitation_expiry_days" 
                                                       value="{{ $settings['user_invitation_expiry_days'] }}"
                                                       min="1" 
                                                       max="365" 
                                                       required>
                                                <div class="form-note">{{ __('Number of days before invitation expires') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Security Settings -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Security Settings') }}</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Max Login Attempts') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" 
                                                       class="form-control" 
                                                       name="max_login_attempts" 
                                                       value="{{ $settings['max_login_attempts'] }}"
                                                       min="1" 
                                                       max="10" 
                                                       required>
                                                <div class="form-note">{{ __('Maximum failed login attempts before lockout') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Session Timeout (Minutes)') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" 
                                                       class="form-control" 
                                                       name="session_timeout_minutes" 
                                                       value="{{ $settings['session_timeout_minutes'] }}"
                                                       min="15" 
                                                       max="480" 
                                                       required>
                                                <div class="form-note">{{ __('Session timeout in minutes (15-480)') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <em class="icon ni ni-save me-2"></em>
                                                {{ __('Save Settings') }}
                                            </button>
                                            <a href="{{ route('admin.user-management.dashboard') }}" class="btn btn-outline-secondary">
                                                <em class="icon ni ni-arrow-left me-2"></em>
                                                {{ __('Cancel') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Current Settings Overview -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Current Settings Overview') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-gs">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-user-plus text-primary"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('User Registration') }}</h6>
                                            <span class="text-muted">
                                                {{ $settings['user_registration_enabled'] ? 'Enabled' : 'Disabled' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-mail text-success"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Email Verification') }}</h6>
                                            <span class="text-muted">
                                                {{ $settings['email_verification_required'] ? 'Required' : 'Optional' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-info bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-shield text-info"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Default Role') }}</h6>
                                            <span class="text-muted">{{ ucfirst($settings['default_user_role']) }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-clock text-warning"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Invitation Expiry') }}</h6>
                                            <span class="text-muted">{{ $settings['user_invitation_expiry_days'] }} {{ __('days') }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
