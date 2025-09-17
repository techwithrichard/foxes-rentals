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
                            <h3 class="nk-block-title page-title">{{ __('User Management Dashboard') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Comprehensive overview of all users, roles, and system activity') }}</p>
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
                                            <a href="{{ route('admin.users-management.create') }}" class="btn btn-primary">
                                                <em class="icon ni ni-plus"></em>
                                                <span>{{ __('Create User') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.user-management.analytics') }}" class="btn btn-outline-primary">
                                                <em class="icon ni ni-chart-bar"></em>
                                                <span>{{ __('View Analytics') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-primary bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-users text-primary" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Total Users') }}</h6>
                                            <h4 class="mb-0">{{ number_format($stats['total_users']) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-user-circle text-success" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Landlords') }}</h6>
                                            <h4 class="mb-0">{{ number_format($stats['total_landlords']) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-users text-info" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Tenants') }}</h6>
                                            <h4 class="mb-0">{{ number_format($stats['total_tenants']) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-shield-check text-warning" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Active Users') }}</h6>
                                            <h4 class="mb-0">{{ number_format($stats['active_users']) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Stats Row -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-md-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-danger bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-clock text-danger" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Pending Users') }}</h6>
                                            <h4 class="mb-0">{{ number_format($stats['pending_users']) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-secondary bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-shield text-secondary" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Total Roles') }}</h6>
                                            <h4 class="mb-0">{{ number_format($stats['total_roles']) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-dark bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-key text-dark" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Total Permissions') }}</h6>
                                            <h4 class="mb-0">{{ number_format($stats['total_permissions']) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Users and Activity -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Recent Users -->
                        <div class="col-lg-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('Recent Users') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Email') }}</th>
                                                    <th>{{ __('Role') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Joined') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($recentUsers as $user)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                                <em class="icon ni ni-user text-primary"></em>
                                                            </div>
                                                            <span class="fw-medium">{{ $user->name }}</span>
                                                        </div>
                                                    </td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>
                                                        @if($user->roles->count() > 0)
                                                            @foreach($user->roles as $role)
                                                                <span class="badge bg-primary">{{ ucfirst($role->name) }}</span>
                                                            @endforeach
                                                        @else
                                                            <span class="text-muted">{{ __('No Role') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($user->email_verified_at)
                                                            <span class="badge bg-success">{{ __('Active') }}</span>
                                                        @else
                                                            <span class="badge bg-warning">{{ __('Pending') }}</span>
                                                        @endif
                                                    </td>
                                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                                </tr>
                                                @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-4">
                                                        {{ __('No users found') }}
                                                    </td>
                                                </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Activity Summary -->
                        <div class="col-lg-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('User Activity') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-user-plus text-success"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('New This Month') }}</h6>
                                            <span class="text-muted">{{ number_format($userActivity['new_this_month']) }} {{ __('users') }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-info bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-activity text-info"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Active This Month') }}</h6>
                                            <span class="text-muted">{{ number_format($userActivity['active_this_month']) }} {{ __('users') }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.user-management.analytics') }}" class="btn btn-outline-primary btn-sm">
                                            <em class="icon ni ni-chart-bar me-1"></em>
                                            {{ __('View Detailed Analytics') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Quick Actions') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.landlords.create') }}" class="btn btn-outline-primary w-100">
                                        <em class="icon ni ni-user-plus me-2"></em>
                                        {{ __('Add Landlord') }}
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.tenants.create') }}" class="btn btn-outline-success w-100">
                                        <em class="icon ni ni-user-plus me-2"></em>
                                        {{ __('Add Tenant') }}
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.roles-management.create') }}" class="btn btn-outline-info w-100">
                                        <em class="icon ni ni-shield-plus me-2"></em>
                                        {{ __('Create Role') }}
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.user-management.settings') }}" class="btn btn-outline-secondary w-100">
                                        <em class="icon ni ni-setting me-2"></em>
                                        {{ __('User Settings') }}
                                    </a>
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
