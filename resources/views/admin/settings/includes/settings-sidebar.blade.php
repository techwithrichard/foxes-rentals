<div class="card-aside card-aside-left user-aside toggle-slide toggle-slide-left toggle-break-lg"
     data-toggle-body="true" data-content="userAside" data-toggle-screen="lg" data-toggle-overlay="true">
    <div class="card-inner-group" data-simplebar>
        <div class="card-inner">
            <h3 class="nk-block-title page-title">{{ __('Settings Dashboard') }}</h3>
            <div class="nk-block-des text-soft">
                <p>{{ __('Manage your system configuration and preferences') }}</p>
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
                            <div class="fs-6 fw-bold mt-2">{{ $systemHealth['users_count'] ?? '0' }}</div>
                            <div class="text-muted small">Active Users</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner text-center">
                            <div class="text-success">
                                <em class="icon ni ni-building-fill" style="font-size: 1.5rem;"></em>
                            </div>
                            <div class="fs-6 fw-bold mt-2">{{ $systemHealth['properties_count'] ?? '0' }}</div>
                            <div class="text-muted small">Properties</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner text-center">
                            <div class="text-info">
                                <em class="icon ni ni-key-fill" style="font-size: 1.5rem;"></em>
                            </div>
                            <div class="fs-6 fw-bold mt-2">{{ $systemHealth['api_keys_count'] ?? '0' }}</div>
                            <div class="text-muted small">API Keys</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card card-bordered h-100">
                        <div class="card-inner text-center">
                            <div class="text-warning">
                                <em class="icon ni ni-shield-check-fill" style="font-size: 1.5rem;"></em>
                            </div>
                            <div class="fs-6 fw-bold mt-2">
                                <span class="badge bg-{{ ($systemHealth['system_status'] ?? 'unknown') === 'healthy' ? 'success' : 'danger' }}">
                                    {{ ucfirst($systemHealth['system_status'] ?? 'Unknown') }}
                                </span>
                            </div>
                            <div class="text-muted small">System Status</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Settings Navigation -->
        <div class="card-inner p-0">
            <ul class="link-list-menu">
                <li class="{{ active(['admin.settings.index']) }}">
                    <a href="{{ route('admin.settings.index') }}">
                        <em class="icon ni ni-dashboard"></em>
                        <span>{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="{{ active(['admin.settings.api-keys.*']) }}">
                    <a href="{{ route('admin.settings.api-keys.index') }}">
                        <em class="icon ni ni-key"></em>
                        <span>{{ __('API Keys') }}</span>
                        @if(($systemHealth['api_keys_count'] ?? 0) > 0)
                            <span class="badge badge-sm badge-primary">{{ $systemHealth['api_keys_count'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ active(['admin.users.advanced.*']) }}">
                    <a href="{{ route('admin.users.advanced.index') }}">
                        <em class="icon ni ni-users"></em>
                        <span>{{ __('User Management') }}</span>
                    </a>
                </li>
                <li class="{{ active(['admin.settings.property.*']) }}">
                    <a href="{{ route('admin.settings.property.index') }}">
                        <em class="icon ni ni-building"></em>
                        <span>{{ __('Property Settings') }}</span>
                    </a>
                </li>
                <li class="{{ active(['admin.settings.system-health.*']) }}">
                    <a href="{{ route('admin.settings.system-health.index') }}">
                        <em class="icon ni ni-heartbeat"></em>
                        <span>{{ __('System Health') }}</span>
                        @if(isset($systemHealth['overall_status']) && $systemHealth['overall_status']['status'] !== 'healthy')
                            <span class="badge badge-sm badge-{{ $systemHealth['overall_status']['status'] === 'warning' ? 'warning' : 'danger' }}">
                                {{ ucfirst($systemHealth['overall_status']['status']) }}
                            </span>
                        @endif
                    </a>
                </li>
                <li class="{{ active(['admin.settings.analytics.*']) }}">
                    <a href="{{ route('admin.settings.analytics.index') }}">
                        <em class="icon ni ni-chart-bar"></em>
                        <span>{{ __('Analytics & Reports') }}</span>
                        @if(isset($systemHealth['reports_count']) && $systemHealth['reports_count'] > 0)
                            <span class="badge badge-sm badge-primary">{{ $systemHealth['reports_count'] }}</span>
                        @endif
                    </a>
                </li>
                <li class="{{ active(['admin.settings.financial']) }}">
                    <a href="{{ route('admin.settings.financial') }}">
                        <em class="icon ni ni-money"></em>
                        <span>{{ __('Financial Settings') }}</span>
                    </a>
                </li>
                <li class="{{ active(['admin.settings.system']) }}">
                    <a href="{{ route('admin.settings.system') }}">
                        <em class="icon ni ni-setting"></em>
                        <span>{{ __('System Settings') }}</span>
                    </a>
                </li>
                <li class="{{ active(['admin.settings.roles.*']) }}">
                    <a href="{{ route('admin.settings.roles.index') }}">
                        <em class="icon ni ni-shield-star"></em>
                        <span>{{ __('Roles & Permissions') }}</span>
                    </a>
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
                <a href="{{ route('admin.settings.api-keys.create') }}" class="btn btn-outline-secondary btn-sm">
                    <em class="icon ni ni-plus"></em> Add API Key
                </a>
            </div>
        </div>
        
        <!-- System Health Indicator -->
        @if(isset($systemHealth))
        <div class="card-inner">
            <h6 class="title">{{ __('System Health') }}</h6>
            <div class="system-health-indicators">
                <div class="health-item d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Database</span>
                    <span class="badge badge-sm bg-{{ $systemHealth['database']['status'] === 'healthy' ? 'success' : 'danger' }}">
                        {{ $systemHealth['database']['response_time'] ?? 'N/A' }}
                    </span>
                </div>
                <div class="health-item d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Cache</span>
                    <span class="badge badge-sm bg-{{ $systemHealth['cache']['status'] === 'healthy' ? 'success' : 'danger' }}">
                        {{ $systemHealth['cache']['response_time'] ?? 'N/A' }}
                    </span>
                </div>
                <div class="health-item d-flex justify-content-between align-items-center mb-2">
                    <span class="text-muted small">Storage</span>
                    <span class="badge badge-sm bg-{{ $systemHealth['storage']['status'] === 'healthy' ? 'success' : 'danger' }}">
                        {{ $systemHealth['storage']['response_time'] ?? 'N/A' }}
                    </span>
                </div>
                @if(isset($systemHealth['external_services']))
                    @foreach($systemHealth['external_services'] as $service => $status)
                    <div class="health-item d-flex justify-content-between align-items-center mb-2">
                        <span class="text-muted small">{{ ucfirst($service) }}</span>
                        <span class="badge badge-sm bg-{{ $status['status'] === 'configured' ? 'success' : ($status['status'] === 'not_configured' ? 'warning' : 'danger') }}">
                            {{ ucfirst($status['status']) }}
                        </span>
                    </div>
                    @endforeach
                @endif
            </div>
            <div class="mt-3">
                <small class="text-muted">
                    Last checked: {{ isset($systemHealth['last_checked']) ? \Carbon\Carbon::parse($systemHealth['last_checked'])->diffForHumans() : 'Never' }}
                </small>
            </div>
        </div>
        @endif
    </div>
</div>

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
    
    // Simulate refresh by reloading the page
    setTimeout(() => {
        location.reload();
    }, 1000);
}

function showAlert(message, type) {
    // Create a simple alert notification
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.parentNode.removeChild(alertDiv);
        }
    }, 5000);
}
</script>
@endpush
