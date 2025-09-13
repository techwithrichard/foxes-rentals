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
                            <h3 class="nk-block-title page-title">{{ __('All Rent Properties') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage and view all rental properties in the system</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-menu-alt-r"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        @can('create rental property')
                                            <li>
                                                <a href="{{ route('admin.rental-properties.create') }}" class="btn btn-primary">
                                                    <em class="icon ni ni-plus"></em>
                                                    <span>{{ __('Add Property') }}</span>
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
                                                    <li><a class="dropdown-item" href="#" onclick="exportToExcel()">Excel</a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="exportToPDF()">PDF</a></li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filters and Search -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-body">
                            <form method="GET" action="{{ route('admin.rental-properties.all') }}" class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Filter Status') }}</label>
                                    <select name="status" class="form-select">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>{{ __('Inactive') }}</option>
                                        <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>{{ __('Maintenance') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Entries') }}</label>
                                    <select name="per_page" class="form-select">
                                        <option value="10" {{ request('per_page') == '10' ? 'selected' : '' }}>10</option>
                                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25</option>
                                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50</option>
                                        <option value="100" {{ request('per_page') == '100' ? 'selected' : '' }}>100</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">{{ __('Search') }}</label>
                                    <input type="text" name="search" class="form-control" placeholder="{{ __('Search properties...') }}" value="{{ request('search') }}">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Property Type') }}</label>
                                    <select name="property_type" class="form-select">
                                        <option value="">{{ __('All Types') }}</option>
                                        @foreach($propertyTypes as $type)
                                            <option value="{{ $type->id }}" {{ request('property_type') == $type->id ? 'selected' : '' }}>
                                                {{ $type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">{{ __('Actions') }}</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <em class="icon ni ni-search"></em>
                                        </button>
                                        <a href="{{ route('admin.rental-properties.all') }}" class="btn btn-outline-secondary">
                                            <em class="icon ni ni-reload"></em>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Properties Table -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th class="sortable" data-sort="id">
                                                # <em class="icon ni ni-arrows-v"></em>
                                            </th>
                                            <th class="sortable" data-sort="name">
                                                {{ __('Name') }} <em class="icon ni ni-arrows-v"></em>
                                            </th>
                                            <th class="sortable" data-sort="property_type">
                                                {{ __('Property Type') }} <em class="icon ni ni-arrows-v"></em>
                                            </th>
                                            <th class="sortable" data-sort="rent_amount">
                                                {{ __('Rent Price') }} <em class="icon ni ni-arrows-v"></em>
                                            </th>
                                            <th class="sortable" data-sort="city">
                                                {{ __('City') }} <em class="icon ni ni-arrows-v"></em>
                                            </th>
                                            <th class="sortable" data-sort="status">
                                                {{ __('Status') }} <em class="icon ni ni-arrows-v"></em>
                                            </th>
                                            <th class="sortable" data-sort="landlord">
                                                {{ __('Landlord') }} <em class="icon ni ni-arrows-v"></em>
                                            </th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rentalProperties as $property)
                                            <tr>
                                                <td>{{ $loop->iteration + ($rentalProperties->currentPage() - 1) * $rentalProperties->perPage() }}</td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        @if($property->images && count($property->images) > 0)
                                                            <div class="avatar avatar-sm me-2">
                                                                <img src="{{ $property->images[0] }}" alt="{{ $property->name }}" class="rounded">
                                                            </div>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $property->name }}</div>
                                                            @if($property->is_featured)
                                                                <span class="badge badge-sm badge-primary">{{ __('Featured') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm badge-outline-primary">
                                                        {{ $property->propertyType->name ?? 'N/A' }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="fw-bold text-primary">{{ $property->formatted_rent }}</div>
                                                    @if($property->deposit_amount)
                                                        <small class="text-muted">Deposit: {{ $property->formatted_deposit }}</small>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{ $property->address->city ?? 'N/A' }}, {{ $property->address->state ?? 'N/A' }}
                                                </td>
                                                <td>
                                                    @if($property->is_vacant)
                                                        <span class="badge badge-sm badge-danger">{{ __('Vacant') }}</span>
                                                    @else
                                                        <span class="badge badge-sm badge-success">{{ __('Occupied') }}</span>
                                                    @endif
                                                    <br>
                                                    <small class="text-muted">{{ ucfirst($property->status) }}</small>
                                                </td>
                                                <td>
                                                    {{ $property->landlord->name ?? 'Multi Owned' }}
                                                </td>
                                                <td>
                                                    <div class="d-flex gap-1">
                                                        <a href="{{ route('admin.rental-properties.show', $property) }}" 
                                                           class="btn btn-sm btn-outline-primary" title="{{ __('View') }}">
                                                            <em class="icon ni ni-eye"></em>
                                                        </a>
                                                        @can('edit rental property')
                                                            <a href="{{ route('admin.rental-properties.edit', $property) }}" 
                                                               class="btn btn-sm btn-outline-secondary" title="{{ __('Edit') }}">
                                                                <em class="icon ni ni-edit"></em>
                                                            </a>
                                                        @endcan
                                                        <div class="dropdown">
                                                            <a href="#" class="btn btn-sm btn-outline-light" data-bs-toggle="dropdown">
                                                                <em class="icon ni ni-more-h"></em>
                                                            </a>
                                                            <ul class="dropdown-menu">
                                                                <li>
                                                                    <a class="dropdown-item" href="#" 
                                                                       onclick="toggleFeatured('{{ $property->id }}')">
                                                                        @if($property->is_featured)
                                                                            <em class="icon ni ni-star-fill text-warning"></em> {{ __('Remove from Featured') }}
                                                                        @else
                                                                            <em class="icon ni ni-star text-muted"></em> {{ __('Mark as Featured') }}
                                                                        @endif
                                                                    </a>
                                                                </li>
                                                                <li>
                                                                    <a class="dropdown-item" href="#" 
                                                                       onclick="togglePublished('{{ $property->id }}')">
                                                                        @if($property->is_published)
                                                                            <em class="icon ni ni-eye-off text-muted"></em> {{ __('Unpublish') }}
                                                                        @else
                                                                            <em class="icon ni ni-eye text-success"></em> {{ __('Publish') }}
                                                                        @endif
                                                                    </a>
                                                                </li>
                                                                <li><hr class="dropdown-divider"></li>
                                                                @can('delete rental property')
                                                                    <li>
                                                                        <a class="dropdown-item text-danger" href="#" 
                                                                           onclick="deleteProperty('{{ $property->id }}')">
                                                                            <em class="icon ni ni-trash"></em> {{ __('Delete') }}
                                                                        </a>
                                                                    </li>
                                                                @endcan
                                                            </ul>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <em class="icon ni ni-home fs-2x"></em>
                                                        <p class="mt-2">{{ __('No rental properties found') }}</p>
                                                        @can('create rental property')
                                                            <a href="{{ route('admin.rental-properties.create') }}" class="btn btn-primary mt-2">
                                                                {{ __('Add First Property') }}
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
                            @if($rentalProperties->hasPages())
                                <div class="mt-3">
                                    {{ $rentalProperties->appends(request()->query())->links() }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Confirm Delete') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to delete this property? This action cannot be undone.') }}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function deleteProperty(propertyId) {
    document.getElementById('deleteForm').action = `/admin/rental-properties/${propertyId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function toggleFeatured(propertyId) {
    fetch(`/admin/rental-properties/${propertyId}/toggle-featured`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the property.');
    });
}

function togglePublished(propertyId) {
    fetch(`/admin/rental-properties/${propertyId}/toggle-published`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the property.');
    });
}

function exportToExcel() {
    // Implementation for Excel export
    alert('Excel export functionality will be implemented');
}

function exportToPDF() {
    // Implementation for PDF export
    alert('PDF export functionality will be implemented');
}
</script>
@endpush
