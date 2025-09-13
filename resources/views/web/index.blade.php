@extends('web.web_layout')

@section('content')
<!-- Hero Section -->
<section class="hero-section position-relative overflow-hidden" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); min-height: 100vh; display: flex; align-items: center;">
    <!-- Animated Background Elements -->
    <div class="hero-bg-elements position-absolute w-100 h-100">
        <div class="floating-shapes">
            <div class="shape shape-1"></div>
            <div class="shape shape-2"></div>
            <div class="shape shape-3"></div>
            <div class="shape shape-4"></div>
        </div>
    </div>
    
    <div class="container position-relative z-3">
        <div class="row align-items-center min-vh-100">
            <div class="col-lg-6">
                <div class="hero-content text-white">
                    <div class="hero-badge mb-4 animate-fade-in">
                        <span class="badge bg-gradient-primary px-4 py-2 rounded-pill">
                            <i class="icon ni ni-star-fill me-2"></i>Kenya's Leading Property Platform
                        </span>
                    </div>
                    <h1 class="display-2 fw-bold mb-4 animate-fade-in-delay" style="background: linear-gradient(135deg, #ffffff 0%, #e2e8f0 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                        Find Your Dream <span class="text-gradient">Property</span>
                    </h1>
                    <p class="lead mb-5 animate-fade-in-delay-2 text-light opacity-90">
                        Discover premium properties for rent, lease, or purchase. From modern apartments to luxury villas, 
                        we connect you with the perfect home in Kenya's most desirable locations.
                    </p>
                    <div class="hero-buttons animate-fade-in-delay-3">
                        <a href="#properties" class="btn btn-gradient-primary btn-lg me-3 px-5 py-3 shadow-lg rounded-pill">
                            <i class="icon ni ni-search me-2"></i>Explore Properties
                        </a>
                        <a href="#about" class="btn btn-outline-light btn-lg px-5 py-3 rounded-pill">
                            <i class="icon ni ni-info me-2"></i>Learn More
                        </a>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="hero-stats mt-5 animate-fade-in-delay-4">
                        <div class="row g-4">
                            <div class="col-4">
                                <div class="stat-item text-center">
                                    <h3 class="fw-bold text-white mb-1">2,500+</h3>
                                    <p class="mb-0 text-light opacity-75 small">Properties Listed</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item text-center">
                                    <h3 class="fw-bold text-white mb-1">5,000+</h3>
                                    <p class="mb-0 text-light opacity-75 small">Happy Clients</p>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-item text-center">
                                    <h3 class="fw-bold text-white mb-1">98%</h3>
                                    <p class="mb-0 text-light opacity-75 small">Satisfaction Rate</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="hero-image text-center animate-fade-in-delay">
                    <!-- Property Showcase Carousel -->
                    <div class="property-showcase position-relative">
                        <div class="main-property-card bg-white rounded-4 shadow-2xl p-4 position-relative" style="transform: perspective(1000px) rotateY(-8deg) rotateX(5deg);">
                            <div class="property-image-container position-relative rounded-3 overflow-hidden mb-4" style="height: 280px;">
                                <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=1000&q=80" 
                                     alt="Luxury Villa" class="w-100 h-100" style="object-fit: cover;">
                                <div class="property-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center">
                                    <div class="play-button bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="icon ni ni-play-fill text-primary" style="font-size: 1.5rem;"></i>
                                    </div>
                                </div>
                                <div class="property-badges position-absolute top-0 start-0 p-3">
                                    <span class="badge bg-success px-3 py-2 rounded-pill">Available</span>
                                </div>
                                <div class="property-badges position-absolute top-0 end-0 p-3">
                                    <span class="badge bg-primary px-3 py-2 rounded-pill">For Rent</span>
                                </div>
                            </div>
                            <div class="property-info">
                                <h5 class="fw-bold mb-2 text-dark">Modern Luxury Villa</h5>
                                <p class="text-muted small mb-2">
                                    <i class="icon ni ni-map-pin me-1"></i>Karen, Nairobi
                                </p>
                                <div class="property-features d-flex justify-content-between mb-3">
                                    <span class="small text-muted">
                                        <i class="icon ni ni-bed me-1"></i>4 Bedrooms
                                    </span>
                                    <span class="small text-muted">
                                        <i class="icon ni ni-bath me-1"></i>3 Bathrooms
                                    </span>
                                    <span class="small text-muted">
                                        <i class="icon ni ni-square me-1"></i>2,500 sqft
                                    </span>
                                </div>
                                <div class="property-price d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold text-primary fs-4">KSh 150,000</span>
                                        <span class="text-muted small">/month</span>
                                    </div>
                                    <button class="btn btn-primary btn-sm rounded-pill px-4">
                                        View Details
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Floating Property Cards -->
                        <div class="floating-card floating-card-1 position-absolute bg-white rounded-3 shadow-lg p-3" style="top: 10%; right: -10%; transform: rotate(15deg);">
                            <img src="https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                 alt="Apartment" class="rounded-2 mb-2" style="width: 80px; height: 60px; object-fit: cover;">
                            <div class="small">
                                <div class="fw-bold text-dark">Studio Apartment</div>
                                <div class="text-muted">KSh 45,000/mo</div>
                            </div>
                        </div>
                        
                        <div class="floating-card floating-card-2 position-absolute bg-white rounded-3 shadow-lg p-3" style="bottom: 20%; left: -15%; transform: rotate(-10deg);">
                            <img src="https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" 
                                 alt="Commercial" class="rounded-2 mb-2" style="width: 80px; height: 60px; object-fit: cover;">
                            <div class="small">
                                <div class="fw-bold text-dark">Office Space</div>
                                <div class="text-muted">KSh 80,000/mo</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll Indicator -->
    <div class="scroll-indicator position-absolute bottom-0 start-50 translate-middle-x mb-4">
        <div class="scroll-arrow text-white">
            <i class="icon ni ni-chevron-down" style="font-size: 1.5rem;"></i>
        </div>
    </div>
