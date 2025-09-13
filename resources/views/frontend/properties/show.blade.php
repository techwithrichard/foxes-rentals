@extends('web.web_layout')

@section('content')
<!-- Property Header -->
<section class="py-4 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('frontend.properties.index') }}">Properties</a></li>
                        <li class="breadcrumb-item active">{{ $property->name }}</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</section>

<!-- Property Details -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Property Images -->
            <div class="col-lg-8">
                <div class="property-gallery mb-4">
                    @php
                        // Use placeholder images based on property type
                        $placeholderImages = [
                            'apartment' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                            'house' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                            'studio' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                            'commercial' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'
                        ];
                        $defaultImage = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';
                        $mainImage = $placeholderImages[strtolower($property->type ?? 'apartment')] ?? $defaultImage;
                        
                        // Create multiple images for gallery (using different angles of same property type)
                        $galleryImages = [
                            $mainImage,
                            'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                            'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                            'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'
                        ];
                    @endphp
                    
                    <!-- Main Image -->
                    <div class="main-image mb-3">
                        <img src="{{ $mainImage }}" alt="{{ $property->name }}" class="w-100 rounded-4 shadow-sm" style="height: 400px; object-fit: cover;" id="mainImage">
                    </div>
                    
                    <!-- Thumbnail Images -->
                    <div class="thumbnail-images d-flex gap-2">
                        @foreach($galleryImages as $index => $image)
                        <img src="{{ $image }}" alt="{{ $property->name }}" class="thumbnail-img rounded-3" style="width: 80px; height: 60px; object-fit: cover; cursor: pointer;" onclick="changeMainImage('{{ $image }}')">
                        @endforeach
                    </div>
                </div>

                <!-- Property Information -->
                <div class="property-info">
                    <div class="row">
                        <div class="col-lg-8">
                            <h1 class="fw-bold mb-3">{{ $property->name }}</h1>
                            <p class="text-muted mb-3">
                                <i class="icon ni ni-map-pin me-2"></i>
                                @if($property->address)
                                    {{ $property->address->street }}, {{ $property->address->city }}, {{ $property->address->state }}
                                @else
                                    Location not specified
                                @endif
                            </p>
                            
                            @if($property->description)
                            <div class="description mb-4">
                                <h4 class="fw-bold mb-3">Description</h4>
                                <p class="text-muted">{{ $property->description }}</p>
                            </div>
                            @endif

                            <!-- Property Details -->
                            <div class="property-details mb-4">
                                <h4 class="fw-bold mb-3">Property Details</h4>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="detail-item p-3 bg-light rounded-3 text-center">
                                            <i class="icon ni ni-home text-primary mb-2" style="font-size: 1.5rem;"></i>
                                            <h6 class="fw-bold mb-1">Property Type</h6>
                                            <p class="text-muted mb-0">{{ $property->type ?? 'Not specified' }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="detail-item p-3 bg-light rounded-3 text-center">
                                            <i class="icon ni ni-building text-success mb-2" style="font-size: 1.5rem;"></i>
                                            <h6 class="fw-bold mb-1">Units Available</h6>
                                            <p class="text-muted mb-0">{{ $property->houses ? $property->houses->count() : 0 }}</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="detail-item p-3 bg-light rounded-3 text-center">
                                            <i class="icon ni ni-calendar text-warning mb-2" style="font-size: 1.5rem;"></i>
                                            <h6 class="fw-bold mb-1">Listed</h6>
                                            <p class="text-muted mb-0">{{ $property->created_at->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Amenities -->
                            <div class="amenities mb-4">
                                <h4 class="fw-bold mb-3">Amenities</h4>
                                <div class="row g-2">
                                    @php
                                        $amenities = ['Parking', 'WiFi', 'Security', 'Gym', 'Pool', 'Garden', 'Balcony', 'Air Conditioning'];
                                    @endphp
                                    @foreach($amenities as $amenity)
                                    <div class="col-md-3">
                                        <div class="amenity-item d-flex align-items-center p-2 bg-light rounded-3">
                                            <i class="icon ni ni-check-circle-fill text-success me-2"></i>
                                            <span class="small">{{ $amenity }}</span>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- 3D View Section -->
                            <div class="virtual-tour mb-4">
                                <h4 class="fw-bold mb-3">Virtual Tour</h4>
                                <div class="virtual-tour-container bg-light rounded-4 p-4 text-center" style="height: 300px;">
                                    <div class="d-flex align-items-center justify-content-center h-100">
                                        <div>
                                            <i class="icon ni ni-cube text-primary mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="fw-bold mb-2">3D Virtual Tour</h5>
                                            <p class="text-muted mb-3">Take a virtual walkthrough of this property</p>
                                            <button class="btn btn-primary" onclick="startVirtualTour()">
                                                <i class="icon ni ni-play me-2"></i>Start Tour
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Property Sidebar -->
            <div class="col-lg-4">
                <div class="property-sidebar">
                    <!-- Price Card -->
                    <div class="price-card bg-white rounded-4 shadow-sm p-4 mb-4">
                        <div class="text-center mb-4">
                            <h2 class="fw-bold text-primary mb-1">${{ number_format($property->rent, 2) }}</h2>
                            <p class="text-muted mb-0">per month</p>
                        </div>
                        
                        <div class="price-details mb-4">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Rent:</span>
                                <span class="fw-bold">${{ number_format($property->rent, 2) }}</span>
                            </div>
                            @if($property->deposit)
                            <div class="d-flex justify-content-between mb-2">
                                <span>Deposit:</span>
                                <span class="fw-bold">${{ number_format($property->deposit, 2) }}</span>
                            </div>
                            @endif
                            <hr>
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total:</span>
                                <span class="fw-bold text-primary">${{ number_format($property->rent + ($property->deposit ?? 0), 2) }}</span>
                            </div>
                        </div>

                        <div class="action-buttons">
                            <button class="btn btn-primary w-100 mb-3" onclick="contactLandlord()">
                                <i class="icon ni ni-call me-2"></i>Contact Landlord
                            </button>
                            <button class="btn btn-outline-primary w-100 mb-3" onclick="scheduleViewing()">
                                <i class="icon ni ni-calendar me-2"></i>Schedule Viewing
                            </button>
                            <button class="btn btn-outline-secondary w-100" onclick="toggleFavorite()">
                                <i class="icon ni ni-heart me-2"></i>Add to Favorites
                            </button>
                        </div>
                    </div>

                    <!-- Landlord Info -->
                    @if($property->landlord)
                    <div class="landlord-card bg-white rounded-4 shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-3">Property Owner</h5>
                        <div class="d-flex align-items-center mb-3">
                            <div class="landlord-avatar bg-primary rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                <i class="icon ni ni-user text-white"></i>
                            </div>
                            <div>
                                <h6 class="fw-bold mb-1">{{ $property->landlord->name }}</h6>
                                <p class="text-muted small mb-0">Property Owner</p>
                            </div>
                        </div>
                        <button class="btn btn-outline-primary w-100" onclick="contactLandlord()">
                            <i class="icon ni ni-message me-2"></i>Send Message
                        </button>
                    </div>
                    @endif

                    <!-- Related Properties -->
                    @if($relatedProperties->count() > 0)
                    <div class="related-properties bg-white rounded-4 shadow-sm p-4">
                        <h5 class="fw-bold mb-3">Similar Properties</h5>
                        @foreach($relatedProperties as $relatedProperty)
                        <div class="related-property d-flex mb-3">
                            <div class="related-image me-3">
                                @php
                                    $placeholderImages = [
                                        'apartment' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                                        'house' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                                        'studio' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80',
                                        'commercial' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80'
                                    ];
                                    $defaultImage = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80';
                                    $relatedImage = $placeholderImages[strtolower($relatedProperty->type ?? 'apartment')] ?? $defaultImage;
                                @endphp
                                <img src="{{ $relatedImage }}" alt="{{ $relatedProperty->name }}" class="rounded-3" style="width: 60px; height: 45px; object-fit: cover;">
                            </div>
                            <div class="related-info flex-grow-1">
                                <h6 class="fw-bold mb-1">{{ Str::limit($relatedProperty->name, 20) }}</h6>
                                <p class="text-muted small mb-1">${{ number_format($relatedProperty->rent, 2) }}/month</p>
                                <a href="{{ route('frontend.properties.show', $relatedProperty->id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
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

.thumbnail-img {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.thumbnail-img:hover {
    border-color: #667eea;
    transform: scale(1.05);
}

.detail-item {
    transition: all 0.3s ease;
}

.detail-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.amenity-item {
    transition: all 0.3s ease;
}

.amenity-item:hover {
    background-color: #e3f2fd !important;
}

.price-card {
    position: sticky;
    top: 20px;
}

.virtual-tour-container {
    transition: all 0.3s ease;
}

.virtual-tour-container:hover {
    box-shadow: 0 10px 25px rgba(0,0,0,0.1);
}
</style>

<script>
function changeMainImage(imageUrl) {
    document.getElementById('mainImage').src = imageUrl;
}

function contactLandlord() {
    alert('Contact landlord functionality would be implemented here');
}

function scheduleViewing() {
    alert('Schedule viewing functionality would be implemented here');
}

function toggleFavorite() {
    alert('Add to favorites functionality would be implemented here');
}

function startVirtualTour() {
    alert('3D Virtual Tour would be implemented here using a 3D viewer library');
}

// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});
</script>
@endsection
