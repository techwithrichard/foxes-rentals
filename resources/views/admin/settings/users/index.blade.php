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
                                            <h5 class="nk-block-title">{{ __('User Management') }}</h5>
                                            <span>{{ __('Manage system users, roles, and permissions') }}</span>
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
                                                            <a href="{{ route('admin.settings.users.create') }}" class="btn btn-primary">
                                                                <em class="icon ni ni-plus"></em>
                                                                <span>{{ __('Add User') }}</span>
                                                            </a>
                                                        </li>
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-outline-success" onclick="exportUsers()">
                                                                <em class="icon ni ni-download"></em>
                                                                <span>{{ __('Export') }}</span>
                                                            </button>
                                                        </li>
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-outline-info" onclick="showImportModal()">
                                                                <em class="icon ni ni-upload"></em>
                                                                <span>{{ __('Import') }}</span>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Statistics Overview -->
                                <div class="nk-block">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="card card-bordered">
                                                <div class="card-inner">
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-primary me-3">
                                                            <em class="icon ni ni-users" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Total Users') }}</h6>
                                                            <h4 class="mb-0">{{ $statistics['total_users'] ?? 0 }}</h4>
                                                            <small class="text-muted">{{ $statistics['recent_registrations'] ?? 0 }} this month</small>
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
                                                            <em class="icon ni ni-user-check" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Active Users') }}</h6>
                                                            <h4 class="mb-0">{{ $statistics['active_users'] ?? 0 }}</h4>
                                                            <small class="text-muted">{{ $statistics['inactive_users'] ?? 0 }} inactive</small>
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
                                                            <em class="icon ni ni-signin" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Logins Today') }}</h6>
                                                            <h4 class="mb-0">{{ $statistics['login_statistics']['logins_today'] ?? 0 }}</h4>
                                                            <small class="text-muted">{{ $statistics['login_statistics']['unique_users_logged_in_today'] ?? 0 }} unique</small>
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
                                                            <em class="icon ni ni-alert-circle" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Failed Logins') }}</h6>
                                                            <h4 class="mb-0">{{ $statistics['login_statistics']['failed_logins_today'] ?? 0 }}</h4>
                                                            <small class="text-muted">Today</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Filters -->
                                <div class="row g-3 mb-4">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Role') }}</label>
                                            <select class="form-select" id="filter-role" onchange="filterUsers()">
                                                <option value="">{{ __('All Roles') }}</option>
                                                @foreach($roles as $role)
                                                    <option value="{{ $role->name }}" {{ request('role') === $role->name ? 'selected' : '' }}>
                                                        {{ $role->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Status') }}</label>
                                            <select class="form-select" id="filter-status" onchange="filterUsers()">
                                                <option value="">{{ __('All Status') }}</option>
                                                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Search') }}</label>
                                            <input type="text" class="form-control" id="search-input" placeholder="{{ __('Search users...') }}" 
                                                   value="{{ request('search') }}" onkeyup="filterUsers()">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label class="form-label">&nbsp;</label>
                                            <div class="btn-group w-100" role="group">
                                                <button type="button" class="btn btn-outline-secondary" onclick="clearFilters()">
                                                    <em class="icon ni ni-filter-clear"></em>
                                                </button>
                                                <button type="button" class="btn btn-outline-primary" onclick="refreshData()">
                                                    <em class="icon ni ni-refresh"></em>
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

                                <!-- Users Table -->
                                <div class="nk-block">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>
                                                        <input type="checkbox" id="select-all" onchange="toggleSelectAll()">
                                                    </th>
                                                    <th>{{ __('User') }}</th>
                                                    <th>{{ __('Contact') }}</th>
                                                    <th>{{ __('Roles') }}</th>
                                                    <th>{{ __('Status') }}</th>
                                                    <th>{{ __('Profile') }}</th>
                                                    <th>{{ __('Last Login') }}</th>
                                                    <th>{{ __('Created') }}</th>
                                                    <th>{{ __('Actions') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($users as $user)
                                                    <tr data-user-id="{{ $user->id }}" data-status="{{ $user->is_active ? 'active' : 'inactive' }}">
                                                        <td>
                                                            <input type="checkbox" class="user-checkbox" value="{{ $user->id }}" onchange="updateBulkActions()">
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <div class="user-avatar me-3">
                                                                    <div class="avatar avatar-sm bg-primary">
                                                                        @if($user->profile && $user->profile->profile_picture)
                                                                            <img src="{{ $user->profile->profile_picture_url }}" alt="{{ $user->name }}">
                                                                        @else
                                                                            <span>{{ $user->initials }}</span>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">{{ $user->display_name }}</h6>
                                                                    <small class="text-muted">ID: {{ $user->id }}</small>
                                                                    @if($user->profile)
                                                                        <br><small class="text-muted">{{ $user->profile->completion_percentage }}% complete</small>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <strong>{{ $user->email }}</strong>
                                                                @if($user->phone)
                                                                    <br><small class="text-muted">{{ $user->phone }}</small>
                                                                @endif
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex flex-wrap gap-1">
                                                                @forelse($user->roles as $role)
                                                                    <span class="badge badge-outline-primary">{{ $role->name }}</span>
                                                                @empty
                                                                    <span class="text-muted">{{ __('No roles') }}</span>
                                                                @endforelse
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span class="badge badge-{{ $user->is_active ? 'success' : 'secondary' }}">
                                                                {{ $user->is_active ? __('Active') : __('Inactive') }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            @if($user->profile)
                                                                <div class="d-flex align-items-center">
                                                                    <div class="progress progress-sm me-2" style="width: 60px;">
                                                                        <div class="progress-bar" style="width: {{ $user->profile->completion_percentage }}%"></div>
                                                                    </div>
                                                                    <small class="text-muted">{{ $user->profile->completion_percentage }}%</small>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">{{ __('No profile') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            @if($user->last_login_at)
                                                                <div>
                                                                    <small class="text-muted">{{ $user->last_login_at->diffForHumans() }}</small>
                                                                    <br><small class="text-muted">{{ $user->last_login_at->format('M d, Y H:i') }}</small>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">{{ __('Never') }}</span>
                                                            @endif
                                                        </td>
                                                        <td>
                                                            <div>
                                                                <small class="text-muted">{{ $user->created_at->format('M d, Y') }}</small>
                                                                <br><small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="btn-group" role="group">
                                                                <a href="{{ route('admin.settings.users.show', $user) }}" class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                                                                    <em class="icon ni ni-eye"></em>
                                                                </a>
                                                                <a href="{{ route('admin.settings.users.edit', $user) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                                                    <em class="icon ni ni-edit"></em>
                                                                </a>
                                                                <button type="button" class="btn btn-sm btn-outline-{{ $user->is_active ? 'warning' : 'success' }}" 
                                                                        onclick="toggleStatus('{{ $user->id }}')" 
                                                                        title="{{ $user->is_active ? __('Deactivate') : __('Activate') }}">
                                                                    <em class="icon ni ni-{{ $user->is_active ? 'pause' : 'play' }}"></em>
                                                                </button>
                                                                @if($user->id !== auth()->id())
                                                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deleteUser('{{ $user->id }}')" title="{{ __('Delete') }}">
                                                                    <em class="icon ni ni-trash"></em>
                                                                </button>
                                                                @endif
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center py-4">
                                                            <div class="text-muted">
                                                                <em class="icon ni ni-users" style="font-size: 3rem;"></em>
                                                                <h6 class="mt-3">{{ __('No Users Found') }}</h6>
                                                                <p>{{ __('Get started by adding your first user') }}</p>
                                                                <a href="{{ route('admin.settings.users.create') }}" class="btn btn-primary">
                                                                    <em class="icon ni ni-plus"></em>
                                                                    {{ __('Add User') }}
                                                                </a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Pagination -->
                                    @if($users->hasPages())
                                        <div class="mt-4">
                                            {{ $users->links() }}
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

<!-- Import Modal -->
<div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="importModalLabel">{{ __('Import Users') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="import-form" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">{{ __('CSV File') }}</label>
                        <input type="file" class="form-control" name="csv_file" accept=".csv,.txt" required>
                        <div class="form-note">{{ __('Upload a CSV file with user data. Required columns: name, email') }}</div>
                    </div>
                    <div class="alert alert-info">
                        <em class="icon ni ni-info-fill me-2"></em>
                        <strong>{{ __('CSV Format:') }}</strong>
                        <br>name, email, phone, role, is_active
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="submit" class="btn btn-primary">{{ __('Import Users') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
let selectedUsers = new Set();

function filterUsers() {
    const role = document.getElementById('filter-role').value.toLowerCase();
    const status = document.getElementById('filter-status').value.toLowerCase();
    const search = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr[data-user-id]');
    
    rows.forEach(row => {
        const rowStatus = row.getAttribute('data-status');
        const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const roles = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        
        const roleMatch = !role || roles.includes(role);
        const statusMatch = !status || rowStatus === status;
        const searchMatch = !search || name.includes(search) || email.includes(search);
        
        row.style.display = (roleMatch && statusMatch && searchMatch) ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('filter-role').value = '';
    document.getElementById('filter-status').value = '';
    document.getElementById('search-input').value = '';
    filterUsers();
}

function refreshData() {
    location.reload();
}

function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.user-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
        if (selectAll.checked) {
            selectedUsers.add(checkbox.value);
        } else {
            selectedUsers.delete(checkbox.value);
        }
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.user-checkbox:checked');
    selectedUsers.clear();
    checkboxes.forEach(checkbox => selectedUsers.add(checkbox.value));
    
    document.getElementById('selected-count').textContent = selectedUsers.size;
    document.getElementById('bulk-actions').style.display = selectedUsers.size > 0 ? 'block' : 'none';
}

function bulkAction(action) {
    if (selectedUsers.size === 0) {
        alert('Please select at least one user');
        return;
    }
    
    const actionText = {
        'activate': 'activate',
        'deactivate': 'deactivate',
        'delete': 'delete'
    }[action];
    
    if (!confirm(`Are you sure you want to ${actionText} ${selectedUsers.size} user(s)?`)) {
        return;
    }
    
    fetch('{{ route("admin.settings.users.bulk-action") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            action: action,
            user_ids: Array.from(selectedUsers)
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

function toggleStatus(userId) {
    fetch(`{{ url('admin/settings/users') }}/${userId}/toggle-status`, {
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

function deleteUser(userId) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    fetch(`{{ url('admin/settings/users') }}/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => {
        if (response.ok) {
            showAlert('User deleted successfully', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert('Failed to delete user', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while deleting user', 'danger');
    });
}

function exportUsers() {
    const filters = {
        role: document.getElementById('filter-role').value,
        status: document.getElementById('filter-status').value,
        search: document.getElementById('search-input').value
    };
    
    const params = new URLSearchParams(filters);
    const url = '{{ route("admin.settings.users.export") }}?' + params.toString();
    
    window.open(url, '_blank');
}

function showImportModal() {
    const modal = new bootstrap.Modal(document.getElementById('importModal'));
    modal.show();
}

// Handle import form submission
document.getElementById('import-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    submitBtn.innerHTML = '<em class="icon ni ni-loading"></em> Importing...';
    submitBtn.disabled = true;
    
    fetch('{{ route("admin.settings.users.import") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        
        if (data.success) {
            showAlert(data.message, 'success');
            document.getElementById('importModal').querySelector('.btn-close').click();
            setTimeout(() => location.reload(), 1000);
        } else {
            showAlert(data.message, 'danger');
        }
    })
    .catch(error => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
        console.error('Error:', error);
        showAlert('An error occurred while importing users', 'danger');
    });
});

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