</section>

<!-- Advanced Search Section -->
<section class="py-5 bg-gradient-light position-relative" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-12">
                <div class="search-card bg-white rounded-4 shadow-2xl p-5 position-relative overflow-hidden">
                    <!-- Search Header -->
                    <div class="text-center mb-4">
                        <h3 class="fw-bold text-dark mb-2">Find Your Perfect Property</h3>
                        <p class="text-muted">Search through thousands of properties with our advanced filters</p>
                    </div>
                    
                    <form action="{{ route('frontend.properties.search') }}" method="GET" class="row g-4">
                        <!-- Location Search -->
                        <div class="col-lg-4 col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold text-dark mb-2">
                                    <i class="icon ni ni-map-pin me-2 text-primary"></i>Location
                                </label>
                                <div class="position-relative">
                                    <input type="text" class="form-control form-control-lg border-2 rounded-pill" 
                                           name="location" placeholder="Enter city, area, or neighborhood" 
                                           value="{{ request('location') }}" style="padding-left: 3rem;">
                                    <i class="icon ni ni-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Property Type -->
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold text-dark mb-2">
                                    <i class="icon ni ni-home me-2 text-primary"></i>Property Type
                                </label>
                                <select class="form-select form-select-lg border-2 rounded-pill" name="type">
                                    <option value="">All Property Types</option>
                                    <option value="apartment" {{ request('type') == 'apartment' ? 'selected' : '' }}>Apartment</option>
                                    <option value="house" {{ request('type') == 'house' ? 'selected' : '' }}>House</option>
                                    <option value="studio" {{ request('type') == 'studio' ? 'selected' : '' }}>Studio</option>
                                    <option value="commercial" {{ request('type') == 'commercial' ? 'selected' : '' }}>Commercial</option>
                                    <option value="villa" {{ request('type') == 'villa' ? 'selected' : '' }}>Villa</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Purpose -->
                        <div class="col-lg-3 col-md-6">
                            <div class="form-group">
                                <label class="form-label fw-bold text-dark mb-2">
                                    <i class="icon ni ni-key me-2 text-primary"></i>Purpose
                                </label>
                                <select class="form-select form-select-lg border-2 rounded-pill" name="purpose">
                                    <option value="">All Purposes</option>
                                    <option value="rent" {{ request('purpose') == 'rent' ? 'selected' : '' }}>For Rent</option>
                                    <option value="sale" {{ request('purpose') == 'sale' ? 'selected' : '' }}>For Sale</option>
                                    <option value="lease" {{ request('purpose') == 'lease' ? 'selected' : '' }}>For Lease</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Search Button -->
                        <div class="col-lg-2 col-md-6">
                            <div class="form-group">
                                <label class="form-label text-white mb-2">&nbsp;</label>
                                <button type="submit" class="btn btn-gradient-primary btn-lg w-100 rounded-pill shadow-lg">
                                    <i class="icon ni ni-search me-2"></i>Search
                                </button>
                            </div>
                        </div>
                        
                        <!-- Advanced Filters (Collapsible) -->
                        <div class="col-12">
                            <div class="text-center">
                                <button type="button" class="btn btn-link text-primary fw-bold" data-bs-toggle="collapse" data-bs-target="#advancedFilters">
                                    <i class="icon ni ni-setting me-2"></i>Advanced Filters
                                    <i class="icon ni ni-chevron-down ms-2"></i>
                                </button>
                            </div>
                            
                            <div class="collapse mt-4" id="advancedFilters">
                                <div class="row g-4">
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label fw-bold text-dark mb-2">Min Price (KSh)</label>
                                        <input type="number" class="form-control border-2 rounded-pill" 
                                               name="min_rent" placeholder="0" value="{{ request('min_rent') }}">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label fw-bold text-dark mb-2">Max Price (KSh)</label>
                                        <input type="number" class="form-control border-2 rounded-pill" 
                                               name="max_rent" placeholder="No limit" value="{{ request('max_rent') }}">
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label fw-bold text-dark mb-2">Bedrooms</label>
                                        <select class="form-select border-2 rounded-pill" name="bedrooms">
                                            <option value="">Any</option>
                                            <option value="1">1+</option>
                                            <option value="2">2+</option>
                                            <option value="3">3+</option>
                                            <option value="4">4+</option>
                                        </select>
                                    </div>
                                    <div class="col-lg-3 col-md-6">
                                        <label class="form-label fw-bold text-dark mb-2">Bathrooms</label>
                                        <select class="form-select border-2 rounded-pill" name="bathrooms">
                                            <option value="">Any</option>
                                            <option value="1">1+</option>
                                            <option value="2">2+</option>
                                            <option value="3">3+</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Featured Properties Section -->
