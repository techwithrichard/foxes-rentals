<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg"
     data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <!-- Header -->
        <div class="card-inner">
            <h3 class="nk-block-title page-title">{{ __('Settings') }}</h3>
            <div class="nk-block-des text-soft">
                <p>{{ __('Configure your system') }}</p>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="card-inner p-3">
            <div class="row g-2">
                <div class="col-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner text-center">
                            <div class="text-primary">
                                <em class="icon ni ni-users-fill" style="font-size: 1.5rem;"></em>
                            </div>
                            <div class="fs-6 fw-bold mt-2">{{ $stats['users'] ?? '0' }}</div>
                            <div class="text-muted small">Users</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner text-center">
                            <div class="text-success">
                                <em class="icon ni ni-building-fill" style="font-size: 1.5rem;"></em>
                            </div>
                            <div class="fs-6 fw-bold mt-2">{{ $stats['properties'] ?? '0' }}</div>
                            <div class="text-muted small">Properties</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation -->
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <!-- Dashboard -->
                <li class="{{ active(['admin.settings.index']) }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <em class="icon ni ni-dashboard"></em>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
                
                <!-- General Settings -->
                <li class="menu-item {{ active(['admin.settings.general', 'admin.settings.company', 'admin.settings.appearance']) }}">
                    <a href="#" class="menu-link" data-bs-toggle="collapse" data-bs-target="#generalMenu">
                        <em class="icon ni ni-setting"></em>
                        <span>{{ __('General') }}</span>
                        <em class="icon ni ni-chevron-right"></em>
                    </a>
                    <div class="collapse" id="generalMenu">
                        <ul class="sub-menu">
                            <li class="{{ active(['admin.settings.general']) }}">
                                <a href="{{ route('admin.settings.general') }}">
                                    <em class="icon ni ni-app"></em>
                                    <span>{{ __('Application') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.company']) }}">
                                <a href="{{ route('admin.settings.company') }}">
                                    <em class="icon ni ni-building"></em>
                                    <span>{{ __('Company') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.appearance']) }}">
                                <a href="{{ route('admin.settings.appearance') }}">
                                    <em class="icon ni ni-palette"></em>
                                    <span>{{ __('Appearance') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Property Management -->
                <li class="menu-item {{ active(['admin.settings.property-types', 'admin.settings.house-types', 'admin.settings.amenities']) }}">
                    <a href="#" class="menu-link" data-bs-toggle="collapse" data-bs-target="#propertyMenu">
                        <em class="icon ni ni-building"></em>
                        <span>{{ __('Properties') }}</span>
                        <em class="icon ni ni-chevron-right"></em>
                    </a>
                    <div class="collapse" id="propertyMenu">
                        <ul class="sub-menu">
                            <li class="{{ active(['admin.settings.property-types']) }}">
                                <a href="{{ route('admin.settings.property-types') }}">
                                    <em class="icon ni ni-home"></em>
                                    <span>{{ __('Property Types') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.house-types']) }}">
                                <a href="{{ route('admin.settings.house-types') }}">
                                    <em class="icon ni ni-home-alt"></em>
                                    <span>{{ __('House Types') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.amenities']) }}">
                                <a href="{{ route('admin.settings.amenities') }}">
                                    <em class="icon ni ni-star"></em>
                                    <span>{{ __('Amenities') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Financial Settings -->
                <li class="menu-item {{ active(['admin.settings.payment-methods', 'admin.settings.expense-types', 'admin.settings.currency']) }}">
                    <a href="#" class="menu-link" data-bs-toggle="collapse" data-bs-target="#financialMenu">
                        <em class="icon ni ni-money"></em>
                        <span>{{ __('Financial') }}</span>
                        <em class="icon ni ni-chevron-right"></em>
                    </a>
                    <div class="collapse" id="financialMenu">
                        <ul class="sub-menu">
                            <li class="{{ active(['admin.settings.payment-methods']) }}">
                                <a href="{{ route('admin.settings.payment-methods') }}">
                                    <em class="icon ni ni-credit-card"></em>
                                    <span>{{ __('Payment Methods') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.expense-types']) }}">
                                <a href="{{ route('admin.settings.expense-types') }}">
                                    <em class="icon ni ni-receipt"></em>
                                    <span>{{ __('Expense Types') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.currency']) }}">
                                <a href="{{ route('admin.settings.currency') }}">
                                    <em class="icon ni ni-coins"></em>
                                    <span>{{ __('Currency') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- User Management -->
                <li class="menu-item {{ active(['admin.settings.users.index', 'admin.settings.roles.index', 'admin.settings.security']) }}">
                    <a href="#" class="menu-link" data-bs-toggle="collapse" data-bs-target="#userMenu">
                        <em class="icon ni ni-users"></em>
                        <span>{{ __('Users') }}</span>
                        <em class="icon ni ni-chevron-right"></em>
                    </a>
                    <div class="collapse" id="userMenu">
                        <ul class="sub-menu">
                            <li class="{{ active(['admin.settings.users.index']) }}">
                                <a href="{{ route('admin.settings.users.index') }}">
                                    <em class="icon ni ni-user"></em>
                                    <span>{{ __('User Settings') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.roles.index']) }}">
                                <a href="{{ route('admin.settings.roles.index') }}">
                                    <em class="icon ni ni-shield-star"></em>
                                    <span>{{ __('Roles & Permissions') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.security']) }}">
                                <a href="{{ route('admin.settings.security') }}">
                                    <em class="icon ni ni-lock"></em>
                                    <span>{{ __('Security') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- System & Integration -->
                <li class="menu-item {{ active(['admin.settings.api-keys.index', 'admin.settings.integrations', 'admin.settings.backup']) }}">
                    <a href="#" class="menu-link" data-bs-toggle="collapse" data-bs-target="#systemMenu">
                        <em class="icon ni ni-cpu"></em>
                        <span>{{ __('System') }}</span>
                        <em class="icon ni ni-chevron-right"></em>
                    </a>
                    <div class="collapse" id="systemMenu">
                        <ul class="sub-menu">
                            <li class="{{ active(['admin.settings.api-keys.index']) }}">
                                <a href="{{ route('admin.settings.api-keys.index') }}">
                                    <em class="icon ni ni-key"></em>
                                    <span>{{ __('API Keys') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.integrations']) }}">
                                <a href="{{ route('admin.settings.integrations') }}">
                                    <em class="icon ni ni-plug"></em>
                                    <span>{{ __('Integrations') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.backup']) }}">
                                <a href="{{ route('admin.settings.backup') }}">
                                    <em class="icon ni ni-save"></em>
                                    <span>{{ __('Backup & Restore') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                
                <!-- Analytics & Reports -->
                <li class="menu-item {{ active(['admin.settings.analytics.index', 'admin.settings.reports', 'admin.settings.system-health.index']) }}">
                    <a href="#" class="menu-link" data-bs-toggle="collapse" data-bs-target="#analyticsMenu">
                        <em class="icon ni ni-chart-bar"></em>
                        <span>{{ __('Analytics') }}</span>
                        <em class="icon ni ni-chevron-right"></em>
                    </a>
                    <div class="collapse" id="analyticsMenu">
                        <ul class="sub-menu">
                            <li class="{{ active(['admin.settings.analytics.index']) }}">
                                <a href="{{ route('admin.settings.analytics.index') }}">
                                    <em class="icon ni ni-chart"></em>
                                    <span>{{ __('Dashboard') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.reports']) }}">
                                <a href="{{ route('admin.settings.reports') }}">
                                    <em class="icon ni ni-file-text"></em>
                                    <span>{{ __('Reports') }}</span>
                                </a>
                            </li>
                            <li class="{{ active(['admin.settings.system-health.index']) }}">
                                <a href="{{ route('admin.settings.system-health.index') }}">
                                    <em class="icon ni ni-heartbeat"></em>
                                    <span>{{ __('System Health') }}</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
            </ul>
        </div>
        
        <!-- Quick Actions -->
        <div class="card-inner">
            <h6 class="title">{{ __('Quick Actions') }}</h6>
            <div class="btn-group-vertical w-100" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm mb-2" onclick="clearSettingsCache()">
                    <em class="icon ni ni-trash"></em> Clear Cache
                </button>
                <button type="button" class="btn btn-outline-success btn-sm mb-2" onclick="exportSettings()">
                    <em class="icon ni ni-download"></em> Export Settings
                </button>
                <button type="button" class="btn btn-outline-info btn-sm mb-2" onclick="refreshSystemHealth()">
                    <em class="icon ni ni-refresh"></em> Refresh Status
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.menu-item .menu-link {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #526484;
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 0.375rem;
    margin: 0.125rem 0;
}

.menu-item .menu-link:hover {
    color: #6576ff;
    background-color: #f8f9ff;
}

.menu-item .menu-link.active {
    color: #6576ff;
    background-color: #f8f9ff;
}

.menu-item .menu-link em:first-child {
    margin-right: 0.75rem;
    font-size: 1.25rem;
}

.menu-item .menu-link em:last-child {
    margin-left: auto;
    transition: transform 0.3s ease;
}

.menu-item .menu-link[aria-expanded="true"] em:last-child {
    transform: rotate(90deg);
}

.sub-menu {
    list-style: none;
    padding: 0;
    margin: 0.5rem 0 0 0;
}

.sub-menu li {
    margin: 0;
}

.sub-menu .menu-link {
    padding: 0.5rem 1rem 0.5rem 3rem;
    font-size: 0.875rem;
}

.sub-menu .menu-link em:first-child {
    font-size: 1rem;
}

.card-inner-group {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

@media (max-width: 768px) {
    .card-inner-group {
        max-height: none;
    }
}
</style>
@endpush

@push('scripts')
<script>
function clearSettingsCache() {
    if (confirm('Are you sure you want to clear the settings cache?')) {
        fetch('{{ route("admin.settings.clear-cache") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('Cache cleared successfully!', 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('Failed to clear cache: ' + data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('An error occurred while clearing cache', 'danger');
        });
    }
}

function exportSettings() {
    fetch('{{ route("admin.settings.export") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const blob = new Blob([JSON.stringify(data.settings, null, 2)], {type: 'application/json'});
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'foxes-settings-' + new Date().toISOString().split('T')[0] + '.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            showAlert('Settings exported successfully!', 'success');
        } else {
            showAlert('Failed to export settings: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while exporting settings', 'danger');
    });
}

function refreshSystemHealth() {
    const refreshBtn = document.querySelector('button[onclick="refreshSystemHealth()"]');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<em class="icon ni ni-loading"></em> Refreshing...';
    refreshBtn.disabled = true;
    
    setTimeout(() => {
        location.reload();
    }, 1000);
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
</script>
@endpush
