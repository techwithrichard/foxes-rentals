<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg"
     data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <!-- Professional Header -->
        <div class="card-inner">
            <div class="d-flex align-items-center mb-3">
                <div class="text-primary me-3">
                    <em class="icon ni ni-setting" style="font-size: 2rem;"></em>
                </div>
                <div>
                    <h3 class="nk-block-title page-title mb-1">{{ __('System Configuration') }}</h3>
                    <div class="nk-block-des text-soft">
                        <p class="mb-0">{{ __('Manage your rental management system') }}</p>
                    </div>
                </div>
            </div>
            
            <!-- System Status Indicator -->
            <div class="system-status-indicator">
                <div class="d-flex align-items-center justify-content-between p-2 bg-light rounded">
                    <span class="text-muted small">{{ __('System Status') }}</span>
                    <span class="badge badge-success">
                        <em class="icon ni ni-check-circle me-1"></em>
                        {{ __('Operational') }}
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Professional Navigation -->
        <div class="card-inner p-0">
            <ul class="professional-menu">
                
                <!-- Dashboard -->
                <li class="menu-section">
                    <div class="menu-section-title">
                        <em class="icon ni ni-dashboard"></em>
                        <span>{{ __('Overview') }}</span>
                    </div>
                    <ul class="menu-items">
                        <li class="{{ active(['admin.settings.index']) }}">
                            <a href="{{ route('admin.settings.index') }}">
                                <em class="icon ni ni-dashboard"></em>
                                <span>{{ __('Dashboard') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Application Configuration -->
                <li class="menu-section">
                    <div class="menu-section-title">
                        <em class="icon ni ni-app"></em>
                        <span>{{ __('Application') }}</span>
                    </div>
                    <ul class="menu-items">
                        <li class="{{ active(['admin.settings.general']) }}">
                            <a href="{{ route('admin.settings.general') }}">
                                <em class="icon ni ni-setting"></em>
                                <span>{{ __('General') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.settings.company']) }}">
                            <a href="{{ route('admin.settings.company') }}">
                                <em class="icon ni ni-building"></em>
                                <span>{{ __('Company Profile') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.settings.appearance']) }}">
                            <a href="{{ route('admin.settings.appearance') }}">
                                <em class="icon ni ni-palette"></em>
                                <span>{{ __('Appearance') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.settings.localization']) }}">
                            <a href="{{ route('admin.settings.localization') }}">
                                <em class="icon ni ni-globe"></em>
                                <span>{{ __('Localization') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Property Management -->
                <li class="menu-section">
                    <div class="menu-section-title">
                        <em class="icon ni ni-building"></em>
                        <span>{{ __('Property Management') }}</span>
                    </div>
                    <ul class="menu-items">
                        <li class="{{ active(['admin.property-types.index']) }}">
                            <a href="{{ route('admin.property-types.index') }}">
                                <em class="icon ni ni-home"></em>
                                <span>{{ __('Property Types') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.house-types.index']) }}">
                            <a href="{{ route('admin.house-types.index') }}">
                                <em class="icon ni ni-home-alt"></em>
                                <span>{{ __('House Types') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.amenities.index']) }}">
                            <a href="{{ route('admin.amenities.index') }}">
                                <em class="icon ni ni-star"></em>
                                <span>{{ __('Amenities') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.pricing-rules.index']) }}">
                            <a href="{{ route('admin.pricing-rules.index') }}">
                                <em class="icon ni ni-money"></em>
                                <span>{{ __('Pricing Rules') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Financial Configuration -->
                <li class="menu-section">
                    <div class="menu-section-title">
                        <em class="icon ni ni-money"></em>
                        <span>{{ __('Financial') }}</span>
                    </div>
                    <ul class="menu-items">
                        <li class="{{ active(['admin.payment-methods.index']) }}">
                            <a href="{{ route('admin.payment-methods.index') }}">
                                <em class="icon ni ni-credit-card"></em>
                                <span>{{ __('Payment Methods') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.expense-types.index']) }}">
                            <a href="{{ route('admin.expense-types.index') }}">
                                <em class="icon ni ni-receipt"></em>
                                <span>{{ __('Expense Types') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.currency.index']) }}">
                            <a href="{{ route('admin.currency.index') }}">
                                <em class="icon ni ni-coins"></em>
                                <span>{{ __('Currency') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.tax-settings.index']) }}">
                            <a href="{{ route('admin.tax-settings.index') }}">
                                <em class="icon ni ni-calculator"></em>
                                <span>{{ __('Tax Settings') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- User Management -->
                <li class="menu-section">
                    <div class="menu-section-title">
                        <em class="icon ni ni-users"></em>
                        <span>{{ __('User Management') }}</span>
                    </div>
                    <ul class="menu-items">
                        <li class="{{ active(['admin.users-management.index']) }}">
                            <a href="{{ route('admin.users-management.index') }}">
                                <em class="icon ni ni-user"></em>
                                <span>{{ __('Users') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.roles-management.index']) }}">
                            <a href="{{ route('admin.roles-management.index') }}">
                                <em class="icon ni ni-shield-star"></em>
                                <span>{{ __('Roles') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.permissions.index']) }}">
                            <a href="{{ route('admin.permissions.index') }}">
                                <em class="icon ni ni-shield-check"></em>
                                <span>{{ __('Permissions') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.security.index']) }}">
                            <a href="{{ route('admin.security.index') }}">
                                <em class="icon ni ni-lock"></em>
                                <span>{{ __('Security') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- System & Integration -->
                <li class="menu-section">
                    <div class="menu-section-title">
                        <em class="icon ni ni-cpu"></em>
                        <span>{{ __('System & Integration') }}</span>
                    </div>
                    <ul class="menu-items">
                        <li class="{{ active(['admin.api-keys.index']) }}">
                            <a href="{{ route('admin.api-keys.index') }}">
                                <em class="icon ni ni-key"></em>
                                <span>{{ __('API Keys') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.integrations.index']) }}">
                            <a href="{{ route('admin.integrations.index') }}">
                                <em class="icon ni ni-plug"></em>
                                <span>{{ __('Integrations') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.backups.index']) }}">
                            <a href="{{ route('admin.backups.index') }}">
                                <em class="icon ni ni-save"></em>
                                <span>{{ __('Backup & Restore') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.system-health.index']) }}">
                            <a href="{{ route('admin.system-health.index') }}">
                                <em class="icon ni ni-heartbeat"></em>
                                <span>{{ __('System Health') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Communication -->
                <li class="menu-section">
                    <div class="menu-section-title">
                        <em class="icon ni ni-message"></em>
                        <span>{{ __('Communication') }}</span>
                    </div>
                    <ul class="menu-items">
                        <li class="{{ active(['admin.email-templates.index']) }}">
                            <a href="{{ route('admin.email-templates.index') }}">
                                <em class="icon ni ni-mail"></em>
                                <span>{{ __('Email Templates') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.notifications.index']) }}">
                            <a href="{{ route('admin.notifications.index') }}">
                                <em class="icon ni ni-bell"></em>
                                <span>{{ __('Notifications') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.sms-settings.index']) }}">
                            <a href="{{ route('admin.sms-settings.index') }}">
                                <em class="icon ni ni-mobile"></em>
                                <span>{{ __('SMS Settings') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
                <!-- Reports & Analytics -->
                <li class="menu-section">
                    <div class="menu-section-title">
                        <em class="icon ni ni-chart-bar"></em>
                        <span>{{ __('Reports & Analytics') }}</span>
                    </div>
                    <ul class="menu-items">
                        <li class="{{ active(['admin.analytics.index']) }}">
                            <a href="{{ route('admin.analytics.index') }}">
                                <em class="icon ni ni-chart"></em>
                                <span>{{ __('Analytics') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.report-templates.index']) }}">
                            <a href="{{ route('admin.report-templates.index') }}">
                                <em class="icon ni ni-file-text"></em>
                                <span>{{ __('Report Templates') }}</span>
                            </a>
                        </li>
                        <li class="{{ active(['admin.scheduled-reports.index']) }}">
                            <a href="{{ route('admin.scheduled-reports.index') }}">
                                <em class="icon ni ni-calendar"></em>
                                <span>{{ __('Scheduled Reports') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                
            </ul>
        </div>
        
        <!-- Quick Actions -->
        <div class="card-inner">
            <h6 class="title">{{ __('Quick Actions') }}</h6>
            <div class="quick-actions">
                <button type="button" class="btn btn-outline-primary btn-sm w-100 mb-2" onclick="clearCache()">
                    <em class="icon ni ni-trash"></em>
                    <span>{{ __('Clear Cache') }}</span>
                </button>
                <button type="button" class="btn btn-outline-success btn-sm w-100 mb-2" onclick="exportSettings()">
                    <em class="icon ni ni-download"></em>
                    <span>{{ __('Export Config') }}</span>
                </button>
                <button type="button" class="btn btn-outline-info btn-sm w-100 mb-2" onclick="refreshSystem()">
                    <em class="icon ni ni-refresh"></em>
                    <span>{{ __('Refresh System') }}</span>
                </button>
                <button type="button" class="btn btn-outline-warning btn-sm w-100" onclick="runMaintenance()">
                    <em class="icon ni ni-tools"></em>
                    <span>{{ __('Run Maintenance') }}</span>
                </button>
            </div>
        </div>
        
        <!-- System Info -->
        <div class="card-inner">
            <h6 class="title">{{ __('System Information') }}</h6>
            <div class="system-info">
                <div class="info-item">
                    <span class="label">{{ __('Version') }}</span>
                    <span class="value">v2.1.0</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('Last Updated') }}</span>
                    <span class="value">{{ now()->format('M d, Y') }}</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('Database') }}</span>
                    <span class="value">MySQL 8.0</span>
                </div>
                <div class="info-item">
                    <span class="label">{{ __('PHP Version') }}</span>
                    <span class="value">PHP {{ PHP_VERSION }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.professional-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-section {
    margin-bottom: 1.5rem;
}

.menu-section-title {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    background: linear-gradient(135deg, #f8f9ff 0%, #e8ecff 100%);
    border-radius: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #526484;
    border-left: 3px solid #6576ff;
}

.menu-section-title em {
    margin-right: 0.75rem;
    font-size: 1.25rem;
    color: #6576ff;
}

.menu-items {
    list-style: none;
    padding: 0;
    margin: 0;
}

.menu-items li {
    margin: 0;
}

.menu-items li a {
    display: flex;
    align-items: center;
    padding: 0.75rem 1rem;
    color: #526484;
    text-decoration: none;
    transition: all 0.3s ease;
    border-radius: 0.375rem;
    margin: 0.125rem 0;
    position: relative;
}

.menu-items li a:hover {
    color: #6576ff;
    background-color: #f8f9ff;
    transform: translateX(4px);
}

.menu-items li.active a {
    color: #6576ff;
    background-color: #f8f9ff;
    font-weight: 500;
}

.menu-items li.active a::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 3px;
    background: #6576ff;
    border-radius: 0 2px 2px 0;
}

.menu-items li a em:first-child {
    margin-right: 0.75rem;
    font-size: 1.125rem;
    width: 20px;
    text-align: center;
}

.system-status-indicator {
    margin-top: 1rem;
}

.quick-actions {
    display: flex;
    flex-direction: column;
}

.system-info {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e5e9f2;
}

.info-item:last-child {
    border-bottom: none;
}

.info-item .label {
    font-size: 0.875rem;
    color: #526484;
    font-weight: 500;
}

.info-item .value {
    font-size: 0.875rem;
    color: #6576ff;
    font-weight: 600;
}

.card-inner-group {
    max-height: calc(100vh - 200px);
    overflow-y: auto;
}

@media (max-width: 768px) {
    .card-inner-group {
        max-height: none;
    }
    
    .menu-section-title {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
    
    .menu-items li a {
        padding: 0.5rem 0.75rem;
        font-size: 0.875rem;
    }
}

/* Smooth scrolling */
.card-inner-group {
    scroll-behavior: smooth;
}

/* Custom scrollbar */
.card-inner-group::-webkit-scrollbar {
    width: 4px;
}

.card-inner-group::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 2px;
}

.card-inner-group::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 2px;
}

.card-inner-group::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
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

function runMaintenance() {
    if (confirm('Are you sure you want to run system maintenance? This may take a few minutes.')) {
        showLoading('Running maintenance...');
        // Simulate maintenance process
        setTimeout(() => {
            hideLoading();
            showNotification('System maintenance completed successfully!', 'success');
        }, 3000);
    }
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