<section id="properties" class="py-5 position-relative" style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <div class="section-header">
                    <span class="badge bg-gradient-primary px-4 py-2 rounded-pill mb-3">
                        <i class="icon ni ni-star-fill me-2"></i>Featured Properties
                    </span>
                    <h2 class="display-4 fw-bold mb-3 text-dark">Premium Property Collection</h2>
                    <p class="lead text-muted">Discover our handpicked selection of premium properties available for rent, purchase, or lease across Kenya's most desirable locations.</p>
                </div>
            </div>
        </div>
        
        <!-- Property Categories Tabs -->
        <div class="row mb-5">
            <div class="col-12">
                <div class="property-tabs-container bg-white rounded-4 shadow-sm p-2 d-inline-block">
                    <ul class="nav nav-pills justify-content-center mb-0" id="propertyTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-4 py-3" id="all-tab" data-bs-toggle="pill" data-bs-target="#all" type="button" role="tab">
                                <i class="icon ni ni-home me-2"></i>All Properties
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-3" id="rent-tab" data-bs-toggle="pill" data-bs-target="#rent" type="button" role="tab">
                                <i class="icon ni ni-key me-2"></i>For Rent
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-3" id="sale-tab" data-bs-toggle="pill" data-bs-target="#sale" type="button" role="tab">
                                <i class="icon ni ni-money me-2"></i>For Sale
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-4 py-3" id="lease-tab" data-bs-toggle="pill" data-bs-target="#lease" type="button" role="tab">
                                <i class="icon ni ni-file-text me-2"></i>For Lease
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Property Cards -->
        <div class="tab-content" id="propertyTabsContent">
            <div class="tab-pane fade show active" id="all" role="tabpanel">
                <div class="row g-4">
                    @if($featuredProperties->count() > 0)
                        @foreach($featuredProperties as $property)
                        <div class="col-lg-4 col-md-6">
                            <div class="property-card bg-white rounded-4 shadow-lg h-100 hover-lift position-relative overflow-hidden">
                                <!-- Property Image -->
                                <div class="property-image position-relative" style="height: 250px; overflow: hidden;">
                                    @php
                                        // High-quality property images based on type
                                        $propertyImages = [
                                            'apartment' => 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'house' => 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'studio' => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'commercial' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80',
                                            'villa' => 'https://images.unsplash.com/photo-1613490493576-7fde63acd811?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'
                                        ];
                                        $defaultImage = 'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
                                        $propertyImage = $propertyImages[strtolower($property->type ?? 'apartment')] ?? $defaultImage;
                                    @endphp
                                    <img src="{{ $propertyImage }}" alt="{{ $property->name }}" class="w-100 h-100" style="object-fit: cover; transition: transform 0.3s ease;">
                                    
                                    <!-- Image Overlay -->
                                    <div class="property-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.3); opacity: 0; transition: opacity 0.3s ease;">
                                        <button class="btn btn-light rounded-circle" style="width: 50px; height: 50px;">
                                            <i class="icon ni ni-eye"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Property Badges -->
                                    <div class="property-badges position-absolute top-0 start-0 p-3">
                                        <span class="badge bg-{{ $property->is_vacant ? 'success' : 'warning' }} px-3 py-2 rounded-pill shadow-sm">
                                            {{ $property->is_vacant ? 'Available' : 'Occupied' }}
                                        </span>
                                    </div>
                                    <div class="property-badges position-absolute top-0 end-0 p-3">
                                        <span class="badge bg-primary px-3 py-2 rounded-pill shadow-sm">
                                            {{ ucfirst($property->type ?? 'Property') }}
                                        </span>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="property-actions position-absolute top-0 end-0 p-3" style="top: 60px !important;">
                                        <button class="btn btn-light btn-sm rounded-circle shadow-sm" onclick="toggleFavorite(this)">
                                            <i class="icon ni ni-heart"></i>
                                        </button>
                                    </div>
                                    
                                    <!-- Price Tag -->
                                    <div class="property-price-tag position-absolute bottom-0 start-0 p-3">
                                        <div class="bg-white rounded-3 px-3 py-2 shadow-sm">
                                            <span class="fw-bold text-primary fs-5">KSh {{ number_format($property->rent, 0) }}</span>
                                            <span class="text-muted small d-block">per month</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Property Content -->
                                <div class="property-content p-4">
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <h5 class="fw-bold mb-0 text-dark">{{ $property->name }}</h5>
                                        <div class="property-rating">
                                            <div class="d-flex align-items-center">
                                                <i class="icon ni ni-star-fill text-warning me-1"></i>
                                                <span class="small text-muted">4.8</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <p class="text-muted small mb-3">
                                        <i class="icon ni ni-map-pin me-1 text-primary"></i>
                                        @if($property->address)
                                            {{ $property->address->city }}, {{ $property->address->state }}
                                        @else
                                            Nairobi, Kenya
                                        @endif
                                    </p>
                                    
                                    @if($property->description)
                                    <p class="text-muted small mb-3">{{ Str::limit($property->description, 100) }}</p>
                                    @endif
                                    
                                    <!-- Property Features -->
                                    <div class="property-features d-flex justify-content-between mb-3">
                                        <span class="small text-muted d-flex align-items-center">
                                            <i class="icon ni ni-bed me-1 text-primary"></i>
                                            {{ rand(1, 4) }} Beds
                                        </span>
                                        <span class="small text-muted d-flex align-items-center">
                                            <i class="icon ni ni-bath me-1 text-primary"></i>
                                            {{ rand(1, 3) }} Baths
                                        </span>
                                        <span class="small text-muted d-flex align-items-center">
                                            <i class="icon ni ni-square me-1 text-primary"></i>
                                            {{ rand(800, 2500) }} sqft
                                        </span>
                                    </div>
                                    
                                    <!-- Amenities -->
                                    <div class="property-amenities mb-4">
                                        @php
                                            $amenities = ['Parking', 'WiFi', 'Security', 'Gym', 'Pool'];
                                            $selectedAmenities = array_slice($amenities, 0, rand(2, 4));
                                        @endphp
                                        @foreach($selectedAmenities as $amenity)
                                        <span class="badge bg-light text-dark me-1 mb-1 px-2 py-1 rounded-pill small">{{ $amenity }}</span>
                                        @endforeach
                                    </div>
                                    
                                    <!-- Property Footer -->
                                    <div class="property-footer d-flex justify-content-between align-items-center">
                                        <div class="property-meta">
                                            <span class="small text-muted">
                                                <i class="icon ni ni-calendar me-1"></i>{{ $property->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                        <a href="{{ route('frontend.properties.show', $property->id) }}" class="btn btn-gradient-primary btn-sm rounded-pill px-4">
                                            <i class="icon ni ni-arrow-right me-1"></i>View Details
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    @else
                        <div class="col-12">
                            <div class="text-center py-5">
                                <div class="empty-state">
                                    <div class="empty-icon mb-4">
                                        <i class="icon ni ni-home text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                                    </div>
                                    <h3 class="fw-bold text-dark mb-2">No Properties Available</h3>
                                    <p class="text-muted mb-4">We're working on adding new properties. Check back soon!</p>
                                    <a href="#search" class="btn btn-gradient-primary rounded-pill px-4">
                                        <i class="icon ni ni-search me-2"></i>Search Properties
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                
                <div class="text-center mt-5">
                    <a href="{{ route('frontend.properties.index') }}" class="btn btn-outline-primary btn-lg">
                        View All Properties <i class="icon ni ni-arrow-right ms-2"></i>
                    </a>
                </div>
            </div>
            
            <!-- Other tabs content would go here -->
            <div class="tab-pane fade" id="rent" role="tabpanel">
                <div class="text-center py-5">
                    <h4>Properties for Rent</h4>
                    <p class="text-muted">Coming soon...</p>
                </div>
            </div>
            <div class="tab-pane fade" id="sale" role="tabpanel">
                <div class="text-center py-5">
                    <h4>Properties for Sale</h4>
                    <p class="text-muted">Coming soon...</p>
                </div>
            </div>
            <div class="tab-pane fade" id="lease" role="tabpanel">
                <div class="text-center py-5">
                    <h4>Properties for Lease</h4>
                    <p class="text-muted">Coming soon...</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="py-5 position-relative" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <div class="section-header">
                    <span class="badge bg-gradient-primary px-4 py-2 rounded-pill mb-3">
                        <i class="icon ni ni-star-fill me-2"></i>Our Services
                    </span>
                    <h2 class="display-4 fw-bold mb-3 text-dark">Complete Property Management Solutions</h2>
                    <p class="lead text-muted">From finding your dream home to managing your investment properties, we provide comprehensive real estate services across Kenya.</p>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <!-- For Renters -->
            <div class="col-lg-4 col-md-6">
                <div class="service-card bg-white rounded-4 shadow-lg p-5 h-100 hover-lift text-center">
                    <div class="service-icon mb-4">
                        <div class="icon-wrapper bg-gradient-primary rounded-4 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="icon ni ni-key text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Property Rentals</h4>
                    <p class="text-muted mb-4">Find your perfect home with our extensive database of rental properties. From studio apartments to luxury villas.</p>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Advanced search filters</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Virtual property tours</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Online application process</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>24/7 tenant support</li>
                    </ul>
                    <a href="#properties" class="btn btn-gradient-primary rounded-pill px-4 mt-3">
                        <i class="icon ni ni-search me-2"></i>Browse Rentals
                    </a>
                </div>
            </div>
            
            <!-- For Buyers -->
            <div class="col-lg-4 col-md-6">
                <div class="service-card bg-white rounded-4 shadow-lg p-5 h-100 hover-lift text-center">
                    <div class="service-icon mb-4">
                        <div class="icon-wrapper bg-gradient-primary rounded-4 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="icon ni ni-home text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Property Sales</h4>
                    <p class="text-muted mb-4">Discover investment opportunities and dream homes with our curated selection of properties for sale.</p>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Market analysis & pricing</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Property inspections</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Legal documentation</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Financing assistance</li>
                    </ul>
                    <a href="#properties" class="btn btn-gradient-primary rounded-pill px-4 mt-3">
                        <i class="icon ni ni-home me-2"></i>View Properties
                    </a>
                </div>
            </div>
            
            <!-- For Landlords -->
            <div class="col-lg-4 col-md-6">
                <div class="service-card bg-white rounded-4 shadow-lg p-5 h-100 hover-lift text-center">
                    <div class="service-icon mb-4">
                        <div class="icon-wrapper bg-gradient-primary rounded-4 d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                            <i class="icon ni ni-building text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h4 class="fw-bold mb-3 text-dark">Property Management</h4>
                    <p class="text-muted mb-4">Maximize your rental income with our comprehensive property management services for landlords.</p>
                    <ul class="list-unstyled text-start">
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Tenant screening & placement</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Rent collection & accounting</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Maintenance coordination</li>
                        <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Financial reporting</li>
                    </ul>
                    <a href="/register" class="btn btn-gradient-primary rounded-pill px-4 mt-3">
                        <i class="icon ni ni-user me-2"></i>Get Started
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 position-relative" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <div class="section-header">
                    <span class="badge bg-gradient-primary px-4 py-2 rounded-pill mb-3">
                        <i class="icon ni ni-star-fill me-2"></i>Testimonials
                    </span>
                    <h2 class="display-4 fw-bold mb-3 text-white">What Our Clients Say</h2>
                    <p class="lead text-light opacity-90">Don't just take our word for it. Here's what our satisfied clients have to say about their experience with Foxes Rental Systems.</p>
                </div>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card bg-white rounded-4 shadow-lg p-4 h-100">
                    <div class="testimonial-header d-flex align-items-center mb-3">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" 
                             alt="Client" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">John Kamau</h6>
                            <p class="small text-muted mb-0">Property Owner</p>
                        </div>
                    </div>
                    <div class="testimonial-rating mb-3">
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                    </div>
                    <p class="text-muted mb-0">"Foxes Rental Systems has transformed how I manage my properties. The platform is intuitive, and the support team is always available. My rental income has increased by 30% since I started using their services."</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card bg-white rounded-4 shadow-lg p-4 h-100">
                    <div class="testimonial-header d-flex align-items-center mb-3">
                        <img src="https://images.unsplash.com/photo-1494790108755-2616b612b786?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" 
                             alt="Client" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Sarah Wanjiku</h6>
                            <p class="small text-muted mb-0">Tenant</p>
                        </div>
                    </div>
                    <div class="testimonial-rating mb-3">
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                    </div>
                    <p class="text-muted mb-0">"Finding my dream apartment was so easy with Foxes Rental Systems. The search filters are amazing, and I found exactly what I was looking for within a week. The payment process is also very convenient."</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="testimonial-card bg-white rounded-4 shadow-lg p-4 h-100">
                    <div class="testimonial-header d-flex align-items-center mb-3">
                        <img src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80" 
                             alt="Client" class="rounded-circle me-3" style="width: 50px; height: 50px; object-fit: cover;">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">Michael Ochieng</h6>
                            <p class="small text-muted mb-0">Real Estate Investor</p>
                        </div>
                    </div>
                    <div class="testimonial-rating mb-3">
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                        <i class="icon ni ni-star-fill text-warning"></i>
                    </div>
                    <p class="text-muted mb-0">"As a real estate investor, I need reliable property management. Foxes Rental Systems provides excellent analytics, automated rent collection, and comprehensive reporting. Highly recommended!"</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- About Us Section -->
<section id="about" class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">About Foxes Rental Systems</h2>
                <p class="lead text-muted">Your trusted partner in property management and real estate solutions.</p>
            </div>
        </div>
        
        <!-- About Tabs -->
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-pills justify-content-center mb-5" id="aboutTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="about-us-tab" data-bs-toggle="pill" data-bs-target="#about-us" type="button" role="tab">
                            <i class="icon ni ni-info me-2"></i>About Us
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tenants-tab" data-bs-toggle="pill" data-bs-target="#tenants" type="button" role="tab">
                            <i class="icon ni ni-user me-2"></i>For Tenants
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="landlords-tab" data-bs-toggle="pill" data-bs-target="#landlords" type="button" role="tab">
                            <i class="icon ni ni-building me-2"></i>For Landlords
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="aboutTabsContent">
            <!-- About Us Tab -->
            <div class="tab-pane fade show active" id="about-us" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="about-content">
                            <h3 class="fw-bold mb-4">Our Story</h3>
                            <p class="text-muted mb-4">Foxes Rental Systems was founded with a vision to revolutionize the property management industry in Kenya. We understand the challenges faced by both property owners and tenants, and we've created a comprehensive platform that addresses these needs.</p>
                            <p class="text-muted mb-4">Our mission is to make property management seamless, transparent, and efficient for everyone involved in the rental ecosystem.</p>
                            
                            <div class="row g-3">
                                <div class="col-6">
                                    <div class="stat-box text-center p-3 bg-white rounded-3 shadow-sm">
                                        <h4 class="fw-bold text-primary mb-1">500+</h4>
                                        <p class="small text-muted mb-0">Properties Managed</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box text-center p-3 bg-white rounded-3 shadow-sm">
                                        <h4 class="fw-bold text-success mb-1">1000+</h4>
                                        <p class="small text-muted mb-0">Happy Clients</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box text-center p-3 bg-white rounded-3 shadow-sm">
                                        <h4 class="fw-bold text-warning mb-1">5+</h4>
                                        <p class="small text-muted mb-0">Years Experience</p>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="stat-box text-center p-3 bg-white rounded-3 shadow-sm">
                                        <h4 class="fw-bold text-info mb-1">24/7</h4>
                                        <p class="small text-muted mb-0">Customer Support</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="about-image">
                            <img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=1000&q=80" alt="About Us" class="w-100 rounded-4 shadow-lg" style="height: 400px; object-fit: cover;">
                        </div>
                    </div>
                </div>
            </div>

            <!-- For Tenants Tab -->
            <div class="tab-pane fade" id="tenants" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="feature-card h-100 p-4 rounded-4 shadow-sm border-0 bg-white hover-lift">
                            <div class="feature-icon bg-primary bg-opacity-10 rounded-3 p-3 mb-4 d-inline-block">
                                <em class="icon ni ni-search text-primary" style="font-size: 2rem;"></em>
                            </div>
                            <h4 class="fw-bold mb-3">Easy Property Search</h4>
                            <p class="text-muted mb-4">Find your perfect home with our advanced search filters and detailed property listings.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Advanced search filters</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>High-quality photos</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Virtual tours</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card h-100 p-4 rounded-4 shadow-sm border-0 bg-white hover-lift">
                            <div class="feature-icon bg-success bg-opacity-10 rounded-3 p-3 mb-4 d-inline-block">
                                <em class="icon ni ni-credit-card text-success" style="font-size: 2rem;"></em>
                            </div>
                            <h4 class="fw-bold mb-3">Easy Payments</h4>
                            <p class="text-muted mb-4">Pay your rent conveniently through MPesa and other secure payment methods.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>MPesa integration</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Automated receipts</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Payment history</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card h-100 p-4 rounded-4 shadow-sm border-0 bg-white hover-lift">
                            <div class="feature-icon bg-warning bg-opacity-10 rounded-3 p-3 mb-4 d-inline-block">
                                <em class="icon ni ni-call text-warning" style="font-size: 2rem;"></em>
                            </div>
                            <h4 class="fw-bold mb-3">24/7 Support</h4>
                            <p class="text-muted mb-4">Get help whenever you need it with our round-the-clock customer support.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Live chat support</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Phone support</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Email support</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- For Landlords Tab -->
            <div class="tab-pane fade" id="landlords" role="tabpanel">
                <div class="row g-4">
                    <div class="col-lg-4">
                        <div class="feature-card h-100 p-4 rounded-4 shadow-sm border-0 bg-white hover-lift">
                            <div class="feature-icon bg-primary bg-opacity-10 rounded-3 p-3 mb-4 d-inline-block">
                                <em class="icon ni ni-home text-primary" style="font-size: 2rem;"></em>
                            </div>
                            <h4 class="fw-bold mb-3">Property Management</h4>
                            <p class="text-muted mb-4">Manage all your properties from one centralized dashboard with comprehensive tools.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Property listings</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Tenant management</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Financial tracking</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card h-100 p-4 rounded-4 shadow-sm border-0 bg-white hover-lift">
                            <div class="feature-icon bg-success bg-opacity-10 rounded-3 p-3 mb-4 d-inline-block">
                                <em class="icon ni ni-chart text-success" style="font-size: 2rem;"></em>
                            </div>
                            <h4 class="fw-bold mb-3">Analytics & Reports</h4>
                            <p class="text-muted mb-4">Get detailed insights into your property performance and financial metrics.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Revenue reports</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Occupancy rates</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Maintenance costs</li>
                            </ul>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="feature-card h-100 p-4 rounded-4 shadow-sm border-0 bg-white hover-lift">
                            <div class="feature-icon bg-warning bg-opacity-10 rounded-3 p-3 mb-4 d-inline-block">
                                <em class="icon ni ni-users text-warning" style="font-size: 2rem;"></em>
                            </div>
                            <h4 class="fw-bold mb-3">Tenant Screening</h4>
                            <p class="text-muted mb-4">Find reliable tenants with our comprehensive screening and verification process.</p>
                            <ul class="list-unstyled">
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Background checks</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Credit verification</li>
                                <li class="mb-2"><i class="icon ni ni-check-circle-fill text-success me-2"></i>Reference checks</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5 position-relative overflow-hidden" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%);">
    <!-- Background Elements -->
    <div class="cta-bg-elements position-absolute w-100 h-100">
        <div class="cta-shapes">
            <div class="cta-shape cta-shape-1"></div>
            <div class="cta-shape cta-shape-2"></div>
            <div class="cta-shape cta-shape-3"></div>
        </div>
    </div>
    
    <div class="container position-relative z-3">
        <div class="row">
            <div class="col-lg-10 mx-auto text-center">
                <div class="cta-content text-white">
                    <h2 class="display-3 fw-bold mb-4">Ready to Transform Your Property Experience?</h2>
                    <p class="lead mb-5 opacity-90">Join over 5,000 satisfied clients who trust Foxes Rental Systems for their property management and real estate needs across Kenya.</p>
                    
                    <div class="cta-buttons mb-5">
                        <a href="{{ route('frontend.properties.index') }}" class="btn btn-light btn-lg me-3 px-5 py-4 shadow-lg rounded-pill">
                            <i class="icon ni ni-search me-2"></i>Browse Properties
                        </a>
                        <a href="/register" class="btn btn-outline-light btn-lg px-5 py-4 rounded-pill">
                            <i class="icon ni ni-user me-2"></i>Get Started Free
                        </a>
                    </div>
                    
                    <!-- Trust Indicators -->
                    <div class="row g-4 mb-5">
                        <div class="col-md-4">
                            <div class="trust-item text-center">
                                <div class="trust-icon mb-3">
                                    <i class="icon ni ni-shield-check" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                </div>
                                <h5 class="fw-bold mb-2">100% Secure</h5>
                                <p class="small opacity-75 mb-0">Bank-level security for all transactions</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="trust-item text-center">
                                <div class="trust-icon mb-3">
                                    <i class="icon ni ni-call" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                </div>
                                <h5 class="fw-bold mb-2">24/7 Support</h5>
                                <p class="small opacity-75 mb-0">Round-the-clock customer assistance</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="trust-item text-center">
                                <div class="trust-icon mb-3">
                                    <i class="icon ni ni-star-fill" style="font-size: 2.5rem; opacity: 0.8;"></i>
                                </div>
                                <h5 class="fw-bold mb-2">98% Satisfaction</h5>
                                <p class="small opacity-75 mb-0">Rated by thousands of happy clients</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cta-footer">
                        <p class="small opacity-75 mb-0">
                            <i class="icon ni ni-check-circle me-2"></i>Free to browse • No registration required • Expert support available
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
/* Modern Color Palette */
:root {
    --primary-gradient: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%);
    --secondary-gradient: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);
    --accent-gradient: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);
    --dark-gradient: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);
    --light-gradient: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
    --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
    --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
}

