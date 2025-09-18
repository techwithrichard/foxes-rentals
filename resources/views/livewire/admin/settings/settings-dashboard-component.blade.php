<div class="card-inner card-inner-lg">
    <!-- Dashboard Header -->
    <div class="nk-block-head nk-block-head-lg">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">{{ __('Settings Dashboard') }}</h5>
                <span>{{ __('Monitor and manage your system configuration') }}</span>
            </div>
            <div class="nk-block-head-content align-self-start d-lg-none">
                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                   data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
            </div>
            <div class="nk-block-head-content">
                <div class="toggle-wrap nk-block-tools-toggle">
                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                    <div class="toggle-expand-content" data-content="pageMenu">
                        <ul class="nk-block-tools g-3">
                            <li class="nk-block-tools-opt">
                                <button type="button" class="btn btn-outline-primary" wire:click="clearCache">
                                    <em class="icon ni ni-trash"></em>
                                    <span>{{ __('Clear Cache') }}</span>
                                </button>
                            </li>
                            <li class="nk-block-tools-opt">
                                <button type="button" class="btn btn-outline-success" wire:click="exportSettings">
                                    <em class="icon ni ni-download"></em>
                                    <span>{{ __('Export Settings') }}</span>
                                </button>
                            </li>
                            <li class="nk-block-tools-opt">
                                <button type="button" class="btn btn-outline-info" wire:click="refreshData">
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

    <!-- Statistics Cards -->
    <div class="nk-block">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="card card-bordered">
                    <div class="card-inner">
                        <div class="d-flex align-items-center">
                            <div class="text-primary me-3">
                                <em class="icon ni ni-setting" style="font-size: 2rem;"></em>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ __('Total Settings') }}</h6>
                                <h4 class="mb-0">{{ $statistics['total_settings'] ?? 0 }}</h4>
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
                                <em class="icon ni ni-key" style="font-size: 2rem;"></em>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ __('API Keys') }}</h6>
                                <h4 class="mb-0">{{ $statistics['active_api_keys'] ?? 0 }}</h4>
                                <small class="text-muted">{{ $statistics['expired_api_keys'] ?? 0 }} expired</small>
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
                                <em class="icon ni ni-users" style="font-size: 2rem;"></em>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ __('Users') }}</h6>
                                <h4 class="mb-0">{{ $statistics['users_count'] ?? 0 }}</h4>
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
                                <em class="icon ni ni-building" style="font-size: 2rem;"></em>
                            </div>
                            <div>
                                <h6 class="mb-1">{{ __('Properties') }}</h6>
                                <h4 class="mb-0">{{ $statistics['properties_count'] ?? 0 }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Health Status -->
    @if(!empty($systemHealth))
    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-header">
                <h6 class="card-title">{{ __('System Health Status') }}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    @foreach($systemHealth as $component => $status)
                        @if(is_array($status) && isset($status['status']))
                        <div class="col-md-6">
                            <div class="d-flex justify-content-between align-items-center p-3 border rounded">
                                <div>
                                    <h6 class="mb-1">{{ ucfirst(str_replace('_', ' ', $component)) }}</h6>
                                    <small class="text-muted">{{ $status['message'] ?? 'No message' }}</small>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-{{ $status['status'] === 'healthy' || $status['status'] === 'configured' ? 'success' : ($status['status'] === 'not_configured' ? 'warning' : 'danger') }}">
                                        {{ ucfirst($status['status']) }}
                                    </span>
                                    @if(isset($status['response_time']))
                                        <br><small class="text-muted">{{ $status['response_time'] }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Changes -->
    @if(!empty($recentChanges))
    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-header">
                <h6 class="card-title">{{ __('Recent Changes') }}</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>{{ __('Setting') }}</th>
                                <th>{{ __('Change') }}</th>
                                <th>{{ __('User') }}</th>
                                <th>{{ __('Time') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentChanges as $change)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $change['setting_name'] }}</strong>
                                        <br><small class="text-muted">{{ $change['setting_key'] }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="change-display">
                                        <span class="text-danger">{{ $change['old_value'] }}</span>
                                        <em class="icon ni ni-arrow-right mx-2"></em>
                                        <span class="text-success">{{ $change['new_value'] }}</span>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <strong>{{ $change['changed_by'] }}</strong>
                                        @if($change['ip_address'])
                                            <br><small class="text-muted">{{ $change['ip_address'] }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $change['changed_at'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions -->
    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-header">
                <h6 class="card-title">{{ __('Quick Actions') }}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('admin.settings.api-keys.index') }}" class="btn btn-outline-primary w-100">
                            <em class="icon ni ni-key me-2"></em>
                            {{ __('Manage API Keys') }}
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.users.advanced.index') }}" class="btn btn-outline-info w-100">
                            <em class="icon ni ni-users me-2"></em>
                            {{ __('User Management') }}
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.settings.property.index') }}" class="btn btn-outline-success w-100">
                            <em class="icon ni ni-building me-2"></em>
                            {{ __('Property Settings') }}
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('admin.settings.roles.index') }}" class="btn btn-outline-warning w-100">
                            <em class="icon ni ni-shield-star me-2"></em>
                            {{ __('Roles & Permissions') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Settings -->
    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-header">
                <h6 class="card-title">{{ __('Search Settings') }}</h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-8">
                        <div class="form-group">
                            <input type="text" class="form-control" placeholder="{{ __('Search settings by name or key...') }}" 
                                   wire:model.live="searchQuery">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <select class="form-select" wire:model.live="selectedCategory">
                                <option value="">{{ __('All Categories') }}</option>
                                <option value="general">{{ __('General') }}</option>
                                <option value="api">{{ __('API Keys') }}</option>
                                <option value="property">{{ __('Property') }}</option>
                                <option value="financial">{{ __('Financial') }}</option>
                                <option value="system">{{ __('System') }}</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                @if($searchQuery || $selectedCategory)
                <div class="mt-3">
                    <div class="alert alert-info">
                        <em class="icon ni ni-info-fill me-2"></em>
                        {{ __('Search results for') }}: 
                        @if($searchQuery) <strong>"{{ $searchQuery }}"</strong> @endif
                        @if($searchQuery && $selectedCategory) {{ __('in') }} @endif
                        @if($selectedCategory) <strong>{{ ucfirst($selectedCategory) }}</strong> @endif
                        <button type="button" class="btn btn-sm btn-outline-secondary ms-2" 
                                wire:click="$set('searchQuery', ''); $set('selectedCategory', '')">
                            {{ __('Clear') }}
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:init', function () {
    // Auto-refresh dashboard every 5 minutes
    setInterval(function() {
        Livewire.dispatch('refreshDashboard');
    }, 300000); // 5 minutes
});

// Listen for dashboard refresh events
Livewire.on('dashboardRefreshed', function() {
    // Show a subtle notification
    if (typeof showToast === 'function') {
        showToast('Dashboard refreshed', 'info');
    }
});
</script>
@endpush
