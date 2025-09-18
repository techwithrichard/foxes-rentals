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
                                            <h5 class="nk-block-title">{{ __('API Keys Management') }}</h5>
                                            <span>{{ __('Manage API keys for external services and integrations') }}</span>
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
                                                            <a href="{{ route('admin.settings.api-keys.create') }}" class="btn btn-primary">
                                                                <em class="icon ni ni-plus"></em>
                                                                <span>{{ __('Add API Key') }}</span>
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filters -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Service') }}</label>
                                            <select class="form-select" id="filter-service" onchange="filterApiKeys()">
                                                <option value="">{{ __('All Services') }}</option>
                                                @foreach($services as $service)
                                                    <option value="{{ $service }}" {{ request('service') === $service ? 'selected' : '' }}>
                                                        {{ ucfirst($service) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Environment') }}</label>
                                            <select class="form-select" id="filter-environment" onchange="filterApiKeys()">
                                                <option value="">{{ __('All Environments') }}</option>
                                                @foreach($environments as $env)
                                                    <option value="{{ $env }}" {{ request('environment') === $env ? 'selected' : '' }}>
                                                        {{ ucfirst($env) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Status') }}</label>
                                            <select class="form-select" id="filter-status" onchange="filterApiKeys()">
                                                <option value="">{{ __('All Status') }}</option>
                                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>{{ __('Expired') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                                    <em class="icon ni ni-filter-clear"></em>
                                                    {{ __('Clear') }}
                                                </button>
                                                <button type="button" class="btn btn-outline-primary" onclick="refreshData()">
                                                    <em class="icon ni ni-refresh"></em>
                                                    {{ __('Refresh') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bulk Actions -->
                                <div class="row mb-4" id="bulk-actions" style="display: none;">
                                    <div class="col-12">
                                        <div class="alert alert-info d-flex align-items-center">
                                            <em class="icon ni ni-info-fill me-2"></em>
                                            <div class="flex-grow-1">
                                                <strong>{{ __('Bulk Actions') }}:</strong>
                                                <span id="selected-count">0</span> {{ __('items selected') }}
                                            </div>
                                            <div class="btn-group ms-3">
                                                <button type="button" class="btn btn-sm btn-success" onclick="bulkAction('activate')">
                                                    <em class="icon ni ni-check"></em> {{ __('Activate') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="bulkAction('deactivate')">
                                                    <em class="icon ni ni-cross"></em> {{ __('Deactivate') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="bulkAction('delete')">
                                                    <em class="icon ni ni-trash"></em> {{ __('Delete') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- API Keys Table -->
                                <div class="nk-block">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                                    </th>
                                                    <th>{{ __('Service') }}</th>
                                                    <th>{{ __('Key Type') }}</th>
                                                    <th>{{ __('Environment') }}</th>
                                                    <th>{{ __('Value') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Last Used') }}</th>
                                                    <th>{{ __('Created') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($apiKeys as $apiKey)
                                                    <tr>
                                                        <td>
                                                            <input type="checkbox" class="api-key-checkbox" value="{{ $apiKey->id }}" onchange="updateBulkActions()">
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="service-icon me-2">
                                                                    @if(str_contains(strtolower($apiKey->service_name), 'mpesa'))
                                                                        <span class="badge badge-primary">M-PESA</span>
                                                                    @elseif(str_contains(strtolower($apiKey->service_name), 'paypal'))
                                                                        <span class="badge badge-info">PayPal</span>
                                                                    @elseif(str_contains(strtolower($apiKey->service_name), 'sendgrid'))
                                                                        <span class="badge badge-success">SendGrid</span>
                                                                    @else
                                                                        <span class="badge badge-secondary">{{ $apiKey->service_display_name }}</span>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <strong>{{ $apiKey->service_display_name }}</strong>
                                                                    @if($apiKey->description)
                                                                        <br><small class="text-muted">{{ Str::limit($apiKey->description, 50) }}</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-outline-secondary">{{ $apiKey->key_type_display_name }}</span>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-{{ $apiKey->environment === 'production' ? 'danger' : ($apiKey->environment === 'staging' ? 'warning' : 'info') }}">
                                                                {{ ucfirst($apiKey->environment) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <div class="api-key-value">
                                                                <code class="text-muted">{{ $apiKey->masked_value }}</code>
                                                                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="toggleApiKeyValue('{{ $apiKey->id }}')">
                                                                    <em class="icon ni ni-eye"></em>
                                                                </button>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @if($apiKey->isExpired())
                                                                <span class="badge badge-danger">{{ __('Expired') }}</span>
                                                            @elseif($apiKey->is_active)
                                                                <span class="badge badge-success">{{ __('Active') }}</span>
                                                            @else
                                                                <span class="badge badge-secondary">{{ __('Inactive') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($apiKey->last_used_at)
                                                                <div>
                                                                    <small class="text-muted">{{ $apiKey->last_used_at->diffForHumans() }}</small>
                                                                    @if($apiKey->lastUsedBy)
                                                                        <br><small class="text-muted">by {{ $apiKey->lastUsedBy->name }}</small>
                                                                    @endif
                                                                </div>
                                                            @else
                                                                <span class="text-muted">{{ __('Never') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <small class="text-muted">{{ $apiKey->created_at->format('M d, Y') }}</small>
                                                                @if($apiKey->creator)
                                                                    <br><small class="text-muted">by {{ $apiKey->creator->name }}</small>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="testConnection('{{ $apiKey->id }}')" title="{{ __('Test Connection') }}">
                                                                    <em class="icon ni ni-play"></em>
                                                                </button>
                                                                <a href="{{ route('admin.settings.api-keys.edit', $apiKey) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                                                    <em class="icon ni ni-edit"></em>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline-{{ $apiKey->is_active ? 'warning' : 'success' }}" 
                                                                        onclick="toggleStatus('{{ $apiKey->id }}')" 
                                                                        title="{{ $apiKey->is_active ? __('Deactivate') : __('Activate') }}">
                                                                    <em class="icon ni ni-{{ $apiKey->is_active ? 'pause' : 'play' }}"></em>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteApiKey('{{ $apiKey->id }}')" title="{{ __('Delete') }}">
                                                                    <em class="icon ni ni-trash"></em>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <em class="icon ni ni-key" style="font-size: 3rem;"></em>
                                                                <h6 class="mt-3">{{ __('No API Keys Found') }}</h6>
                                                                <p>{{ __('Get started by adding your first API key') }}</p>
                                                                <a href="{{ route('admin.settings.api-keys.create') }}" class="btn btn-primary">
                                                                    <em class="icon ni ni-plus"></em>
                                                                    {{ __('Add API Key') }}
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    @if($apiKeys->hasPages())
                                        <div class="mt-4">
                                            {{ $apiKeys->links() }}
                                        </div>
                                    @endif
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

@push('scripts')
<script>
let selectedApiKeys = new Set();

function filterApiKeys() {
    const service = document.getElementById('filter-service').value;
    const environment = document.getElementById('filter-environment').value;
    const status = document.getElementById('filter-status').value;
    
    const params = new URLSearchParams();
    if (service) params.append('service', service);
    if (environment) params.append('environment', environment);
    if (status) params.append('status', status);
    
    window.location.href = '{{ route("admin.settings.api-keys.index") }}?' + params.toString();
}

function clearFilters() {
    document.getElementById('filter-service').value = '';
    document.getElementById('filter-environment').value = '';
    document.getElementById('filter-status').value = '';
    filterApiKeys();
}

function refreshData() {
    location.reload();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.api-key-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        if (selectAll.checked) {
            selectedApiKeys.add(checkbox.value);
        } else {
            selectedApiKeys.delete(checkbox.value);
        }
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.api-key-checkbox:checked');
    selectedApiKeys.clear();
    checkboxes.forEach(checkbox => selectedApiKeys.add(checkbox.value));
    
    document.getElementById('selected-count').textContent = selectedApiKeys.size;
    document.getElementById('bulk-actions').style.display = selectedApiKeys.size > 0 ? 'block' : 'none';
}

function bulkAction(action) {
    if (selectedApiKeys.size === 0) {
        alert('Please select at least one API key');
        return;
    }
    
    const actionText = {
        'activate': 'activate',
        'deactivate': 'deactivate',
        'delete': 'delete'
    }[action];
    
    if (!confirm(`Are you sure you want to ${actionText} ${selectedApiKeys.size} API key(s)?`)) {
        return;
    }
    
    fetch('{{ route("admin.settings.api-keys.bulk-action") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            api_key_ids: Array.from(selectedApiKeys)
        })
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
        showAlert('An error occurred while performing bulk action', 'danger');
    });
}

function toggleStatus(apiKeyId) {
    fetch(`{{ url('admin/settings/api-keys') }}/${apiKeyId}/toggle-status`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
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
        showAlert('An error occurred while updating status', 'danger');
    });
}

function testConnection(apiKeyId) {
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<em class="icon ni ni-loading"></em>';
    button.disabled = true;
    
    fetch(`{{ url('admin/settings/api-keys') }}/${apiKeyId}/test-connection`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        button.innerHTML = originalText;
        button.disabled = false;
        
        if (data.success) {
            const result = data.result;
            const alertClass = result.success ? 'success' : 'warning';
            showAlert(`${result.message} (${result.response_time}ms)`, alertClass);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        button.innerHTML = originalText;
        button.disabled = false;
        console.error('Error:', error);
        showAlert('An error occurred while testing connection', 'danger');
    });
}

function deleteApiKey(apiKeyId) {
    if (!confirm('Are you sure you want to delete this API key? This action cannot be undone.')) {
        return;
    }
    
    fetch(`{{ url('admin/settings/api-keys') }}/${apiKeyId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => {
        if (response.ok) {
            showAlert('API key deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to delete API key', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting API key', 'danger');
    });
}

function toggleApiKeyValue(apiKeyId) {
    // This would typically make an AJAX call to get the actual value
    // For security reasons, we might not want to show the full value
    alert('API key values are masked for security. Use the edit function to view the full value.');
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
@endsection