/* Animations */
.animate-fade-in {
    animation: fadeInUp 0.8s ease-out;
}

.animate-fade-in-delay {
    animation: fadeInUp 0.8s ease-out 0.2s both;
}

.animate-fade-in-delay-2 {
    animation: fadeInUp 0.8s ease-out 0.4s both;
}

.animate-fade-in-delay-3 {
    animation: fadeInUp 0.8s ease-out 0.6s both;
}

.animate-fade-in-delay-4 {
    animation: fadeInUp 0.8s ease-out 0.8s both;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

/* Floating Shapes Animation */
.floating-shapes .shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 6s ease-in-out infinite;
}

.shape-1 {
    width: 80px;
    height: 80px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.shape-2 {
    width: 120px;
    height: 120px;
    top: 60%;
    right: 15%;
    animation-delay: 2s;
}

.shape-3 {
    width: 60px;
    height: 60px;
    bottom: 20%;
    left: 20%;
    animation-delay: 4s;
}

.shape-4 {
    width: 100px;
    height: 100px;
    top: 40%;
    right: 30%;
    animation-delay: 1s;
}

/* Gradient Buttons */
.btn-gradient-primary {
    background: var(--primary-gradient);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.btn-gradient-primary:hover {
    background: linear-gradient(135deg, #0284c7 0%, #0369a1 50%, #075985 100%);
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(14, 165, 233, 0.3);
    color: white;
}

.bg-gradient-primary {
    background: var(--primary-gradient);
}

.bg-gradient-light {
    background: var(--light-gradient);
}

/* Property Cards */
.property-card {
    transition: all 0.4s ease;
    border: 1px solid rgba(226, 232, 240, 0.5);
    overflow: hidden;
}

.property-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
    border-color: #0ea5e9;
}

.property-card:hover .property-image img {
    transform: scale(1.1);
}

.property-card:hover .property-overlay {
    opacity: 1 !important;
}

.property-card:hover .floating-card {
    animation: pulse 2s ease-in-out infinite;
}

/* Floating Cards */
.floating-card {
    transition: all 0.3s ease;
    z-index: 10;
}

.floating-card:hover {
    transform: rotate(0deg) scale(1.05) !important;
    box-shadow: 0 15px 35px rgba(0,0,0,0.2) !important;
}

/* Search Card */
.search-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

.search-card:hover {
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    transform: translateY(-5px);
}

/* Form Controls */
.form-control:focus, .form-select:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
}

/* Navigation Pills */
.nav-pills .nav-link {
    border-radius: 50px;
    padding: 12px 24px;
    font-weight: 500;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.nav-pills .nav-link.active {
    background: var(--primary-gradient);
    border: 2px solid #0ea5e9;
    color: white;
    box-shadow: 0 5px 15px rgba(14, 165, 233, 0.3);
}

.nav-pills .nav-link:hover:not(.active) {
    background-color: rgba(14, 165, 233, 0.1);
    color: #0ea5e9;
    border-color: rgba(14, 165, 233, 0.3);
}

/* Property Tabs Container */
.property-tabs-container {
    border: 1px solid rgba(226, 232, 240, 0.5);
    backdrop-filter: blur(10px);
}

/* Hover Effects */
.hover-lift {
    transition: all 0.3s ease;
}

.hover-lift:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.15) !important;
}

