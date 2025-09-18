@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <!-- Professional Header -->
                <div class="nk-block-head nk-block-head-lg">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h2 class="nk-block-title fw-normal">{{ __('System Configuration') }}</h2>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Configure and manage your Foxes Rental Management System') }}</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-more-v"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        <li class="nk-block-tools-opt">
                                            <button type="button" class="btn btn-outline-primary btn-sm" onclick="clearCache()">
                                                <em class="icon ni ni-trash"></em>
                                                <span>{{ __('Clear Cache') }}</span>
                                            </button>
                                        </li>
                                        <li class="nk-block-tools-opt">
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="exportSettings()">
                                                <em class="icon ni ni-download"></em>
                                                <span>{{ __('Export Config') }}</span>
                                            </button>
                                        </li>
                                        <li class="nk-block-tools-opt">
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="refreshSystem()">
                                                <em class="icon ni ni-refresh"></em>
                                                <span>{{ __('Refresh') }}</span>
                                            </button>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- System Status Overview -->
                <div class="nk-block">
                    <div class="row g-3">
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center">
                                        <div class="text-success me-3">
                                            <em class="icon ni ni-shield-check" style="font-size: 2.5rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('System Health') }}</h6>
                                            <h4 class="mb-0 text-success">{{ __('Healthy') }}</h4>
                                            <small class="text-muted">All systems operational</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center">
                                        <div class="text-info me-3">
                                            <em class="icon ni ni-users" style="font-size: 2.5rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Active Users') }}</h6>
                                            <h4 class="mb-0">{{ $stats['users'] ?? 0 }}</h4>
                                            <small class="text-muted">{{ $stats['new_users_today'] ?? 0 }} new today</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center">
                                        <div class="text-warning me-3">
                                            <em class="icon ni ni-building" style="font-size: 2.5rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Properties') }}</h6>
                                            <h4 class="mb-0">{{ $stats['properties'] ?? 0 }}</h4>
                                            <small class="text-muted">{{ $stats['vacant_properties'] ?? 0 }} vacant</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-3 col-md-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center">
                                        <div class="text-primary me-3">
                                            <em class="icon ni ni-key" style="font-size: 2.5rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('API Keys') }}</h6>
                                            <h4 class="mb-0">{{ $stats['api_keys'] ?? 0 }}</h4>
                                            <small class="text-muted">{{ $stats['expired_keys'] ?? 0 }} expired</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuration Categories -->
                <div class="nk-block">
                    <div class="row g-3">
                        <!-- Application Configuration -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="text-primary me-3">
                                            <em class="icon ni ni-app" style="font-size: 2rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Application') }}</h6>
                                            <small class="text-muted">{{ __('Core system configuration') }}</small>
                                        </div>
                                    </div>
                                    <div class="config-list">
                                        <a href="{{ route('admin.settings.general') }}" class="config-item">
                                            <em class="icon ni ni-setting"></em>
                                            <span>{{ __('General Settings') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.settings.company') }}" class="config-item">
                                            <em class="icon ni ni-building"></em>
                                            <span>{{ __('Company Profile') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.settings.appearance') }}" class="config-item">
                                            <em class="icon ni ni-palette"></em>
                                            <span>{{ __('Appearance & Theme') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.settings.localization') }}" class="config-item">
                                            <em class="icon ni ni-globe"></em>
                                            <span>{{ __('Localization') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Property Management -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="text-success me-3">
                                            <em class="icon ni ni-building" style="font-size: 2rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Property Management') }}</h6>
                                            <small class="text-muted">{{ __('Configure property settings') }}</small>
                                        </div>
                                    </div>
                                    <div class="config-list">
                                        <a href="{{ route('admin.property-types.index') }}" class="config-item">
                                            <em class="icon ni ni-home"></em>
                                            <span>{{ __('Property Types') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.house-types.index') }}" class="config-item">
                                            <em class="icon ni ni-home-alt"></em>
                                            <span>{{ __('House Types') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.amenities.index') }}" class="config-item">
                                            <em class="icon ni ni-star"></em>
                                            <span>{{ __('Amenities') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.pricing-rules.index') }}" class="config-item">
                                            <em class="icon ni ni-money"></em>
                                            <span>{{ __('Pricing Rules') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Financial Configuration -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="text-info me-3">
                                            <em class="icon ni ni-money" style="font-size: 2rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Financial') }}</h6>
                                            <small class="text-muted">{{ __('Payment and billing configuration') }}</small>
                                        </div>
                                    </div>
                                    <div class="config-list">
                                        <a href="{{ route('admin.payment-methods.index') }}" class="config-item">
                                            <em class="icon ni ni-credit-card"></em>
                                            <span>{{ __('Payment Methods') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.expense-types.index') }}" class="config-item">
                                            <em class="icon ni ni-receipt"></em>
                                            <span>{{ __('Expense Types') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.currency.index') }}" class="config-item">
                                            <em class="icon ni ni-coins"></em>
                                            <span>{{ __('Currency Settings') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.tax-settings.index') }}" class="config-item">
                                            <em class="icon ni ni-calculator"></em>
                                            <span>{{ __('Tax Settings') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Management -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="text-warning me-3">
                                            <em class="icon ni ni-users" style="font-size: 2rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('User Management') }}</h6>
                                            <small class="text-muted">{{ __('Users, roles and permissions') }}</small>
                                        </div>
                                    </div>
                                    <div class="config-list">
                                        <a href="{{ route('admin.users-management.index') }}" class="config-item">
                                            <em class="icon ni ni-user"></em>
                                            <span>{{ __('Users') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.roles-management.index') }}" class="config-item">
                                            <em class="icon ni ni-shield-star"></em>
                                            <span>{{ __('Roles') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.permissions.index') }}" class="config-item">
                                            <em class="icon ni ni-shield-check"></em>
                                            <span>{{ __('Permissions') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.security.index') }}" class="config-item">
                                            <em class="icon ni ni-lock"></em>
                                            <span>{{ __('Security') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System & Integration -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="text-danger me-3">
                                            <em class="icon ni ni-cpu" style="font-size: 2rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('System & Integration') }}</h6>
                                            <small class="text-muted">{{ __('APIs and system configuration') }}</small>
                                        </div>
                                    </div>
                                    <div class="config-list">
                                        <a href="{{ route('admin.api-keys.index') }}" class="config-item">
                                            <em class="icon ni ni-key"></em>
                                            <span>{{ __('API Keys') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.integrations.index') }}" class="config-item">
                                            <em class="icon ni ni-plug"></em>
                                            <span>{{ __('Integrations') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.backups.index') }}" class="config-item">
                                            <em class="icon ni ni-save"></em>
                                            <span>{{ __('Backup & Restore') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.system-health.index') }}" class="config-item">
                                            <em class="icon ni ni-heartbeat"></em>
                                            <span>{{ __('System Health') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Communication -->
                        <div class="col-xl-4 col-lg-6">
                            <div class="card card-bordered h-100">
                                <div class="card-inner">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="text-purple me-3">
                                            <em class="icon ni ni-message" style="font-size: 2rem;"></em>
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ __('Communication') }}</h6>
                                            <small class="text-muted">{{ __('Email, SMS and notifications') }}</small>
                                        </div>
                                    </div>
                                    <div class="config-list">
                                        <a href="{{ route('admin.email-templates.index') }}" class="config-item">
                                            <em class="icon ni ni-mail"></em>
                                            <span>{{ __('Email Templates') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.notifications.index') }}" class="config-item">
                                            <em class="icon ni ni-bell"></em>
                                            <span>{{ __('Notifications') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                        <a href="{{ route('admin.sms-settings.index') }}" class="config-item">
                                            <em class="icon ni ni-mobile"></em>
                                            <span>{{ __('SMS Settings') }}</span>
                                            <em class="icon ni ni-chevron-right"></em>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-header">
                            <h6 class="card-title">{{ __('Recent Configuration Changes') }}</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Action') }}</th>
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Time') }}</th>
                                            <th>{{ __('Status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <em class="icon ni ni-setting text-primary me-2"></em>
                                                    <span>{{ __('Updated general settings') }}</span>
                                                </div>
                                            </td>
                                            <td>{{ auth()->user()->name }}</td>
                                            <td>{{ now()->diffForHumans() }}</td>
                                            <td><span class="badge badge-success">{{ __('Success') }}</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <em class="icon ni ni-key text-info me-2"></em>
                                                    <span>{{ __('Created new API key') }}</span>
                                                </div>
                                            </td>
                                            <td>{{ auth()->user()->name }}</td>
                                            <td>{{ now()->subMinutes(5)->diffForHumans() }}</td>
                                            <td><span class="badge badge-success">{{ __('Success') }}</span></td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <em class="icon ni ni-building text-success me-2"></em>
                                                    <span>{{ __('Updated property types') }}</span>
                                                </div>
                                            </td>
                                            <td>{{ auth()->user()->name }}</td>
                                            <td>{{ now()->subMinutes(15)->diffForHumans() }}</td>
                                            <td><span class="badge badge-success">{{ __('Success') }}</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.config-item {
    display: flex;
    align-items: center;
    padding: 0.75rem 0;
    border-bottom: 1px solid #e5e9f2;
    text-decoration: none;
    color: #526484;
    transition: all 0.3s ease;
}

.config-item:hover {
    color: #6576ff;
    background-color: #f8f9ff;
    padding-left: 0.5rem;
    border-radius: 0.375rem;
}

.config-item:last-child {
    border-bottom: none;
}

.config-item em:first-child {
    margin-right: 0.75rem;
    font-size: 1.25rem;
}

.config-item em:last-child {
    margin-left: auto;
    opacity: 0.5;
}

.text-purple {
    color: #8b5cf6 !important;
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 20px rgba(0,0,0,0.1);
}

@media (max-width: 768px) {
    .nk-block-head-lg .nk-block-head-content h2 {
        font-size: 1.5rem;
    }
    
    .card-inner {
        padding: 1rem;
    }
    
    .config-item {
        padding: 0.5rem 0;
    }
}
</style>
@endpush

@push('scripts')
<script>
function clearCache() {
    if (confirm('Are you sure you want to clear the system cache?')) {
        showLoading('Clearing cache...');
        fetch('{{ route("admin.settings.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                showNotification('Cache cleared successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showNotification('Failed to clear cache: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            hideLoading();
            console.error('Error:', error);
            showNotification('An error occurred while clearing cache', 'danger');
        });
    }
}

function exportSettings() {
    showLoading('Exporting configuration...');
    fetch('{{ route("admin.settings.export") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        if (data.success) {
            const blob = new Blob([JSON.stringify(data.settings, null, 2)], {type: 'application/json'});
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'foxes-config-' + new Date().toISOString().split('T')[0] + '.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            showNotification('Configuration exported successfully!', 'success');
        } else {
            showNotification('Failed to export configuration: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        hideLoading();
        console.error('Error:', error);
        showNotification('An error occurred while exporting configuration', 'danger');
    });
}

function refreshSystem() {
    showLoading('Refreshing system...');
    setTimeout(() => {
        hideLoading();
        location.reload();
    }, 1500);
}

function showLoading(message) {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading-overlay';
    loadingDiv.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center';
    loadingDiv.style.cssText = 'background: rgba(0,0,0,0.5); z-index: 9999;';
    loadingDiv.innerHTML = `
        <div class="text-center text-white">
            <div class="spinner-border mb-3" role="status"></div>
            <div>${message}</div>
        </div>
    `;
    document.body.appendChild(loadingDiv);
}

function hideLoading() {
    const loadingDiv = document.getElementById('loading-overlay');
    if (loadingDiv) {
        loadingDiv.remove();
    }
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (notification.parentNode) {
            notification.parentNode.removeChild(notification);
        }
    }, 5000);
}
</script>
@endpush
@endsection
