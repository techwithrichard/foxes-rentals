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
                            <h3 class="nk-block-title page-title">{{ __('Roles Management') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage user roles and their permissions</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-menu-alt-r"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        @can('manage_roles')
                                            <li>
                                                <a href="{{ route('admin.settings.roles.create') }}" class="btn btn-primary">
                                                    <em class="icon ni ni-plus"></em>
                                                    <span>{{ __('Add Role') }}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        <li>
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle btn btn-outline-light" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-download"></em>
                                                    <span>{{ __('Export') }}</span>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('admin.settings.roles.export') }}">CSV Export</a></li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-primary">
                                            <em class="icon ni ni-shield-check"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Total Roles') }}</div>
                                            <div class="h4 mb-0">{{ $roles->total() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-success">
                                            <em class="icon ni ni-check-circle"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Active Roles') }}</div>
                                            <div class="h4 mb-0">{{ $roles->where('is_active', true)->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-info">
                                            <em class="icon ni ni-users"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Roles with Users') }}</div>
                                            <div class="h4 mb-0">{{ $roles->where('users_count', '>', 0)->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-warning">
                                            <em class="icon ni ni-key"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Avg Permissions') }}</div>
                                            <div class="h4 mb-0">{{ round($roles->avg('permissions_count'), 1) }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.settings.roles.index') }}" class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">{{ __('Search') }}</label>
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search roles...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Min Permissions') }}</label>
                                    <input type="number" name="min_permissions" class="form-control" placeholder="0" value="{{ request('min_permissions') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Actions') }}</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <em class="icon ni ni-search"></em>
                                        </button>
                                        <a href="{{ route('admin.settings.roles.index') }}" class="btn btn-outline-secondary">
                                            <em class="icon ni ni-reload"></em>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-body">
                            <form id="bulkActionForm" method="POST" action="{{ route('admin.settings.roles.bulk-action') }}">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Bulk Action') }}</label>
                                        <select name="action" class="form-select" required>
                                            <option value="">{{ __('Select Action') }}</option>
                                            <option value="activate">{{ __('Activate') }}</option>
                                            <option value="deactivate">{{ __('Deactivate') }}</option>
                                            <option value="delete">{{ __('Delete') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-outline-primary" onclick="return confirmBulkAction()">
                                            <em class="icon ni ni-check"></em>
                                            {{ __('Apply Action') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Roles Table -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>
                                                <input type="checkbox" id="selectAll" class="form-check-input">
                                            </th>
                                            <th>{{ __('Role') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Users') }}</th>
                                            <th>{{ __('Permissions') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Created') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($roles as $role)
                                            <tr>
                                                <td>
                                                    @if(!in_array($role->name, ['super_admin', 'admin', 'landlord', 'tenant']))
                                                        <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" class="form-check-input role-checkbox">
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($role->icon)
                                                            <em class="icon {{ $role->icon }} me-2" style="color: {{ $role->color }}"></em>
                                                        @else
                                                            <div class="avatar avatar-sm me-2" style="background-color: {{ $role->color ?? '#6c757d' }}">
                                                                <span class="avatar-text text-white">{{ substr($role->name, 0, 2) }}</span>
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ ucfirst(str_replace('_', ' ', $role->name)) }}</div>
                                                            <small class="text-muted">{{ $role->name }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="text-muted">
                                                        {{ $role->description ?? 'No description available' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm badge-info">{{ $role->users_count }} users</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm badge-primary">{{ $role->permissions_count }} permissions</span>
                                                </td>
                                                <td>
                                                    @if($role->is_active)
                                                        <span class="badge badge-sm badge-success">{{ __('Active') }}</span>
                                                    @else
                                                        <span class="badge badge-sm badge-danger">{{ __('Inactive') }}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $role->created_at->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.settings.roles.show', $role) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                                                            <em class="icon ni ni-eye"></em>
                                                        </a>
                                                        <a href="{{ route('admin.settings.roles.edit', $role) }}" 
                                                           class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                                            <em class="icon ni ni-edit"></em>
                                                        </a>
                                                        @if(!in_array($role->name, ['super_admin', 'admin', 'landlord', 'tenant']))
                                                            <form method="POST" action="{{ route('admin.settings.roles.toggle-status', $role) }}" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-{{ $role->is_active ? 'warning' : 'success' }}" 
                                                                        title="{{ $role->is_active ? __('Deactivate') : __('Activate') }}">
                                                                    <em class="icon ni ni-{{ $role->is_active ? 'pause' : 'play' }}"></em>
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('admin.settings.roles.duplicate', $role) }}" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-info" title="{{ __('Duplicate') }}">
                                                                    <em class="icon ni ni-copy"></em>
                                                                </button>
                                                            </form>
                                                            <form method="POST" action="{{ route('admin.settings.roles.destroy', $role) }}" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}"
                                                                        onclick="return confirm('Are you sure you want to delete this role?')">
                                                                    <em class="icon ni ni-trash"></em>
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <em class="icon ni ni-shield-check fs-2x"></em>
                                                        <p class="mt-2">{{ __('No roles found') }}</p>
                                                        @can('manage_roles')
                                                            <a href="{{ route('admin.settings.roles.create') }}" class="btn btn-primary mt-2">
                                                                {{ __('Add First Role') }}
                                                            </a>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if($roles->hasPages())
                                <div class="mt-3">
                                    {{ $roles->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Select all functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.role-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Individual checkbox change
document.querySelectorAll('.role-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.role-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.role-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
    });
});

function confirmBulkAction() {
    const action = document.querySelector('select[name="action"]').value;
    const checkedBoxes = document.querySelectorAll('.role-checkbox:checked');
    
    if (!action) {
        alert('Please select an action.');
        return false;
    }
    
    if (checkedBoxes.length === 0) {
        alert('Please select at least one role.');
        return false;
    }
    
    const actionText = action.toUpperCase();
    return confirm(`Are you sure you want to ${actionText} ${checkedBoxes.length} role(s)?`);
}

// Add selected role IDs to bulk form
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.role-checkbox:checked');
    const roleIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    // Add hidden inputs for selected role IDs
    roleIds.forEach(roleId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'role_ids[]';
        input.value = roleId;
        this.appendChild(input);
    });
});
</script>
@endpush
