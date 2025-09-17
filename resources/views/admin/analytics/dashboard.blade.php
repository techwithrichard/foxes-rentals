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
                            <h3 class="nk-block-title page-title">{{ __('Analytics Dashboard') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Comprehensive analytics and insights for your rental management system') }}</p>
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
                                            <a href="{{ route('admin.analytics.export') }}" class="btn btn-outline-primary">
                                                <em class="icon ni ni-download"></em>
                                                <span>{{ __('Export Data') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Overview Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- User Analytics -->
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
                                            <h4 class="mb-0">{{ number_format($analytics['user_growth']['total_users']) }}</h4>
                                            <small class="text-success">
                                                <em class="icon ni ni-arrow-up"></em>
                                                {{ $analytics['user_growth']['monthly_growth'] }} {{ __('this month') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Analytics -->
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-success bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-tranx text-success" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Total Revenue') }}</h6>
                                            <h4 class="mb-0">{{ number_format($analytics['financial_performance']['total_revenue']) }}</h4>
                                            <small class="text-success">
                                                <em class="icon ni ni-arrow-up"></em>
                                                {{ number_format($analytics['financial_performance']['monthly_revenue']) }} {{ __('this month') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Analytics -->
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-info bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-building text-info" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Total Properties') }}</h6>
                                            <h4 class="mb-0">{{ number_format($analytics['property_performance']['total_properties']) }}</h4>
                                            <small class="text-info">
                                                <em class="icon ni ni-percentage"></em>
                                                {{ $analytics['property_performance']['occupancy_rate'] }}% {{ __('occupied') }}
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Performance -->
                        <div class="col-md-3 col-sm-6">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <div class="bg-warning bg-opacity-10 rounded-3 p-3">
                                                <em class="icon ni ni-speedometer text-warning" style="font-size: 2rem;"></em>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6 class="text-muted mb-1">{{ __('Memory Usage') }}</h6>
                                            <h4 class="mb-0">{{ number_format($analytics['system_performance']['memory_usage'] / 1024 / 1024, 2) }}MB</h4>
                                            <small class="text-warning">
                                                <em class="icon ni ni-clock"></em>
                                                {{ number_format($analytics['system_performance']['execution_time'], 3) }}s
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics Sections -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- User Analytics -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('User Analytics') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-user-plus text-primary"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('New Users This Month') }}</h6>
                                            <span class="text-muted">{{ $analytics['user_growth']['monthly_growth'] }} {{ __('users') }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-check-circle text-success"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Verified Users') }}</h6>
                                            <span class="text-muted">{{ $analytics['user_growth']['verified_users'] }} {{ __('users') }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.analytics.users') }}" class="btn btn-outline-primary btn-sm">
                                            <em class="icon ni ni-chart-bar me-1"></em>
                                            {{ __('View User Analytics') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Analytics -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('Financial Analytics') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-tranx text-success"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Monthly Revenue') }}</h6>
                                            <span class="text-muted">{{ number_format($analytics['financial_performance']['monthly_revenue']) }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-clock text-warning"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Pending Payments') }}</h6>
                                            <span class="text-muted">{{ number_format($analytics['financial_performance']['pending_payments']) }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.analytics.financial') }}" class="btn btn-outline-success btn-sm">
                                            <em class="icon ni ni-chart-bar me-1"></em>
                                            {{ __('View Financial Analytics') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property and System Analytics -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Property Analytics -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('Property Analytics') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-info bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-building text-info"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Total Properties') }}</h6>
                                            <span class="text-muted">{{ $analytics['property_performance']['total_properties'] }} {{ __('properties') }}</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-home text-success"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Occupied Properties') }}</h6>
                                            <span class="text-muted">{{ $analytics['property_performance']['occupied_properties'] }} {{ __('properties') }}</span>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.analytics.properties') }}" class="btn btn-outline-info btn-sm">
                                            <em class="icon ni ni-chart-bar me-1"></em>
                                            {{ __('View Property Analytics') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Analytics -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('System Analytics') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-warning bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-speedometer text-warning"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Memory Usage') }}</h6>
                                            <span class="text-muted">{{ number_format($analytics['system_performance']['memory_usage'] / 1024 / 1024, 2) }}MB</span>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-danger bg-opacity-10 rounded-3 p-2 me-3">
                                            <em class="icon ni ni-clock text-danger"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ __('Execution Time') }}</h6>
                                            <span class="text-muted">{{ number_format($analytics['system_performance']['execution_time'], 3) }}s</span>
                                        </div>
                                    </div>
                                    <div class="mt-3">
                                        <a href="{{ route('admin.analytics.system') }}" class="btn btn-outline-warning btn-sm">
                                            <em class="icon ni ni-server me-1"></em>
                                            {{ __('View System Analytics') }}
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
                                    <a href="{{ route('admin.analytics.users') }}" class="btn btn-outline-primary w-100">
                                        <em class="icon ni ni-users me-2"></em>
                                        {{ __('User Analytics') }}
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.analytics.financial') }}" class="btn btn-outline-success w-100">
                                        <em class="icon ni ni-tranx me-2"></em>
                                        {{ __('Financial Analytics') }}
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.analytics.properties') }}" class="btn btn-outline-info w-100">
                                        <em class="icon ni ni-building me-2"></em>
                                        {{ __('Property Analytics') }}
                                    </a>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <a href="{{ route('admin.analytics.performance') }}" class="btn btn-outline-warning w-100">
                                        <em class="icon ni ni-speedometer me-2"></em>
                                        {{ __('Performance Analytics') }}
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
