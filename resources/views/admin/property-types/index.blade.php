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
                                            <h5 class="nk-block-title">{{ __('Property Types Management') }}</h5>
                                            <span>{{ __('Manage different types of properties in your system') }}</span>
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
                                                            <a href="{{ route('admin.property-types.create') }}" class="btn btn-primary">
                                                                <em class="icon ni ni-plus"></em>
                                                                <span>{{ __('Add Property Type') }}</span>
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
                                            <label class="form-label">{{ __('Category') }}</label>
                                            <select class="form-select" id="filter-category" onchange="filterPropertyTypes()">
                                                <option value="">{{ __('All Categories') }}</option>
                                                <option value="residential">🏠 {{ __('Residential') }}</option>
                                                <option value="commercial">🏢 {{ __('Commercial') }}</option>
                                                <option value="industrial">🏭 {{ __('Industrial') }}</option>
                                                <option value="land">🌿 {{ __('Land') }}</option>
                                                <option value="mixed-use">🏘️ {{ __('Mixed-Use') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Status') }}</label>
                                            <select class="form-select" id="filter-status" onchange="filterPropertyTypes()">
                                                <option value="">{{ __('All Status') }}</option>
                                                <option value="active">{{ __('Active') }}</option>
                                                <option value="inactive">{{ __('Inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Search') }}</label>
                                            <input type="text" class="form-control" id="search-input" placeholder="{{ __('Search property types...') }}" onkeyup="filterPropertyTypes()">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
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

                                <!-- Property Types Table -->
                                <div class="nk-block">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                                    </th>
                                                    <th>{{ __('Name') }}</th>
                                                    <th>{{ __('Category') }}</th>
                                                    <th>{{ __('Description') }}</th>
                                                    <th>{{ __('Properties') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Sort Order') }}</th>
                                                    <th>{{ __('Created') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody id="property-types-table-body">
                                                @forelse($propertyTypes as $propertyType)
                                                    <tr data-type-id="{{ $propertyType->id }}" 
                                                        data-status="{{ $propertyType->is_active ? 'active' : 'inactive' }}"
                                                        data-category="{{ $propertyType->category }}">
                                                        <td>
                                                            <input type="checkbox" class="property-type-checkbox" value="{{ $propertyType->id }}" onchange="updateBulkActions()">
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    @if($propertyType->icon)
                                                                        <em class="icon ni {{ $propertyType->icon }}" 
                                                                            style="color: {{ $propertyType->color }};"></em>
                                                                    @else
                                                                        <em class="icon ni ni-building"></em>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <strong>{{ $propertyType->name }}</strong>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-{{ $propertyType->category == 'residential' ? 'success' : ($propertyType->category == 'commercial' ? 'primary' : ($propertyType->category == 'industrial' ? 'warning' : ($propertyType->category == 'land' ? 'info' : 'secondary'))) }}">
                                                                @switch($propertyType->category)
                                                                    @case('residential')
                                                                        🏠 {{ __('Residential') }}
                                                                        @break
                                                                    @case('commercial')
                                                                        🏢 {{ __('Commercial') }}
                                                                        @break
                                                                    @case('industrial')
                                                                        🏭 {{ __('Industrial') }}
                                                                        @break
                                                                    @case('land')
                                                                        🌿 {{ __('Land') }}
                                                                        @break
                                                                    @case('mixed-use')
                                                                        🏘️ {{ __('Mixed-Use') }}
                                                                        @break
                                                                    @default
                                                                        {{ ucfirst($propertyType->category) }}
                                                                @endswitch
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($propertyType->description)
                                                                <span class="text-muted">{{ Str::limit($propertyType->description, 100) }}</span>
                                                            @else
                                                                <span class="text-muted">{{ __('No description') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div class="d-flex gap-2">
                                                                @if($propertyType->rental_properties_count > 0)
                                                                    <span class="badge badge-info">{{ $propertyType->rental_properties_count }} rental</span>
                                                                @endif
                                                                @if($propertyType->sale_properties_count > 0)
                                                                    <span class="badge badge-success">{{ $propertyType->sale_properties_count }} sale</span>
                                                                @endif
                                                                @if($propertyType->lease_properties_count > 0)
                                                                    <span class="badge badge-warning">{{ $propertyType->lease_properties_count }} lease</span>
                                                                @endif
                                                                @if($propertyType->rental_properties_count == 0 && $propertyType->sale_properties_count == 0 && $propertyType->lease_properties_count == 0)
                                                                    <span class="text-muted">{{ __('No properties') }}</span>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-{{ $propertyType->is_active ? 'success' : 'secondary' }}">
                                                                {{ $propertyType->is_active ? __('Active') : __('Inactive') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="text-muted">{{ $propertyType->sort_order }}</span>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <small class="text-muted">{{ $propertyType->created_at->format('M d, Y') }}</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('admin.property-types.show', $propertyType) }}" class="btn btn-sm btn-outline-info" title="{{ __('View') }}">
                                                                    <em class="icon ni ni-eye"></em>
                                                                </a>
                                                                <a href="{{ route('admin.property-types.edit', $propertyType) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                                                    <em class="icon ni ni-edit"></em>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline-{{ $propertyType->is_active ? 'warning' : 'success' }}" 
                                                                        onclick="toggleStatus('{{ $propertyType->id }}')" 
                                                                        title="{{ $propertyType->is_active ? __('Deactivate') : __('Activate') }}">
                                                                    <em class="icon ni ni-{{ $propertyType->is_active ? 'pause' : 'play' }}"></em>
                                                                </button>
                                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePropertyType('{{ $propertyType->id }}')" title="{{ __('Delete') }}">
                                                                    <em class="icon ni ni-trash"></em>
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <em class="icon ni ni-building" style="font-size: 3rem;"></em>
                                                                <h6 class="mt-3">{{ __('No Property Types Found') }}</h6>
                                                                <p>{{ __('Get started by adding your first property type') }}</p>
                                                                <a href="{{ route('admin.property-types.create') }}" class="btn btn-primary">
                                                                    <em class="icon ni ni-plus"></em>
                                                                    {{ __('Add Property Type') }}
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                    
                                    <!-- Pagination -->
                                    @if($propertyTypes->hasPages())
                                        <div class="mt-4">
                                            {{ $propertyTypes->links() }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedPropertyTypes = new Set();

function filterPropertyTypes() {
    const category = document.getElementById('filter-category').value.toLowerCase();
    const status = document.getElementById('filter-status').value.toLowerCase();
    const search = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll('#property-types-table-body tr[data-type-id]');
    
    rows.forEach(row => {
        const rowCategory = row.getAttribute('data-category');
        const rowStatus = row.getAttribute('data-status');
        const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const description = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        
        const categoryMatch = !category || rowCategory === category;
        const statusMatch = !status || rowStatus === status;
        const searchMatch = !search || name.includes(search) || description.includes(search);
        
        row.style.display = (categoryMatch && statusMatch && searchMatch) ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('filter-category').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('search-input').value = '';
    filterPropertyTypes();
}

function refreshData() {
    location.reload();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.property-type-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        if (selectAll.checked) {
            selectedPropertyTypes.add(checkbox.value);
        } else {
            selectedPropertyTypes.delete(checkbox.value);
        }
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.property-type-checkbox:checked');
    selectedPropertyTypes.clear();
    checkboxes.forEach(checkbox => selectedPropertyTypes.add(checkbox.value));
    
    document.getElementById('selected-count').textContent = selectedPropertyTypes.size;
    document.getElementById('bulk-actions').style.display = selectedPropertyTypes.size > 0 ? 'block' : 'none';
}

function bulkAction(action) {
    if (selectedPropertyTypes.size === 0) {
        alert('Please select at least one property type');
        return;
    }
    
    const actionText = {
        'activate': 'activate',
        'deactivate': 'deactivate',
        'delete': 'delete'
    }[action];
    
    if (!confirm(`Are you sure you want to ${actionText} ${selectedPropertyTypes.size} property type(s)?`)) {
        return;
    }
    
    fetch('{{ route("admin.property-types.bulk-action") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            ids: Array.from(selectedPropertyTypes)
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

function toggleStatus(propertyTypeId) {
    fetch(`{{ url('admin/property-types') }}/${propertyTypeId}/toggle-status`, {
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

function deletePropertyType(propertyTypeId) {
    if (!confirm('Are you sure you want to delete this property type? This action cannot be undone.')) {
        return;
    }
    
    fetch(`{{ url('admin/property-types') }}/${propertyTypeId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => {
        if (response.ok) {
            showAlert('Property type deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to delete property type', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting property type', 'danger');
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
</script>
@endpush
@endsection