/* Feature Cards */
.feature-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

.feature-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    border-color: #0ea5e9;
}

/* Stats */
.stat-item h3 {
    font-size: 2.5rem;
    margin-bottom: 0.5rem;
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stat-box {
    transition: all 0.3s ease;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

.stat-box:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    border-color: #0ea5e9;
}

/* Text Gradient */
.text-gradient {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

/* Scroll Indicator */
.scroll-indicator {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-10px);
    }
    60% {
        transform: translateY(-5px);
    }
}

/* Property Showcase */
.property-showcase {
    perspective: 1000px;
}

.main-property-card {
    transition: all 0.4s ease;
}

.main-property-card:hover {
    transform: perspective(1000px) rotateY(-5deg) rotateX(3deg) !important;
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-property-card {
        transform: none !important;
    }
    
    .floating-card {
        display: none;
    }
    
    .hero-section {
        min-height: 80vh;
    }
    
    .display-2 {
        font-size: 2.5rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .nav-pills .nav-link {
        padding: 8px 16px;
        font-size: 0.9rem;
    }
    
    .property-card:hover {
        transform: translateY(-5px);
    }
}

@media (max-width: 576px) {
    .hero-section {
        min-height: 70vh;
    }
    
    .display-2 {
        font-size: 2rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}

/* Loading States */
.loading {
    position: relative;
    overflow: hidden;
}

.loading::after {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: loading 1.5s infinite;
}

@keyframes loading {
    0% { left: -100%; }
    100% { left: 100%; }
}

/* Custom Shadows */
.shadow-2xl {
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
}

/* Badge Styles */
.badge {
    font-weight: 500;
    letter-spacing: 0.025em;
}

/* Property Image Overlay */
.property-overlay {
    background: linear-gradient(45deg, rgba(14, 165, 233, 0.8), rgba(6, 182, 212, 0.8));
}

/* Section Headers */
.section-header .badge {
    background: var(--primary-gradient);
    color: white;
    font-weight: 600;
    letter-spacing: 0.05em;
}

/* Empty State */
.empty-state {
    padding: 3rem 1rem;
}

.empty-icon {
    opacity: 0.3;
}

/* Property Features Icons */
.property-features i {
    font-size: 1.1rem;
}

/* Amenities */
.property-amenities .badge {
    font-size: 0.75rem;
    font-weight: 500;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

/* Property Rating */
.property-rating i {
    font-size: 0.9rem;
}

/* Price Tag */
.property-price-tag {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.95) !important;
}

/* Action Buttons */
.property-actions .btn {
    backdrop-filter: blur(10px);
    background: rgba(255, 255, 255, 0.9) !important;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

/* Property Badges */
.property-badges .badge {
    backdrop-filter: blur(10px);
    font-weight: 600;
    letter-spacing: 0.025em;
}

/* CTA Section */
.cta-shapes .cta-shape {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 8s ease-in-out infinite;
}

.cta-shape-1 {
    width: 100px;
    height: 100px;
    top: 20%;
    left: 10%;
    animation-delay: 0s;
}

.cta-shape-2 {
    width: 150px;
    height: 150px;
    top: 60%;
    right: 15%;
    animation-delay: 3s;
}

.cta-shape-3 {
    width: 80px;
    height: 80px;
    bottom: 20%;
    left: 20%;
    animation-delay: 6s;
}

/* Service Cards */
.service-card {
    transition: all 0.4s ease;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

.service-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.15) !important;
    border-color: #0ea5e9;
}

.service-card .icon-wrapper {
    transition: all 0.3s ease;
}

.service-card:hover .icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

/* Testimonial Cards */
.testimonial-card {
    transition: all 0.3s ease;
    border: 1px solid rgba(226, 232, 240, 0.5);
}

.testimonial-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px rgba(0,0,0,0.1) !important;
    border-color: #0ea5e9;
}

.testimonial-rating i {
    font-size: 0.9rem;
}

/* Trust Items */
.trust-item {
    transition: all 0.3s ease;
}

.trust-item:hover {
    transform: translateY(-5px);
}

.trust-icon {
    transition: all 0.3s ease;
}

.trust-item:hover .trust-icon {
    transform: scale(1.1);
}

/* CTA Buttons */
.cta-buttons .btn {
    transition: all 0.3s ease;
    font-weight: 600;
    letter-spacing: 0.025em;
}

.cta-buttons .btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 15px 30px rgba(0,0,0,0.2) !important;
}

