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
                            <h3 class="nk-block-title page-title">{{ __('Property Dashboard') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Overview of your property portfolio performance and key metrics') }}</p>
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
                                            <a href="{{ route('admin.property-dashboard.analytics') }}" class="btn btn-outline-primary">
                                                <em class="icon ni ni-chart-line"></em>
                                                <span>{{ __('Advanced Analytics') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">
                                                <em class="icon ni ni-plus"></em>
                                                <span>{{ __('Add Property') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Key Metrics Cards -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Total Properties -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Total Properties') }}</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" title="Total number of properties in your portfolio"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">{{ number_format($totalProperties) }}</span>
                                        <span class="change up text-success">
                                            <em class="icon ni ni-arrow-long-up"></em>
                                            <span>{{ __('+12%') }}</span>
                                        </span>
                                    </div>
                                    <div class="card-stats">
                                        <div class="card-stats-group">
                                            <div class="card-stats-item">
                                                <span class="card-stats-label">{{ __('Occupied') }}</span>
                                                <span class="card-stats-value">{{ number_format($occupiedProperties) }}</span>
                                            </div>
                                            <div class="card-stats-item">
                                                <span class="card-stats-label">{{ __('Vacant') }}</span>
                                                <span class="card-stats-value">{{ number_format($vacantProperties) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Occupancy Rate -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Occupancy Rate') }}</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" title="Percentage of occupied properties"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">{{ $occupancyRate }}%</span>
                                        <span class="change up text-success">
                                            <em class="icon ni ni-arrow-long-up"></em>
                                            <span>{{ __('+5%') }}</span>
                                        </span>
                                    </div>
                                    <div class="card-stats">
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-success" style="width: {{ $occupancyRate }}%"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Total Revenue -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Total Revenue') }}</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" title="Total rental revenue from all properties"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">{{ setting('currency_symbol') }} {{ number_format($totalRentRevenue, 2) }}</span>
                                        <span class="change up text-success">
                                            <em class="icon ni ni-arrow-long-up"></em>
                                            <span>{{ __('+8%') }}</span>
                                        </span>
                                    </div>
                                    <div class="card-stats">
                                        <div class="card-stats-group">
                                            <div class="card-stats-item">
                                                <span class="card-stats-label">{{ __('This Month') }}</span>
                                                <span class="card-stats-value">{{ setting('currency_symbol') }} {{ number_format($monthlyRentRevenue, 2) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Types -->
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Property Types') }}</h6>
                                        </div>
                                        <div class="card-tools">
                                            <em class="card-hint icon ni ni-help-fill" data-bs-toggle="tooltip" data-bs-placement="left" title="Distribution of property types"></em>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">{{ $totalRentalProperties }}</span>
                                        <span class="change text-muted">
                                            <span>{{ __('Rental') }}</span>
                                        </span>
                                    </div>
                                    <div class="card-stats">
                                        <div class="card-stats-group">
                                            <div class="card-stats-item">
                                                <span class="card-stats-label">{{ __('For Sale') }}</span>
                                                <span class="card-stats-value">{{ $totalSaleProperties }}</span>
                                            </div>
                                            <div class="card-stats-item">
                                                <span class="card-stats-label">{{ __('For Lease') }}</span>
                                                <span class="card-stats-value">{{ $totalLeaseProperties }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts and Analytics -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Property Performance Chart -->
                        <div class="col-lg-8">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Property Performance') }}</h6>
                                        </div>
                                        <div class="card-tools">
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-more-h"></em>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-end">
                                                    <ul class="link-list-opt no-bdr">
                                                        <li><a href="#"><span>{{ __('Last 7 Days') }}</span></a></li>
                                                        <li><a href="#"><span>{{ __('Last 30 Days') }}</span></a></li>
                                                        <li><a href="#"><span>{{ __('Last 90 Days') }}</span></a></li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="propertyPerformanceChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Status Distribution -->
                        <div class="col-lg-4">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Property Status') }}</h6>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="chart-container">
                                            <canvas id="propertyStatusChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <!-- Recent Properties -->
                        <div class="col-lg-6">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Recent Properties') }}</h6>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-primary btn-sm">
                                                <span>{{ __('View All') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="nk-tb-list">
                                            @forelse($recentProperties as $property)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-primary">
                                                            <em class="icon ni ni-building"></em>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="tb-lead">{{ $property->name }}</span>
                                                            <span class="date">{{ $property->created_at->diffForHumans() }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span class="tb-status">
                                                        @if($property->is_vacant)
                                                            <span class="badge badge-dot badge-warning">{{ __('Vacant') }}</span>
                                                        @else
                                                            <span class="badge badge-dot badge-success">{{ __('Occupied') }}</span>
                                                        @endif
                                                    </span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span class="tb-amount">{{ setting('currency_symbol') }} {{ number_format($property->rent ?? 0, 2) }}</span>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <span class="text-muted">{{ __('No recent properties found') }}</span>
                                                </div>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Recent Leases -->
                        <div class="col-lg-6">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-3">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Recent Leases') }}</h6>
                                        </div>
                                        <div class="card-tools">
                                            <a href="{{ route('admin.leases.index') }}" class="btn btn-outline-primary btn-sm">
                                                <span>{{ __('View All') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="card-body">
                                        <div class="nk-tb-list">
                                            @forelse($recentLeases as $lease)
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <div class="user-card">
                                                        <div class="user-avatar bg-success">
                                                            <em class="icon ni ni-user"></em>
                                                        </div>
                                                        <div class="user-info">
                                                            <span class="tb-lead">{{ $lease->tenant->name ?? 'N/A' }}</span>
                                                            <span class="date">{{ $lease->start_date->format('M d, Y') }}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span class="tb-amount">{{ setting('currency_symbol') }} {{ number_format($lease->rent, 2) }}</span>
                                                </div>
                                                <div class="nk-tb-col tb-col-md">
                                                    <span class="tb-status">
                                                        <span class="badge badge-dot badge-success">{{ __('Active') }}</span>
                                                    </span>
                                                </div>
                                            </div>
                                            @empty
                                            <div class="nk-tb-item">
                                                <div class="nk-tb-col">
                                                    <span class="text-muted">{{ __('No recent leases found') }}</span>
                                                </div>
                                            </div>
                                            @endforelse
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

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Property Performance Chart
    const performanceCtx = document.getElementById('propertyPerformanceChart').getContext('2d');
    new Chart(performanceCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 15000, 18000, 16000, 20000, 22000],
                borderColor: '#6576ff',
                backgroundColor: 'rgba(101, 118, 255, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.1)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Property Status Chart
    const statusCtx = document.getElementById('propertyStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Occupied', 'Vacant', 'Maintenance'],
            datasets: [{
                data: [{{ $occupiedProperties }}, {{ $vacantProperties }}, 0],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 20,
                        usePointStyle: true
                    }
                }
            }
        }
    });
});
</script>
@endpush
