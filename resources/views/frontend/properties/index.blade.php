@extends('web.web_layout')

@section('content')
<!-- Page Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-6 fw-bold mb-2">Properties for Rent</h1>
                <p class="text-muted mb-0">Find your perfect home from our extensive collection of properties</p>
            </div>
            <div class="col-lg-6 text-end">
                <div class="property-count">
                    <span class="badge bg-primary fs-6 px-3 py-2">{{ $properties->total() }} Properties Found</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Search Filters -->
<section class="py-4 bg-white border-bottom">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('frontend.properties.search') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="search" placeholder="Search properties..." value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="type">
                            <option value="">All Types</option>
                            <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                            <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>House</option>
                            <option value="studio" {{ request('type') == 'studio' ? 'selected' : '' }}>Studio</option>
                            <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="min_rent" placeholder="Min Rent" value="{{ request('min_rent') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="number" class="form-control" name="max_rent" placeholder="Max Rent" value="{{ request('max_rent') }}">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="location" placeholder="Location" value="{{ request('location') }}">
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="icon ni ni-search"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Properties Grid -->
<section class="py-5">
    <div class="container">
        @if($properties->count() > 0)
        <div class="row g-4">
            @foreach($properties as $property)
            <div class="col-lg-4 col-md-6">
                <div class="property-card bg-white rounded-4 shadow-sm h-100 hover-lift">
                    <div class="property-image position-relative">
                        @php
                            // Use placeholder images based on property type
                            $placeholderImages = [
                                'apartment' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                                'house' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                                'studio' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                                'commercial' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'
                            ];
                            $defaultImage = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';
                            $propertyImage = $placeholderImages[strtolower($property->type ?? 'apartment')] ?? $defaultImage;
                        @endphp
                        <img src="{{ $propertyImage }}" alt="{{ $property->name }}" class="w-100 rounded-top-4" style="height: 250px; object-fit: cover;">
                        
                        <div class="property-badge position-absolute top-0 end-0 m-3">
                            <span class="badge bg-success">{{ $property->is_vacant ? 'Available' : 'Occupied' }}</span>
                        </div>
                        <div class="property-type position-absolute top-0 start-0 m-3">
                            <span class="badge bg-primary">{{ $property->type ?? 'Property' }}</span>
                        </div>
                        <div class="property-actions position-absolute bottom-0 end-0 m-3">
                            <button class="btn btn-light btn-sm rounded-circle" onclick="toggleFavorite(this)">
                                <i class="icon ni ni-heart"></i>
                            </button>
                        </div>
                    </div>
                    <div class="property-content p-4">
                        <h5 class="fw-bold mb-2">{{ $property->name }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="icon ni ni-map-pin me-1"></i>
                            @if($property->address)
                                {{ $property->address->city }}, {{ $property->address->state }}
                            @else
                                Location not specified
                            @endif
                        </p>
                        
                        @if($property->description)
                        <p class="text-muted small mb-3">{{ Str::limit($property->description, 100) }}</p>
                        @endif
                        
                        <div class="property-details d-flex justify-content-between mb-3">
                            @if($property->houses && $property->houses->count() > 0)
                                <span class="small">
                                    <i class="icon ni ni-bed me-1"></i>{{ $property->houses->count() }} Units
                                </span>
                            @endif
                            <span class="small">
                                <i class="icon ni ni-calendar me-1"></i>{{ $property->created_at->diffForHumans() }}
                            </span>
                        </div>
                        
                        <div class="property-footer d-flex justify-content-between align-items-center">
                            <div class="property-price">
                                <span class="fw-bold text-primary fs-5">${{ number_format($property->rent, 2) }}/month</span>
                            </div>
                            <a href="{{ route('frontend.properties.show', $property->id) }}" class="btn btn-primary btn-sm">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        
        <!-- Pagination -->
        <div class="row mt-5">
            <div class="col-12">
                {{ $properties->appends(request()->query())->links() }}
            </div>
        </div>
        @else
        <div class="text-center py-5">
            <div class="empty-state">
                <i class="icon ni ni-home text-muted" style="font-size: 4rem;"></i>
                <h3 class="mt-3 mb-2">No Properties Found</h3>
                <p class="text-muted mb-4">Try adjusting your search criteria or browse all properties.</p>
                <a href="{{ route('frontend.properties.index') }}" class="btn btn-primary">View All Properties</a>
            </div>
        </div>
        @endif
    </div>
</section>

<style>
.property-card {
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.property-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
    border-color: #667eea;
}

.hover-lift {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}

.property-badge {
    z-index: 2;
}

.property-type {
    z-index: 2;
}

.property-actions {
    z-index: 2;
}

.empty-state {
    padding: 3rem 0;
}
</style>

<script>
function toggleFavorite(button) {
    const icon = button.querySelector('i');
    if (icon.classList.contains('ni-heart')) {
        icon.classList.remove('ni-heart');
        icon.classList.add('ni-heart-fill');
        button.classList.add('text-danger');
    } else {
        icon.classList.remove('ni-heart-fill');
        icon.classList.add('ni-heart');
        button.classList.remove('text-danger');
    }
}
</script>
@endsection
