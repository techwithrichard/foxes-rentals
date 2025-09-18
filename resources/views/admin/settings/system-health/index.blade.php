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
                                            <h5 class="nk-block-title">{{ __('System Health Monitoring') }}</h5>
                                            <span>{{ __('Monitor system performance, health, and alerts') }}</span>
                                        </div>
                                        <div class="nk-block-head-content align-self-start d-lg-none">
                                            <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                                               data-target="systemHealthAside"><em class="icon ni ni-menu-alt-r"></em></a>
                                        </div>
                                        <div class="nk-block-head-content">
                                            <div class="toggle-wrap nk-block-tools-toggle">
                                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                                <div class="toggle-expand-content" data-content="pageMenu">
                                                    <ul class="nk-block-tools g-3">
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-primary" onclick="runOptimization()">
                                                                <em class="icon ni ni-setting"></em>
                                                                <span>{{ __('Optimize System') }}</span>
                                                            </button>
                                                        </li>
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-outline-secondary" onclick="clearCache()">
                                                                <em class="icon ni ni-refresh"></em>
                                                                <span>{{ __('Clear Cache') }}</span>
                                                            </button>
                                                        </li>
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-outline-info" onclick="refreshData()">
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

                                <!-- Overall System Status -->
                                <div class="nk-block">
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="card card-bordered">
                                                <div class="card-inner">
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-{{ $systemHealth['overall_status']['status'] === 'healthy' ? 'success' : ($systemHealth['overall_status']['status'] === 'warning' ? 'warning' : 'danger') }} me-3">
                                                            <em class="icon ni ni-{{ $systemHealth['overall_status']['status'] === 'healthy' ? 'check-circle' : ($systemHealth['overall_status']['status'] === 'warning' ? 'alert-circle' : 'cross-circle') }}" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Overall Status') }}</h6>
                                                            <h4 class="mb-0 text-{{ $systemHealth['overall_status']['status'] === 'healthy' ? 'success' : ($systemHealth['overall_status']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                                                {{ ucfirst($systemHealth['overall_status']['status']) }}
                                                            </h4>
                                                            <small class="text-muted">Score: {{ $systemHealth['overall_status']['score'] }}%</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-bordered">
                                                <div class="card-inner">
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-info me-3">
                                                            <em class="icon ni ni-alert-circle" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Active Alerts') }}</h6>
                                                            <h4 class="mb-0">{{ $recentAlerts->where('status', 'active')->count() }}</h4>
                                                            <small class="text-muted">{{ $recentAlerts->where('severity', 'critical')->count() }} critical</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="card card-bordered">
                                                <div class="card-inner">
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-success me-3">
                                                            <em class="icon ni ni-speedometer" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Uptime') }}</h6>
                                                            <h4 class="mb-0">{{ $systemHealth['uptime']['formatted_uptime'] ?? 'Unknown' }}</h4>
                                                            <small class="text-muted">{{ __('Last checked: ') }}{{ \Carbon\Carbon::parse($systemHealth['last_checked'])->diffForHumans() }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- System Health Components -->
                                <div class="nk-block">
                                    <div class="row g-3">
                                        <!-- Database Health -->
                                        <div class="col-md-6">
                                            <div class="card card-bordered">
                                                <div class="card-header">
                                                    <h6 class="card-title">{{ __('Database Health') }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge badge-{{ $systemHealth['database']['status'] === 'healthy' ? 'success' : ($systemHealth['database']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($systemHealth['database']['status']) }}
                                                        </span>
                                                        <small class="text-muted">{{ $systemHealth['database']['response_time'] ?? 0 }}ms</small>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted">{{ __('Connections') }}: {{ $systemHealth['database']['connection_count'] ?? 0 }}/{{ $systemHealth['database']['max_connections'] ?? 0 }}</small>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar" style="width: {{ $systemHealth['database']['connection_usage'] ?? 0 }}%"></div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">{{ __('Size') }}: {{ $systemHealth['database']['database_size']['formatted'] ?? 'Unknown' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Cache Health -->
                                        <div class="col-md-6">
                                            <div class="card card-bordered">
                                                <div class="card-header">
                                                    <h6 class="card-title">{{ __('Cache Health') }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge badge-{{ $systemHealth['cache']['status'] === 'healthy' ? 'success' : ($systemHealth['cache']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($systemHealth['cache']['status']) }}
                                                        </span>
                                                        <small class="text-muted">{{ $systemHealth['cache']['hit_rate'] ?? 0 }}% hit rate</small>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted">{{ __('Memory Usage') }}: {{ $systemHealth['cache']['memory_usage'] ?? 'Unknown' }}</small>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">{{ __('Keys') }}: {{ $systemHealth['cache']['key_count'] ?? 0 }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Storage Health -->
                                        <div class="col-md-6">
                                            <div class="card card-bordered">
                                                <div class="card-header">
                                                    <h6 class="card-title">{{ __('Storage Health') }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge badge-{{ $systemHealth['storage']['status'] === 'healthy' ? 'success' : ($systemHealth['storage']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($systemHealth['storage']['status']) }}
                                                        </span>
                                                        <small class="text-muted">{{ $systemHealth['storage']['usage_percentage'] ?? 0 }}% used</small>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted">{{ __('Free Space') }}: {{ $systemHealth['storage']['formatted_free'] ?? 'Unknown' }}</small>
                                                        <div class="progress progress-sm">
                                                            <div class="progress-bar" style="width: {{ $systemHealth['storage']['usage_percentage'] ?? 0 }}%"></div>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">{{ __('Log Size') }}: {{ \App\Services\SystemHealthMonitoringService::class ? 'N/A' : 'N/A' }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Memory Health -->
                                        <div class="col-md-6">
                                            <div class="card card-bordered">
                                                <div class="card-header">
                                                    <h6 class="card-title">{{ __('Memory Health') }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                                        <span class="badge badge-{{ $systemHealth['memory']['status'] === 'healthy' ? 'success' : ($systemHealth['memory']['status'] === 'warning' ? 'warning' : 'danger') }}">
                                                            {{ ucfirst($systemHealth['memory']['status']) }}
                                                        </span>
                                                        <small class="text-muted">{{ $systemHealth['memory']['memory_percentage'] ?? 0 }}% used</small>
                                                    </div>
                                                    <div class="mb-2">
                                                        <small class="text-muted">{{ __('Peak Memory') }}: {{ $systemHealth['memory']['peak_memory'] ?? 0 }}</small>
                                                    </div>
                                                    <div>
                                                        <small class="text-muted">{{ __('Available') }}: {{ $systemHealth['memory']['memory_available'] ?? 0 }}</small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Performance Metrics -->
                                <div class="nk-block">
                                    <div class="card card-bordered">
                                        <div class="card-header">
                                            <h6 class="card-title">{{ __('Performance Metrics') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h6 class="text-muted">{{ __('Response Time') }}</h6>
                                                        <h4 class="text-{{ $performanceMetrics['response_times']['average'] < 1000 ? 'success' : ($performanceMetrics['response_times']['average'] < 2000 ? 'warning' : 'danger') }}">
                                                            {{ $performanceMetrics['response_times']['average'] ?? 0 }}ms
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h6 class="text-muted">{{ __('Error Rate') }}</h6>
                                                        <h4 class="text-{{ ($performanceMetrics['error_rate'] ?? 0) < 1 ? 'success' : (($performanceMetrics['error_rate'] ?? 0) < 5 ? 'warning' : 'danger') }}">
                                                            {{ $performanceMetrics['error_rate'] ?? 0 }}%
                                                        </h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h6 class="text-muted">{{ __('Throughput') }}</h6>
                                                        <h4 class="text-info">{{ $performanceMetrics['throughput'] ?? 0 }}</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <h6 class="text-muted">{{ __('Requests/min') }}</h6>
                                                        <h4 class="text-info">{{ $performanceMetrics['requests_per_minute'] ?? 0 }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Alerts -->
                                <div class="nk-block">
                                    <div class="card card-bordered">
                                        <div class="card-header">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="card-title">{{ __('Recent Alerts') }}</h6>
                                                <a href="#" class="btn btn-sm btn-outline-primary">{{ __('View All') }}</a>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            @forelse($recentAlerts as $alert)
                                                <div class="d-flex align-items-center p-3 border-bottom">
                                                    <div class="me-3">
                                                        <em class="icon ni ni-{{ $alert->icon }} text-{{ $alert->color }}" style="font-size: 1.5rem;"></em>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $alert->title }}</h6>
                                                        <p class="text-muted mb-1">{{ $alert->message }}</p>
                                                        <small class="text-muted">
                                                            {{ $alert->created_at->diffForHumans() }} • 
                                                            <span class="badge badge-{{ $alert->severity_badge_class }}">{{ $alert->severity_display_name }}</span>
                                                            <span class="badge badge-{{ $alert->status_badge_class }}">{{ $alert->status_display_name }}</span>
                                                        </small>
                                                    </div>
                                                    <div class="btn-group" role="group">
                                                        @if($alert->status === 'active')
                                                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="acknowledgeAlert('{{ $alert->id }}')">
                                                                <em class="icon ni ni-check"></em>
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="resolveAlert('{{ $alert->id }}')">
                                                                <em class="icon ni ni-check-circle"></em>
                                                            </button>
                                                        @elseif($alert->status === 'acknowledged')
                                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="resolveAlert('{{ $alert->id }}')">
                                                                <em class="icon ni ni-check-circle"></em>
                                                            </button>
                                                        @endif
                                                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="suppressAlert('{{ $alert->id }}')">
                                                            <em class="icon ni ni-cross"></em>
                                                        </button>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center py-4">
                                                    <em class="icon ni ni-check-circle text-success" style="font-size: 3rem;"></em>
                                                    <h6 class="mt-3">{{ __('No Active Alerts') }}</h6>
                                                    <p class="text-muted">{{ __('All systems are running smoothly') }}</p>
                                                </div>
                                            @endforelse
                                        </div>
                                    </div>
                                </div>

                                <!-- Optimization Recommendations -->
                                @if(!empty($performanceMetrics['optimization_recommendations']))
                                <div class="nk-block">
                                    <div class="card card-bordered">
                                        <div class="card-header">
                                            <h6 class="card-title">{{ __('Optimization Recommendations') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            @foreach($performanceMetrics['optimization_recommendations'] as $recommendation)
                                                <div class="d-flex align-items-center p-3 border-bottom">
                                                    <div class="me-3">
                                                        <em class="icon ni ni-{{ $recommendation['priority'] === 'high' ? 'alert-circle' : ($recommendation['priority'] === 'medium' ? 'info' : 'check') }} text-{{ $recommendation['priority'] === 'high' ? 'danger' : ($recommendation['priority'] === 'medium' ? 'warning' : 'success') }}"></em>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <h6 class="mb-1">{{ $recommendation['title'] }}</h6>
                                                        <p class="text-muted mb-1">{{ $recommendation['description'] }}</p>
                                                        <small class="text-muted">{{ $recommendation['estimated_impact'] }}</small>
                                                    </div>
                                                    <div>
                                                        <span class="badge badge-{{ $recommendation['priority'] === 'high' ? 'danger' : ($recommendation['priority'] === 'medium' ? 'warning' : 'success') }}">
                                                            {{ ucfirst($recommendation['priority']) }} Priority
                                                        </span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @include('admin.settings.includes.settings-sidebar')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function refreshData() {
    location.reload();
}

function runOptimization() {
    if (!confirm('Are you sure you want to run system optimization? This may take a few minutes.')) {
        return;
    }
    
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<em class="icon ni ni-loading"></em> Optimizing...';
    button.disabled = true;
    
    fetch('{{ route("admin.settings.system-health.optimize") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            type: 'comprehensive'
        })
    })
    .then(response => response.json())
    .then(data => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 2000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        button.innerHTML = originalText;
        button.disabled = false;
        console.error('Error:', error);
        showAlert('An error occurred while optimizing the system', 'danger');
    });
}

function clearCache() {
    if (!confirm('Are you sure you want to clear all system health cache?')) {
        return;
    }
    
    fetch('{{ route("admin.settings.system-health.clear-cache") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while clearing cache', 'danger');
    });
}

function acknowledgeAlert(alertId) {
    fetch(`{{ url('admin/settings/system-health/alerts') }}/${alertId}/acknowledge`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while acknowledging alert', 'danger');
    });
}

function resolveAlert(alertId) {
    fetch(`{{ url('admin/settings/system-health/alerts') }}/${alertId}/resolve`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while resolving alert', 'danger');
    });
}

function suppressAlert(alertId) {
    if (!confirm('Are you sure you want to suppress this alert?')) {
        return;
    }
    
    fetch(`{{ url('admin/settings/system-health/alerts') }}/${alertId}/suppress`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert(data.message, 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while suppressing alert', 'danger');
    });
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

// Auto-refresh every 5 minutes
setInterval(function() {
    // You can implement partial refresh here instead of full page reload
    // refreshData();
}, 300000); // 5 minutes
</script>
@endpush
@endsection