/* Additional Animations */
@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.animate-slide-in-left {
    animation: slideInLeft 0.8s ease-out;
}

.animate-slide-in-right {
    animation: slideInRight 0.8s ease-out;
}

/* Property Card Hover Effects */
.property-card:hover .property-image img {
    transform: scale(1.05);
}

.property-card:hover .property-price-tag {
    transform: scale(1.05);
}

/* Search Form Enhancements */
.search-card .form-control:focus,
.search-card .form-select:focus {
    border-color: #0ea5e9;
    box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25);
    transform: translateY(-2px);
}

/* Mobile Optimizations */
@media (max-width: 768px) {
    .cta-shapes .cta-shape {
        display: none;
    }
    
    .display-3 {
        font-size: 2rem;
    }
    
    .cta-buttons .btn {
        display: block;
        width: 100%;
        margin-bottom: 1rem;
    }
    
    .cta-buttons .btn:last-child {
        margin-bottom: 0;
    }
    
    .service-card,
    .testimonial-card {
        margin-bottom: 2rem;
    }
}

@media (max-width: 576px) {
    .display-3 {
        font-size: 1.75rem;
    }
    
    .display-4 {
        font-size: 1.75rem;
    }
    
    .btn-lg {
        padding: 0.75rem 1.5rem;
        font-size: 1rem;
    }
}
</style>

<script>
// Property card interactions
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

function viewProperty(propertyName) {
    // This would typically navigate to a property detail page
    alert(`Viewing details for: ${propertyName}`);
    // In a real implementation, this would be:
    // window.location.href = `/properties/${propertyId}`;
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

// Add loading animation to search form
document.querySelector('form[action*="search"]').addEventListener('submit', function() {
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="icon ni ni-loader"></i>';
    submitBtn.disabled = true;
    
    // Re-enable after a short delay (in real implementation, this would be handled by the response)
    setTimeout(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }, 2000);
});

// Initialize tooltips if Bootstrap is available
if (typeof bootstrap !== 'undefined') {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}
</script>
@endsection