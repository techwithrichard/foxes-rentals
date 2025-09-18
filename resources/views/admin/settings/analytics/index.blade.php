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
                                            <h5 class="nk-block-title">{{ __('Analytics & Reporting Dashboard') }}</h5>
                                            <span>{{ __('Comprehensive analytics, insights, and reporting for your property management business') }}</span>
                                        </div>
                                        <div class="nk-block-head-content align-self-start d-lg-none">
                                            <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                                               data-target="analyticsAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                        </div>
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-primary" onclick="generateQuickReport()">
                                                                <em class="icon ni ni-file-text"></em>
                                                                <span>{{ __('Quick Report') }}</span>
                                                            </button>
                                                        </li>
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-outline-success" onclick="scheduleReport()">
                                                                <em class="icon ni ni-calendar"></em>
                                                                <span>{{ __('Schedule Report') }}</span>
                                                            </button>
                                                        </li>
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-outline-info" onclick="refreshAnalytics()">
                                                                <em class="icon ni ni-reload"></em>
                                                                <span>{{ __('Refresh') }}</span>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Analytics Navigation Tabs -->
                                <div class="nk-block">
                                    <ul class="nav nav-tabs nav-tabs-s2 nav-tabs-s2-simple">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#overview">
                                                <span>{{ __('Overview') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#property-analytics">
                                                <span>{{ __('Property Analytics') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#financial-analytics">
                                                <span>{{ __('Financial Analytics') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tenant-analytics">
                                                <span>{{ __('Tenant Analytics') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#maintenance-analytics">
                                                <span>{{ __('Maintenance Analytics') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#reports">
                                                <span>{{ __('Reports') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>

                                <!-- Tab Content -->
                                <div class="nk-block">
                                    <div class="tab-content">
                                        <!-- Overview Tab -->
                                        <div class="tab-pane active" id="overview">
                                            <!-- Key Performance Indicators -->
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-3">
                                                    <div class="card card-bordered">
                                                        <div class="card-inner">
                                                            <div class="d-flex align-items-center">
                                                                <div class="text-primary me-3">
                                                                    <em class="icon ni ni-building-fill" style="font-size: 2rem;"></em>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">{{ __('Total Properties') }}</h6>
                                                                    <h4 class="mb-0">{{ $dashboardData['overview']['total_properties'] ?? '0' }}</h4>
                                                                    <small class="text-muted">{{ $dashboardData['overview']['rental_properties'] ?? '0' }} rental, {{ $dashboardData['overview']['sale_properties'] ?? '0' }} sale</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-bordered">
                                                        <div class="card-inner">
                                                            <div class="d-flex align-items-center">
                                                                <div class="text-success me-3">
                                                                    <em class="icon ni ni-users-fill" style="font-size: 2rem;"></em>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">{{ __('Occupancy Rate') }}</h6>
                                                                    <h4 class="mb-0">{{ $dashboardData['overview']['occupancy_rate'] ?? '0' }}%</h4>
                                                                    <small class="text-muted">{{ $dashboardData['overview']['occupied_properties'] ?? '0' }} occupied</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-bordered">
                                                        <div class="card-inner">
                                                            <div class="d-flex align-items-center">
                                                                <div class="text-info me-3">
                                                                    <em class="icon ni ni-money-fill" style="font-size: 2rem;"></em>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">{{ __('Monthly Revenue') }}</h6>
                                                                    <h4 class="mb-0">${{ number_format($dashboardData['overview']['monthly_revenue'] ?? 0, 0) }}</h4>
                                                                    <small class="text-muted">Total: ${{ number_format($dashboardData['overview']['total_revenue'] ?? 0, 0) }}</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="card card-bordered">
                                                        <div class="card-inner">
                                                            <div class="d-flex align-items-center">
                                                                <div class="text-warning me-3">
                                                                    <em class="icon ni ni-file-text-fill" style="font-size: 2rem;"></em>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">{{ __('Active Leases') }}</h6>
                                                                    <h4 class="mb-0">{{ $dashboardData['overview']['active_leases'] ?? '0' }}</h4>
                                                                    <small class="text-muted">{{ $dashboardData['overview']['pending_applications'] ?? '0' }} pending</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Charts Row -->
                                            <div class="row g-3 mb-4">
                                                <div class="col-md-8">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Revenue Trends') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="revenueTrendsChart" height="300"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Property Types Distribution') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="propertyTypesChart" height="300"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Performance Metrics -->
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Performance Metrics') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row g-3">
                                                                <div class="col-6">
                                                                    <div class="text-center">
                                                                        <h6 class="text-muted">{{ __('Average Rent') }}</h6>
                                                                        <h4 class="text-primary">${{ number_format($dashboardData['overview']['average_rent'] ?? 0, 0) }}</h4>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="text-center">
                                                                        <h6 class="text-muted">{{ __('Total Tenants') }}</h6>
                                                                        <h4 class="text-success">{{ $dashboardData['overview']['total_tenants'] ?? '0' }}</h4>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Quick Actions') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="btn-group-vertical w-100" role="group">
                                                                <button type="button" class="btn btn-outline-primary btn-sm mb-2" onclick="generateReport('property')">
                                                                    <em class="icon ni ni-building"></em> Property Report
                                                                </button>
                                                                <button type="button" class="btn btn-outline-success btn-sm mb-2" onclick="generateReport('financial')">
                                                                    <em class="icon ni ni-money"></em> Financial Report
                                                                </button>
                                                                <button type="button" class="btn btn-outline-info btn-sm mb-2" onclick="generateReport('tenant')">
                                                                    <em class="icon ni ni-users"></em> Tenant Report
                                                                </button>
                                                                <button type="button" class="btn btn-outline-warning btn-sm" onclick="generateReport('maintenance')">
                                                                    <em class="icon ni ni-tools"></em> Maintenance Report
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Property Analytics Tab -->
                                        <div class="tab-pane" id="property-analytics">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Properties by Location') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="propertiesByLocationChart" height="250"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Properties by Status') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="propertiesByStatusChart" height="250"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Financial Analytics Tab -->
                                        <div class="tab-pane" id="financial-analytics">
                                            <div class="row g-3">
                                                <div class="col-md-12">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Revenue Breakdown') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="revenueBreakdownChart" height="300"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Tenant Analytics Tab -->
                                        <div class="tab-pane" id="tenant-analytics">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Tenant Demographics') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="tenantDemographicsChart" height="250"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Lease Renewals') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="leaseRenewalsChart" height="250"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Maintenance Analytics Tab -->
                                        <div class="tab-pane" id="maintenance-analytics">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Maintenance Requests by Category') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="maintenanceCategoriesChart" height="250"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Maintenance Costs Trend') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <canvas id="maintenanceCostsChart" height="250"></canvas>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Reports Tab -->
                                        <div class="tab-pane" id="reports">
                                            <div class="row g-3">
                                                <div class="col-md-8">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Report Templates') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-striped">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>{{ __('Name') }}</th>
                                                                            <th>{{ __('Category') }}</th>
                                                                            <th>{{ __('Type') }}</th>
                                                                            <th>{{ __('Usage') }}</th>
                                                                            <th>{{ __('Actions') }}</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        @forelse($reportTemplates as $category => $templates)
                                                                            @foreach($templates as $template)
                                                                                <tr>
                                                                                    <td>{{ $template['name'] }}</td>
                                                                                    <td><span class="badge badge-info">{{ ucfirst($template['category']) }}</span></td>
                                                                                    <td>{{ ucfirst(str_replace('_', ' ', $template['report_type'])) }}</td>
                                                                                    <td>{{ $template['usage_count'] ?? 0 }}</td>
                                                                                    <td>
                                                                                        <button class="btn btn-sm btn-outline-primary" onclick="generateCustomReport('{{ $template['id'] }}')">
                                                                                            <em class="icon ni ni-file-text"></em>
                                                                                        </button>
                                                                                    </td>
                                                                                </tr>
                                                                            @endforeach
                                                                        @empty
                                                                            <tr>
                                                                                <td colspan="5" class="text-center">{{ __('No report templates available') }}</td>
                                                                            </tr>
                                                                        @endforelse
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="card card-bordered">
                                                        <div class="card-header">
                                                            <h6 class="card-title">{{ __('Scheduled Reports') }}</h6>
                                                        </div>
                                                        <div class="card-body">
                                                            @forelse($scheduledReports as $report)
                                                                <div class="d-flex align-items-center p-2 border-bottom">
                                                                    <div class="me-3">
                                                                        <em class="icon ni ni-calendar text-primary"></em>
                                                                    </div>
                                                                    <div class="flex-grow-1">
                                                                        <h6 class="mb-1">{{ $report['name'] }}</h6>
                                                                        <small class="text-muted">{{ $report['frequency_display_name'] }} • {{ $report['format_display_name'] }}</small>
                                                                    </div>
                                                                    <div>
                                                                        <span class="badge badge-{{ $report['status_badge_class'] }}">{{ $report['status_display_text'] }}</span>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                                <p class="text-muted">{{ __('No scheduled reports') }}</p>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @include('admin.settings.includes.settings-sidebar')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Quick Report Modal -->
<div class="modal fade" id="quickReportModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Generate Quick Report') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickReportForm">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Report Type') }}</label>
                            <select class="form-select" name="report_type" required>
                                <option value="">{{ __('Select Report Type') }}</option>
                                <option value="property">{{ __('Property Report') }}</option>
                                <option value="financial">{{ __('Financial Report') }}</option>
                                <option value="tenant">{{ __('Tenant Report') }}</option>
                                <option value="maintenance">{{ __('Maintenance Report') }}</option>
                                <option value="occupancy">{{ __('Occupancy Report') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Export Format') }}</label>
                            <select class="form-select" name="format">
                                <option value="json">{{ __('JSON (View Online)') }}</option>
                                <option value="pdf">{{ __('PDF') }}</option>
                                <option value="excel">{{ __('Excel') }}</option>
                                <option value="csv">{{ __('CSV') }}</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Date From') }}</label>
                            <input type="date" class="form-control" name="date_from">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">{{ __('Date To') }}</label>
                            <input type="date" class="form-control" name="date_to">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="button" class="btn btn-primary" onclick="submitQuickReport()">{{ __('Generate Report') }}</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Initialize charts when page loads
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
});

function initializeCharts() {
    // Revenue Trends Chart
    const revenueCtx = document.getElementById('revenueTrendsChart').getContext('2d');
    new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Revenue',
                data: [12000, 15000, 14000, 18000, 16000, 20000],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Property Types Chart
    const propertyTypesCtx = document.getElementById('propertyTypesChart').getContext('2d');
    new Chart(propertyTypesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Apartments', 'Houses', 'Commercial', 'Townhouses'],
            datasets: [{
                data: [45, 30, 15, 10],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Properties by Location Chart
    const locationCtx = document.getElementById('propertiesByLocationChart').getContext('2d');
    new Chart(locationCtx, {
        type: 'bar',
        data: {
            labels: ['Nairobi', 'Mombasa', 'Kisumu', 'Nakuru', 'Eldoret'],
            datasets: [{
                label: 'Properties',
                data: [25, 18, 12, 8, 5],
                backgroundColor: 'rgba(54, 162, 235, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Properties by Status Chart
    const statusCtx = document.getElementById('propertiesByStatusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Occupied', 'Vacant', 'Maintenance', 'Available'],
            datasets: [{
                data: [60, 20, 15, 5],
                backgroundColor: [
                    '#28a745',
                    '#dc3545',
                    '#ffc107',
                    '#17a2b8'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Revenue Breakdown Chart
    const revenueBreakdownCtx = document.getElementById('revenueBreakdownChart').getContext('2d');
    new Chart(revenueBreakdownCtx, {
        type: 'bar',
        data: {
            labels: ['Rent', 'Deposits', 'Maintenance Fees', 'Other'],
            datasets: [{
                label: 'Revenue',
                data: [85000, 15000, 8000, 2000],
                backgroundColor: [
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
                    '#6c757d'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Tenant Demographics Chart
    const tenantCtx = document.getElementById('tenantDemographicsChart').getContext('2d');
    new Chart(tenantCtx, {
        type: 'doughnut',
        data: {
            labels: ['18-25', '26-35', '36-45', '46-55', '55+'],
            datasets: [{
                data: [15, 35, 25, 15, 10],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB',
                    '#FFCE56',
                    '#4BC0C0',
                    '#9966FF'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Lease Renewals Chart
    const renewalsCtx = document.getElementById('leaseRenewalsChart').getContext('2d');
    new Chart(renewalsCtx, {
        type: 'line',
        data: {
            labels: ['Q1', 'Q2', 'Q3', 'Q4'],
            datasets: [{
                label: 'Renewals',
                data: [12, 18, 15, 22],
                borderColor: 'rgb(75, 192, 192)',
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Maintenance Categories Chart
    const maintenanceCtx = document.getElementById('maintenanceCategoriesChart').getContext('2d');
    new Chart(maintenanceCtx, {
        type: 'bar',
        data: {
            labels: ['Plumbing', 'Electrical', 'HVAC', 'General', 'Emergency'],
            datasets: [{
                label: 'Requests',
                data: [25, 18, 12, 30, 8],
                backgroundColor: 'rgba(255, 99, 132, 0.8)'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });

    // Maintenance Costs Chart
    const maintenanceCostsCtx = document.getElementById('maintenanceCostsChart').getContext('2d');
    new Chart(maintenanceCostsCtx, {
        type: 'line',
        data: {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            datasets: [{
                label: 'Costs',
                data: [2500, 3200, 2800, 4100, 3500, 3800],
                borderColor: 'rgb(255, 99, 132)',
                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
}

function refreshAnalytics() {
    location.reload();
}

function generateQuickReport() {
    const modal = new bootstrap.Modal(document.getElementById('quickReportModal'));
    modal.show();
}

function submitQuickReport() {
    const form = document.getElementById('quickReportForm');
    const formData = new FormData(form);
    
    const button = event.target;
    const originalText = button.innerHTML;
    button.innerHTML = '<em class="icon ni ni-loading"></em> Generating...';
    button.disabled = true;
    
    fetch('{{ route("admin.settings.analytics.generate-report") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        if (data.success) {
            if (data.download_url) {
                window.open(data.download_url, '_blank');
                showAlert('Report generated and downloaded successfully!', 'success');
            } else {
                showAlert('Report generated successfully!', 'success');
                // You could display the report data in a modal or new page
            }
            bootstrap.Modal.getInstance(document.getElementById('quickReportModal')).hide();
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        button.innerHTML = originalText;
        button.disabled = false;
        console.error('Error:', error);
        showAlert('An error occurred while generating the report', 'danger');
    });
}

function generateReport(type) {
    const formData = {
        report_type: type,
        format: 'json'
    };
    
    fetch('{{ route("admin.settings.analytics.generate-report") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Report generated successfully!', 'success');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while generating the report', 'danger');
    });
}

function generateCustomReport(templateId) {
    const formData = {
        template_id: templateId,
        format: 'json'
    };
    
    fetch(`{{ url('admin/settings/analytics/generate-custom-report') }}/${templateId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Custom report generated successfully!', 'success');
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while generating the custom report', 'danger');
    });
}

function scheduleReport() {
    showAlert('Schedule report functionality coming soon!', 'info');
}

function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}

// Auto-refresh analytics every 10 minutes
setInterval(function() {
    // You can implement partial refresh here instead of full page reload
    // refreshAnalytics();
}, 600000); // 10 minutes
</script>
@endpush
@endsection
