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
                                            <h5 class="nk-block-title">{{ __('Property Settings Dashboard') }}</h5>
                                            <span>{{ __('Manage property types, amenities, pricing rules, and lease templates') }}</span>
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
                                                            <button type="button" class="btn btn-outline-primary" onclick="exportPropertySettings()">
                                                                <em class="icon ni ni-download"></em>
                                                                <span>{{ __('Export Settings') }}</span>
                                                            </button>
                                                        </li>
                                                        <li class="nk-block-tools-opt">
                                                            <button type="button" class="btn btn-outline-success" onclick="refreshStatistics()">
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

                                <!-- Statistics Overview -->
                                <div class="nk-block">
                                    <div class="row g-3">
                                        <div class="col-md-3">
                                            <div class="card card-bordered">
                                                <div class="card-inner">
                                                    <div class="d-flex align-items-center">
                                                        <div class="text-primary me-3">
                                                            <em class="icon ni ni-building" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Property Types') }}</h6>
                                                            <h4 class="mb-0">{{ $statistics['property_types']['total'] ?? 0 }}</h4>
                                                            <small class="text-muted">{{ $statistics['property_types']['active'] ?? 0 }} active</small>
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
                                                            <em class="icon ni ni-star" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Amenities') }}</h6>
                                                            <h4 class="mb-0">{{ $statistics['amenities']['total'] ?? 0 }}</h4>
                                                            <small class="text-muted">{{ $statistics['amenities']['categories'] ?? 0 }} categories</small>
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
                                                            <em class="icon ni ni-money" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Pricing Rules') }}</h6>
                                                            <h4 class="mb-0">{{ $statistics['pricing_rules']['total'] ?? 0 }}</h4>
                                                            <small class="text-muted">{{ $statistics['pricing_rules']['active'] ?? 0 }} active</small>
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
                                                            <em class="icon ni ni-file-text" style="font-size: 2rem;"></em>
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-1">{{ __('Lease Templates') }}</h6>
                                                            <h4 class="mb-0">{{ $statistics['lease_templates']['total'] ?? 0 }}</h4>
                                                            <small class="text-muted">{{ $statistics['lease_templates']['active'] ?? 0 }} active</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Quick Actions -->
                                <div class="nk-block">
                                    <div class="card card-bordered">
                                        <div class="card-header">
                                            <h6 class="card-title">{{ __('Quick Actions') }}</h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <a href="{{ route('admin.settings.property.types.create') }}" class="btn btn-outline-primary w-100">
                                                        <em class="icon ni ni-plus me-2"></em>
                                                        {{ __('Add Property Type') }}
                                                    </a>
                                                </div>
                                                <div class="col-md-3">
                                                    <a href="{{ route('admin.settings.property.amenities.create') }}" class="btn btn-outline-success w-100">
                                                        <em class="icon ni ni-plus me-2"></em>
                                                        {{ __('Add Amenity') }}
                                                    </a>
                                                </div>
                                                <div class="col-md-3">
                                                    <a href="{{ route('admin.settings.property.pricing.create') }}" class="btn btn-outline-info w-100">
                                                        <em class="icon ni ni-plus me-2"></em>
                                                        {{ __('Add Pricing Rule') }}
                                                    </a>
                                                </div>
                                                <div class="col-md-3">
                                                    <a href="{{ route('admin.settings.property.lease-templates.create') }}" class="btn btn-outline-warning w-100">
                                                        <em class="icon ni ni-plus me-2"></em>
                                                        {{ __('Add Lease Template') }}
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Recent Items -->
                                <div class="nk-block">
                                    <div class="row g-3">
                                        <!-- Recent Property Types -->
                                        <div class="col-md-6">
                                            <div class="card card-bordered">
                                                <div class="card-header">
                                                    <h6 class="card-title">{{ __('Recent Property Types') }}</h6>
                                                    <a href="{{ route('admin.settings.property.types') }}" class="btn btn-sm btn-outline-primary">
                                                        {{ __('View All') }}
                                                    </a>
                                                </div>
                                                <div class="card-body">
                                                    @forelse($propertyTypes->take(5) as $propertyType)
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    @if($propertyType->icon)
                                                                        <em class="icon ni {{ $propertyType->icon }}"></em>
                                                                    @else
                                                                        <em class="icon ni ni-building"></em>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">{{ $propertyType->name }}</h6>
                                                                    <small class="text-muted">
                                                                        {{ $propertyType->rental_properties_count + $propertyType->sale_properties_count }} properties
                                                                    </small>
                                                                </div>
                                                            </div>
                                                            <span class="badge badge-{{ $propertyType->is_active ? 'success' : 'secondary' }}">
                                                                {{ $propertyType->is_active ? __('Active') : __('Inactive') }}
                                                            </span>
                                                        </div>
                                                    @empty
                                                        <div class="text-center py-4">
                                                            <em class="icon ni ni-building" style="font-size: 2rem; color: #ccc;"></em>
                                                            <p class="text-muted mt-2">{{ __('No property types found') }}</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Recent Amenities -->
                                        <div class="col-md-6">
                                            <div class="card card-bordered">
                                                <div class="card-header">
                                                    <h6 class="card-title">{{ __('Recent Amenities') }}</h6>
                                                    <a href="{{ route('admin.settings.property.amenities') }}" class="btn btn-sm btn-outline-success">
                                                        {{ __('View All') }}
                                                    </a>
                                                </div>
                                                <div class="card-body">
                                                    @forelse($amenities->take(5) as $amenity)
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <div class="d-flex align-items-center">
                                                                <div class="me-3">
                                                                    @if($amenity->icon)
                                                                        <em class="icon ni {{ $amenity->icon }}"></em>
                                                                    @else
                                                                        <em class="icon ni ni-star"></em>
                                                                    @endif
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-1">{{ $amenity->name }}</h6>
                                                                    <small class="text-muted">{{ $amenity->category_display_name }}</small>
                                                                </div>
                                                            </div>
                                                            <div class="text-end">
                                                                <span class="badge badge-{{ $amenity->is_active ? 'success' : 'secondary' }}">
                                                                    {{ $amenity->is_active ? __('Active') : __('Inactive') }}
                                                                </span>
                                                                @if($amenity->is_chargeable)
                                                                    <br><small class="text-muted">{{ $amenity->formatted_cost }}</small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="text-center py-4">
                                                            <em class="icon ni ni-star" style="font-size: 2rem; color: #ccc;"></em>
                                                            <p class="text-muted mt-2">{{ __('No amenities found') }}</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Usage Analytics -->
                                @if(!empty($statistics['usage_analytics']))
                                <div class="nk-block">
                                    <div class="row g-3">
                                        <!-- Most Used Property Types -->
                                        <div class="col-md-6">
                                            <div class="card card-bordered">
                                                <div class="card-header">
                                                    <h6 class="card-title">{{ __('Most Used Property Types') }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    @forelse($statistics['usage_analytics']['most_used_property_types'] as $type)
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <div>
                                                                <h6 class="mb-1">{{ $type['name'] }}</h6>
                                                                <small class="text-muted">
                                                                    {{ $type['rental_count'] }} rental, {{ $type['sale_count'] }} sale
                                                                </small>
                                                            </div>
                                                            <span class="badge badge-primary">{{ $type['total_count'] }}</span>
                                                        </div>
                                                    @empty
                                                        <div class="text-center py-4">
                                                            <em class="icon ni ni-chart-bar" style="font-size: 2rem; color: #ccc;"></em>
                                                            <p class="text-muted mt-2">{{ __('No usage data available') }}</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Most Used Amenities -->
                                        <div class="col-md-6">
                                            <div class="card card-bordered">
                                                <div class="card-header">
                                                    <h6 class="card-title">{{ __('Most Used Amenities') }}</h6>
                                                </div>
                                                <div class="card-body">
                                                    @forelse($statistics['usage_analytics']['most_used_amenities'] as $amenity)
                                                        <div class="d-flex align-items-center justify-content-between mb-3">
                                                            <div>
                                                                <h6 class="mb-1">{{ $amenity['name'] }}</h6>
                                                                <small class="text-muted">{{ $amenity['category'] }}</small>
                                                            </div>
                                                            <span class="badge badge-success">{{ $amenity['usage_count'] }}</span>
                                                        </div>
                                                    @empty
                                                        <div class="text-center py-4">
                                                            <em class="icon ni ni-chart-bar" style="font-size: 2rem; color: #ccc;"></em>
                                                            <p class="text-muted mt-2">{{ __('No usage data available') }}</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
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
function exportPropertySettings() {
    fetch('{{ route("admin.settings.property.export") }}', {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const blob = new Blob([JSON.stringify(data.settings, null, 2)], {type: 'application/json'});
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'property-settings-' + new Date().toISOString().split('T')[0] + '.json';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            showAlert('Property settings exported successfully!', 'success');
        } else {
            showAlert('Failed to export settings: ' + data.message, 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('An error occurred while exporting settings', 'danger');
    });
}

function refreshStatistics() {
    const refreshBtn = document.querySelector('button[onclick="refreshStatistics()"]');
    const originalText = refreshBtn.innerHTML;
    refreshBtn.innerHTML = '<em class="icon ni ni-loading"></em> Refreshing...';
    refreshBtn.disabled = true;
    
    // Simulate refresh by reloading the page
    setTimeout(() => {
        location.reload();
    }, 1000);
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
