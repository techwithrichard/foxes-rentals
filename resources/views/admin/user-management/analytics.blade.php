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
                            <h3 class="nk-block-title page-title">{{ __('User Analytics') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Comprehensive analytics and insights about user behavior and system usage') }}</p>
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

                <!-- User Growth Chart -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('User Growth Over Time') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Month') }}</th>
                                            <th class="text-center">{{ __('Total Users') }}</th>
                                            <th class="text-center">{{ __('Landlords') }}</th>
                                            <th class="text-center">{{ __('Tenants') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($userGrowth as $growth)
                                        <tr>
                                            <td class="fw-medium">{{ $growth['month'] }}</td>
                                            <td class="text-center">
                                                <span class="badge bg-primary">{{ number_format($growth['users']) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-success">{{ number_format($growth['landlords']) }}</span>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-info">{{ number_format($growth['tenants']) }}</span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Role Distribution and Activity Metrics -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Role Distribution -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('Role Distribution') }}</h5>
                                </div>
                                <div class="card-body">
                                    @forelse($roleDistribution as $role)
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-shield text-primary"></em>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ ucfirst($role->name) }}</h6>
                                            <span class="text-muted">{{ $role->users_count }} {{ __('users') }}</span>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-primary">{{ $role->users_count }}</span>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-4">
                                        <div class="bg-light rounded-circle p-3 d-inline-block mb-3">
                                            <em class="icon ni ni-shield text-muted" style="font-size: 2rem;"></em>
                                        </div>
                                        <h6 class="mb-2">{{ __('No Roles Found') }}</h6>
                                        <p class="text-muted">{{ __('There are no roles in the system yet.') }}</p>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>

                        <!-- Activity Metrics -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('Activity Metrics') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="mb-4">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                                <em class="icon ni ni-activity text-success"></em>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ __('Recent Logins (7 days)') }}</h6>
                                                <span class="text-muted">{{ number_format($activityMetrics['recent_logins']) }} {{ __('users') }}</span>
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="mb-3">{{ __('Most Active Users') }}</h6>
                                    @forelse($activityMetrics['most_active_users'] as $user)
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="bg-info bg-opacity-10 rounded-circle p-2 me-3">
                                            <em class="icon ni ni-user text-info"></em>
                                        </div>
                                        <div class="flex-grow-1">
                                            <span class="fw-medium">{{ $user->name }}</span>
                                            <br>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-info">{{ $user->login_activities_count }}</span>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="text-center py-3">
                                        <span class="text-muted">{{ __('No activity data available') }}</span>
                                    </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- User Status Overview -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('User Status Overview') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-gs">
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center">
                                        <div class="bg-success bg-opacity-10 rounded-3 p-3 d-inline-block mb-2">
                                            <em class="icon ni ni-check-circle text-success" style="font-size: 2rem;"></em>
                                        </div>
                                        <h5 class="mb-1">{{ __('Verified Users') }}</h5>
                                        <p class="text-muted">{{ __('Users with verified email addresses') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center">
                                        <div class="bg-warning bg-opacity-10 rounded-3 p-3 d-inline-block mb-2">
                                            <em class="icon ni ni-clock text-warning" style="font-size: 2rem;"></em>
                                        </div>
                                        <h5 class="mb-1">{{ __('Pending Users') }}</h5>
                                        <p class="text-muted">{{ __('Users awaiting email verification') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center">
                                        <div class="bg-info bg-opacity-10 rounded-3 p-3 d-inline-block mb-2">
                                            <em class="icon ni ni-activity text-info" style="font-size: 2rem;"></em>
                                        </div>
                                        <h5 class="mb-1">{{ __('Active Users') }}</h5>
                                        <p class="text-muted">{{ __('Users who logged in recently') }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center">
                                        <div class="bg-secondary bg-opacity-10 rounded-3 p-3 d-inline-block mb-2">
                                            <em class="icon ni ni-user text-secondary" style="font-size: 2rem;"></em>
                                        </div>
                                        <h5 class="mb-1">{{ __('Inactive Users') }}</h5>
                                        <p class="text-muted">{{ __('Users who haven\'t logged in recently') }}</p>
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
                                    <a href="{{ route('admin.users-management.create') }}" class="btn btn-outline-primary w-100">
                                        <em class="icon ni ni-user-plus me-2"></em>
                                        {{ __('Create User') }}
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.roles-management.create') }}" class="btn btn-outline-success w-100">
                                        <em class="icon ni ni-shield-plus me-2"></em>
                                        {{ __('Create Role') }}
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.settings.permissions.create') }}" class="btn btn-outline-info w-100">
                                        <em class="icon ni ni-key-plus me-2"></em>
                                        {{ __('Create Permission') }}
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
