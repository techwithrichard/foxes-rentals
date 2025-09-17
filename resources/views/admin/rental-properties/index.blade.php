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
                            <h3 class="nk-block-title page-title">{{ __('Rental Properties') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage and view rental properties in the system</p>
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

                <!-- Quick Stats -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-lg bg-primary">
                                            <em class="icon ni ni-home"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Total Properties') }}</div>
                                            <div class="h4 mb-0">{{ $rentalProperties->total() }}</div>
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
                                            <div class="text-muted">{{ __('Active Properties') }}</div>
                                            <div class="h4 mb-0">{{ $rentalProperties->where('status', 'active')->count() }}</div>
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
                                            <em class="icon ni ni-home-alt"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Vacant Properties') }}</div>
                                            <div class="h4 mb-0">{{ $rentalProperties->where('is_vacant', true)->count() }}</div>
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
                                            <em class="icon ni ni-star"></em>
                                        </div>
                                        <div class="ms-3">
                                            <div class="text-muted">{{ __('Featured Properties') }}</div>
                                            <div class="h4 mb-0">{{ $rentalProperties->where('is_featured', true)->count() }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-md-3">
                            <a href="{{ route('admin.rental-properties.all') }}" class="card card-hover">
                                <div class="card-body text-center">
                                    <em class="icon ni ni-list fs-2x text-primary"></em>
                                    <h6 class="mt-2">{{ __('All Properties') }}</h6>
                                    <p class="text-muted small">View all rental properties</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.rental-properties.vacant') }}" class="card card-hover">
                                <div class="card-body text-center">
                                    <em class="icon ni ni-home-alt fs-2x text-warning"></em>
                                    <h6 class="mt-2">{{ __('Vacant Properties') }}</h6>
                                    <p class="text-muted small">Properties available for rent</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.rental-properties.occupied') }}" class="card card-hover">
                                <div class="card-body text-center">
                                    <em class="icon ni ni-check-circle fs-2x text-success"></em>
                                    <h6 class="mt-2">{{ __('Occupied Properties') }}</h6>
                                    <p class="text-muted small">Currently rented properties</p>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('admin.rental-properties.featured') }}" class="card card-hover">
                                <div class="card-body text-center">
                                    <em class="icon ni ni-star fs-2x text-info"></em>
                                    <h6 class="mt-2">{{ __('Featured Properties') }}</h6>
                                    <p class="text-muted small">Highlighted properties</p>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Recent Properties -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Recent Properties') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Name') }}</th>
                                            <th>{{ __('Type') }}</th>
                                            <th>{{ __('Rent') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Landlord') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($rentalProperties->take(10) as $property)
                                            <tr>
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
                                                    <div class="fw-bold text-primary">{{ $property->formatted_rent ?? 'KSh ' . number_format($property->rent_amount) }}</div>
                                                    @if($property->deposit_amount)
                                                        <small class="text-muted">Deposit: {{ $property->formatted_deposit ?? 'KSh ' . number_format($property->deposit_amount) }}</small>
                                                    @endif
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
                                                    {{ $property->landlord->name ?? 'N/A' }}
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
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
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
                            
                            @if($rentalProperties->count() > 10)
                                <div class="text-center mt-3">
                                    <a href="{{ route('admin.rental-properties.all') }}" class="btn btn-outline-primary">
                                        {{ __('View All Properties') }}
                                    </a>
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
