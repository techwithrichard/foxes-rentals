@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="nk-block-head nk-block-head-lg">
                                <div class="nk-block-between">
                                    <div class="nk-block-head-content">
                                        <h5 class="nk-block-title">{{ __('Property Type Details') }}</h5>
                                        <span>{{ __('View property type information and usage statistics') }}</span>
                                    </div>
                                    <div class="nk-block-head-content">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.property-types.edit', $propertyType) }}" class="btn btn-primary">
                                                <em class="icon ni ni-edit"></em>
                                                <span>{{ __('Edit') }}</span>
                                            </a>
                                            <a href="{{ route('admin.property-types.index') }}" class="btn btn-outline-secondary">
                                                <em class="icon ni ni-arrow-left"></em>
                                                <span>{{ __('Back to List') }}</span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row g-4">
                                <!-- Property Type Information -->
                                <div class="col-lg-8">
                                    <div class="card">
                                        <div class="card-inner">
                                            <h6 class="title">{{ __('Property Type Information') }}</h6>
                                            
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Name') }}</label>
                                                        <div class="form-control-plaintext">
                                                            <div class="d-flex align-items-center">
                                                                @if($propertyType->icon)
                                                                    <em class="icon ni {{ $propertyType->icon }} me-2" 
                                                                        style="color: {{ $propertyType->color }};"></em>
                                                                @endif
                                                                <strong>{{ $propertyType->name }}</strong>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Category') }}</label>
                                                        <div class="form-control-plaintext">
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
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Description') }}</label>
                                                        <div class="form-control-plaintext">
                                                            @if($propertyType->description)
                                                                {{ $propertyType->description }}
                                                            @else
                                                                <span class="text-muted">{{ __('No description provided') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Status') }}</label>
                                                        <div class="form-control-plaintext">
                                                            <span class="badge badge-{{ $propertyType->is_active ? 'success' : 'secondary' }}">
                                                                {{ $propertyType->is_active ? __('Active') : __('Inactive') }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Sort Order') }}</label>
                                                        <div class="form-control-plaintext">
                                                            {{ $propertyType->sort_order }}
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-md-4">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Created') }}</label>
                                                        <div class="form-control-plaintext">
                                                            {{ $propertyType->created_at->format('M d, Y H:i') }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Visual Settings -->
                                <div class="col-lg-4">
                                    <div class="card">
                                        <div class="card-inner">
                                            <h6 class="title">{{ __('Visual Settings') }}</h6>
                                            
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Icon') }}</label>
                                                        <div class="form-control-plaintext">
                                                            @if($propertyType->icon)
                                                                <div class="d-flex align-items-center">
                                                                    <em class="icon ni {{ $propertyType->icon }} fs-2xl me-3" 
                                                                        style="color: {{ $propertyType->color }};"></em>
                                                                    <code>{{ $propertyType->icon }}</code>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">{{ __('No icon set') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label class="form-label">{{ __('Color') }}</label>
                                                        <div class="form-control-plaintext">
                                                            @if($propertyType->color)
                                                                <div class="d-flex align-items-center">
                                                                    <div class="color-preview me-3" 
                                                                         style="width: 30px; height: 30px; background-color: {{ $propertyType->color }}; border-radius: 4px; border: 1px solid #ddd;"></div>
                                                                    <code>{{ $propertyType->color }}</code>
                                                                </div>
                                                            @else
                                                                <span class="text-muted">{{ __('No color set') }}</span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Usage Statistics -->
                            <div class="row g-4 mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-inner">
                                            <h6 class="title">{{ __('Usage Statistics') }}</h6>
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <div class="text-primary fs-2xl fw-bold">{{ $propertyType->properties_count ?? 0 }}</div>
                                                        <div class="text-muted">{{ __('Total Properties') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <div class="text-success fs-2xl fw-bold">{{ $propertyType->rental_properties_count ?? 0 }}</div>
                                                        <div class="text-muted">{{ __('Rental Properties') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <div class="text-info fs-2xl fw-bold">{{ $propertyType->sale_properties_count ?? 0 }}</div>
                                                        <div class="text-muted">{{ __('Sale Properties') }}</div>
                                                    </div>
                                                </div>
                                                <div class="col-md-3">
                                                    <div class="text-center">
                                                        <div class="text-warning fs-2xl fw-bold">{{ $propertyType->lease_properties_count ?? 0 }}</div>
                                                        <div class="text-muted">{{ __('Lease Properties') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Recent Properties -->
                            @if(($propertyType->properties_count ?? 0) > 0)
                            <div class="row g-4 mt-4">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-inner">
                                            <h6 class="title">{{ __('Recent Properties') }}</h6>
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>{{ __('Property Name') }}</th>
                                                            <th>{{ __('Type') }}</th>
                                                            <th>{{ __('Status') }}</th>
                                                            <th>{{ __('Created') }}</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($propertyType->properties->take(5) as $property)
                                                            <tr>
                                                                <td>
                                                                    <strong>{{ $property->name ?? 'N/A' }}</strong>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-info">{{ ucfirst($property->type ?? 'N/A') }}</span>
                                                                </td>
                                                                <td>
                                                                    <span class="badge badge-{{ $property->status == 'active' ? 'success' : 'secondary' }}">
                                                                        {{ ucfirst($property->status ?? 'N/A') }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    {{ $property->created_at->format('M d, Y') }}
                                                                </td>
                                                            </tr>
                                                        @empty
                                                            <tr>
                                                                <td colspan="4" class="text-center text-muted">
                                                                    {{ __('No properties found') }}
                                                                </td>
                                                            </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
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
