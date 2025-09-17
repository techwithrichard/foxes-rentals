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
                            <h3 class="nk-block-title page-title">{{ __('Advanced User Management') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Comprehensive user management with roles and permissions</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-menu-alt-r"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        @can('create_user')
                                            <li>
                                                <a href="{{ route('admin.users.advanced.create') }}" class="btn btn-primary">
                                                    <em class="icon ni ni-plus"></em>
                                                    <span>{{ __('Add User') }}</span>
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
                                                    <li><a class="dropdown-item" href="{{ route('admin.users.advanced.export') }}">CSV Export</a></li>
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
                                            <em class="icon ni ni-users"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Total Users') }}</div>
                                            <div class="h4 mb-0">{{ $users->total() }}</div>
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
                                            <div class="text-muted">{{ __('Active Users') }}</div>
                                            <div class="h4 mb-0">{{ $users->where('deleted_at', null)->count() }}</div>
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
                                            <em class="icon ni ni-mail"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Verified Users') }}</div>
                                            <div class="h4 mb-0">{{ $users->where('email_verified_at', '!=', null)->count() }}</div>
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
                                            <em class="icon ni ni-shield-check"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Admin Users') }}</div>
                                            <div class="h4 mb-0">{{ $users->filter(function($user) { return $user->hasRole(['admin', 'super_admin']); })->count() }}</div>
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
                            <form method="GET" action="{{ route('admin.users.advanced.index') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Search') }}</label>
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search users...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Role') }}</label>
                                    <select name="role" class="form-select">
                                        <option value="">{{ __('All Roles') }}</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>
                                                {{ ucfirst($role->name) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Status') }}</label>
                                    <select name="status" class="form-select">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Email Verified') }}</label>
                                    <select name="email_verified" class="form-select">
                                        <option value="">{{ __('All') }}</option>
                                        <option value="verified" {{ request('email_verified') == 'verified' ? 'selected' : '' }}>{{ __('Verified') }}</option>
                                        <option value="unverified" {{ request('email_verified') == 'unverified' ? 'selected' : '' }}>{{ __('Unverified') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Actions') }}</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <em class="icon ni ni-search"></em>
                                        </button>
                                        <a href="{{ route('admin.users.advanced.index') }}" class="btn btn-outline-secondary">
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
                            <form id="bulkActionForm" method="POST" action="{{ route('admin.users.advanced.bulk-action') }}">
                                @csrf
                                <div class="row g-3 align-items-end">
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Bulk Action') }}</label>
                                        <select name="action" class="form-select" required>
                                            <option value="">{{ __('Select Action') }}</option>
                                            <option value="assign_role">{{ __('Assign Role') }}</option>
                                            <option value="remove_role">{{ __('Remove Role') }}</option>
                                            <option value="delete">{{ __('Delete') }}</option>
                                            <option value="restore">{{ __('Restore') }}</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Role') }}</label>
                                        <select name="role" class="form-select">
                                            <option value="">{{ __('Select Role') }}</option>
                                            @foreach($roles as $role)
                                                <option value="{{ $role->name }}">{{ ucfirst($role->name) }}</option>
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

                <!-- Users Table -->
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
                                            <th>{{ __('User') }}</th>
                                            <th>{{ __('Contact') }}</th>
                                            <th>{{ __('Roles') }}</th>
                                            <th>{{ __('Permissions') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Last Login') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($users as $user)
                                            <tr>
                                                <td>
                                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" class="form-check-input user-checkbox">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar avatar-sm me-2">
                                                            <span class="avatar-text bg-primary">{{ substr($user->name, 0, 2) }}</span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold">{{ $user->name }}</div>
                                                            <small class="text-muted">ID: {{ $user->id }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div>
                                                        <div class="fw-bold">{{ $user->email }}</div>
                                                        @if($user->phone)
                                                            <small class="text-muted">{{ $user->phone }}</small>
                                                        @endif
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-wrap gap-1">
                                                        @foreach($user->roles as $role)
                                                            <span class="badge badge-sm badge-outline-primary">{{ ucfirst($role->name) }}</span>
                                                        @endforeach
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm badge-info">{{ $user->permissions_count }} permissions</span>
                                                </td>
                                                <td>
                                                    @if($user->deleted_at)
                                                        <span class="badge badge-sm badge-danger">{{ __('Inactive') }}</span>
                                                    @else
                                                        <span class="badge badge-sm badge-success">{{ __('Active') }}</span>
                                                    @endif
                                                    <br>
                                                    @if($user->email_verified_at)
                                                        <small class="text-success">{{ __('Verified') }}</small>
                                                    @else
                                                        <small class="text-warning">{{ __('Unverified') }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.users.advanced.show', $user) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                                                            <em class="icon ni ni-eye"></em>
                                                        </a>
                                                        <a href="{{ route('admin.users.advanced.edit', $user) }}" 
                                                           class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                                            <em class="icon ni ni-edit"></em>
                                                        </a>
                                                        @if($user->deleted_at)
                                                            <form method="POST" action="{{ route('admin.users.advanced.restore', $user) }}" style="display: inline;">
                                                                @csrf
                                                                <button type="submit" class="btn btn-sm btn-outline-success" title="{{ __('Restore') }}">
                                                                    <em class="icon ni ni-undo"></em>
                                                                </button>
                                                            </form>
                                                        @else
                                                            <form method="POST" action="{{ route('admin.users.advanced.destroy', $user) }}" style="display: inline;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="{{ __('Delete') }}"
                                                                        onclick="return confirm('Are you sure you want to delete this user?')">
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
                                                        <em class="icon ni ni-users fs-2x"></em>
                                                        <p class="mt-2">{{ __('No users found') }}</p>
                                                        @can('create_user')
                                                            <a href="{{ route('admin.users.advanced.create') }}" class="btn btn-primary mt-2">
                                                                {{ __('Add First User') }}
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
                            @if($users->hasPages())
                                <div class="mt-3">
                                    {{ $users->appends(request()->query())->links() }}
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
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Individual checkbox change
document.querySelectorAll('.user-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const allCheckboxes = document.querySelectorAll('.user-checkbox');
        const checkedCheckboxes = document.querySelectorAll('.user-checkbox:checked');
        const selectAllCheckbox = document.getElementById('selectAll');
        
        selectAllCheckbox.checked = allCheckboxes.length === checkedCheckboxes.length;
        selectAllCheckbox.indeterminate = checkedCheckboxes.length > 0 && checkedCheckboxes.length < allCheckboxes.length;
    });
});

function confirmBulkAction() {
    const action = document.querySelector('select[name="action"]').value;
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    
    if (!action) {
        alert('Please select an action.');
        return false;
    }
    
    if (checkedBoxes.length === 0) {
        alert('Please select at least one user.');
        return false;
    }
    
    const actionText = action.replace('_', ' ').toUpperCase();
    return confirm(`Are you sure you want to ${actionText} ${checkedBoxes.length} user(s)?`);
}

// Add selected user IDs to bulk form
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    const userIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    // Add hidden inputs for selected user IDs
    userIds.forEach(userId => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'user_ids[]';
        input.value = userId;
        this.appendChild(input);
    });
});
</script>
@endpush
