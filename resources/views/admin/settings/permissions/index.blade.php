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
                            <h3 class="nk-block-title page-title">{{ __('Permissions Management') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage system permissions and access controls</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-menu-alt-r"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        @can('manage_permissions')
                                            <li>
                                                <a href="{{ route('admin.settings.permissions.create') }}" class="btn btn-primary">
                                                    <em class="icon ni ni-plus"></em>
                                                    <span>{{ __('Add Permission') }}</span>
                                                </a>
                                            </li>
                                        @endcan
                                        <li>
                                            <a href="{{ route('admin.settings.permissions.statistics') }}" class="btn btn-outline-info">
                                                <em class="icon ni ni-chart-bar"></em>
                                                <span>{{ __('Statistics') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <div class="dropdown">
                                                <a href="#" class="dropdown-toggle btn btn-outline-light" data-bs-toggle="dropdown">
                                                    <em class="icon ni ni-download"></em>
                                                    <span>{{ __('Export') }}</span>
                                                </a>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item" href="{{ route('admin.settings.permissions.export') }}">CSV Export</a></li>
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
                                            <em class="icon ni ni-key"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Total Permissions') }}</div>
                                            <div class="h4 mb-0">{{ $permissions->total() }}</div>
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
                                            <em class="icon ni ni-shield-check"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Categories') }}</div>
                                            <div class="h4 mb-0">{{ $categories->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-info">
                                            <em class="icon ni ni-users"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Used Permissions') }}</div>
                                            <div class="h4 mb-0">{{ $permissions->where('roles_count', '>', 0)->count() }}</div>
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
                                            <em class="icon ni ni-alert-circle"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Unused Permissions') }}</div>
                                            <div class="h4 mb-0">{{ $permissions->where('roles_count', 0)->count() }}</div>
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
                            <form method="GET" action="{{ route('admin.settings.permissions.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Search') }}</label>
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search permissions...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Category') }}</label>
                                    <select name="category" class="form-select">
                                        <option value="">{{ __('All Categories') }}</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                                {{ ucfirst($category) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Min Roles') }}</label>
                                    <input type="number" name="min_roles" class="form-control" placeholder="0" value="{{ request('min_roles') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Actions') }}</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <em class="icon ni ni-search"></em>
                                        </button>
                                        <a href="{{ route('admin.settings.permissions.index') }}" class="btn btn-outline-secondary">
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
                            <form id="bulkActionForm" method="POST" action="{{ route('admin.settings.permissions.bulk-action') }}">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Bulk Action') }}</label>
                                        <select name="action" class="form-select" required>
                                            <option value="">{{ __('Select Action') }}</option>
                                            <option value="assign_to_role">{{ __('Assign to Role') }}</option>
                                            <option value="remove_from_role">{{ __('Remove from Role') }}</option>
                                            <option value="delete">{{ __('Delete') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Role') }}</label>
                                        <select name="role_id" class="form-select">
                                            <option value="">{{ __('Select Role') }}</option>
                                            @foreach(\Spatie\Permission\Models\Role::all() as $role)
                                                <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                                            @endforeach
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

                <!-- Bulk Create Permissions -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Bulk Create Permissions') }}</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('admin.settings.permissions.bulk-create') }}">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Module Name') }}</label>
                                        <input type="text" name="module" class="form-control" placeholder="e.g., property" required>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">{{ __('Actions') }}</label>
                                        <input type="text" name="actions[]" class="form-control" placeholder="e.g., create, edit, delete, view" required>
                                        <small class="text-muted">Separate multiple actions with commas</small>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Description Prefix') }}</label>
                                        <input type="text" name="description_prefix" class="form-control" placeholder="e.g., Can">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label">&nbsp;</label>
                                        <button type="submit" class="btn btn-primary d-block">
                                            <em class="icon ni ni-plus"></em>
                                            {{ __('Create') }}
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Permissions Table -->
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
                                            <th>{{ __('Permission') }}</th>
                                            <th>{{ __('Category') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Roles') }}</th>
                                            <th>{{ __('Users') }}</th>
                                            <th>{{ __('Guard') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($permissions as $permission)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" class="form-check-input permission-checkbox">
                                                </td>
                                                <td>
                                                    <div class="fw-bold">{{ $permission->name }}</div>
                                                </td>
                                                <td>
                                                    @php
                                                        $category = explode('_', $permission->name)[0] ?? 'other';
                                                    @endphp
                                                    <span class="badge badge-sm badge-outline-primary">{{ ucfirst($category) }}</span>
                                                </td>
                                                <td>
                                                    <div class="text-muted">
                                                        {{ $permission->description ?? 'No description available' }}
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm badge-info">{{ $permission->roles_count }} roles</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm badge-success">{{ $permission->users_count }} users</span>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm badge-outline-secondary">{{ $permission->guard_name }}</span>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.settings.permissions.show', $permission) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                                                            <em class="icon ni ni-eye"></em>
                                                        </a>
                                                        <a href="{{ route('admin.settings.permissions.edit', $permission) }}" 
                                                           class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                                            <em class="icon ni ni-edit"></em>
                                                        </a>
                                                        @if($permission->roles_count == 0 && $permission->users_count == 0)
                                                            <form method="POST" action="{{ route('admin.settings.permissions.destroy', $permission) }}" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}"
                                                                        onclick="return confirm('Are you sure you want to delete this permission?')">
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
                                                        <em class="icon ni ni-key fs-2x"></em>
                                                        <p class="mt-2">{{ __('No permissions found') }}</p>
                                                        @can('manage_permissions')
                                                            <a href="{{ route('admin.settings.permissions.create') }}" class="btn btn-primary mt-2">
                                                                {{ __('Add First Permission') }}
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
                            @if($permissions->hasPages())
                                <div class="mt-3">
                                    {{ $permissions->appends(request()->query())->links() }}
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
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Individual checkbox change
document.querySelectorAll('.permission-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.permission-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
    });
});

function confirmBulkAction() {
    const action = document.querySelector('select[name="action"]').value;
    const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
    
    if (!action) {
        alert('Please select an action.');
        return false;
    }
    
    if (checkedBoxes.length === 0) {
        alert('Please select at least one permission.');
        return false;
    }
    
    if ((action === 'assign_to_role' || action === 'remove_from_role') && !document.querySelector('select[name="role_id"]').value) {
        alert('Please select a role.');
        return false;
    }
    
    const actionText = action.replace('_', ' ').toUpperCase();
    return confirm(`Are you sure you want to ${actionText} ${checkedBoxes.length} permission(s)?`);
}

// Add selected permission IDs to bulk form
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
    const permissionIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    // Add hidden inputs for selected permission IDs
    permissionIds.forEach(permissionId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'permission_ids[]';
        input.value = permissionId;
        this.appendChild(input);
    });
});

// Handle bulk create form
document.querySelector('form[action*="bulk-create"]').addEventListener('submit', function(e) {
    const actionsInput = document.querySelector('input[name="actions[]"]');
    const actions = actionsInput.value.split(',').map(action => action.trim()).filter(action => action);
    
    if (actions.length === 0) {
        e.preventDefault();
        alert('Please enter at least one action.');
        return false;
    }
    
    // Convert actions array to individual inputs
    actions.forEach(action => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'actions[]';
        input.value = action;
        this.appendChild(input);
    });
});
</script>
@endpush
