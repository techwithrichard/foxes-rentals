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
                            <h3 class="nk-block-title page-title">{{ __('Add Rental Property') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Create a new rental property listing') }}</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-menu-alt-r"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        <li>
                                            <a href="{{ route('admin.rental-properties.index') }}" class="btn btn-outline-primary">
                                                <em class="icon ni ni-arrow-left"></em>
                                                <span>{{ __('Back to Properties') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Type Selection -->
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Select Property Type') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Property Type') }}</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select" id="propertyType" name="property_type">
                                                <option value="">{{ __('Select Property Type') }}</option>
                                                <option value="single">{{ __('Single Unit Property') }}</option>
                                                <option value="multi">{{ __('Multi Unit Property') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">{{ __('Property Category') }}</label>
                                        <div class="form-control-wrap">
                                            <select class="form-select" id="propertyCategory" name="property_category">
                                                <option value="">{{ __('Select Category') }}</option>
                                                <option value="apartment">{{ __('Apartment') }}</option>
                                                <option value="house">{{ __('House') }}</option>
                                                <option value="studio">{{ __('Studio') }}</option>
                                                <option value="commercial">{{ __('Commercial') }}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Property Creation Form -->
                <div class="nk-block" id="propertyForm" style="display: none;">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Property Details') }}</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('admin.rental-properties.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                
                                <!-- Basic Information -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Basic Information') }}</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Property Name') }} <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" name="name" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Property Type') }}</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" name="property_type_id" required>
                                                    <option value="">{{ __('Select Property Type') }}</option>
                                                    <!-- Property types will be loaded dynamically -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location Information -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Location Information') }}</h6>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Address') }} <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" name="address" rows="3" required></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('City') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" name="city">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('State/County') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" name="state">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Postal Code') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="text" class="form-control" name="postal_code">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Financial Information -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Financial Information') }}</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Monthly Rent') }} <span class="text-danger">*</span></label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" name="rent_amount" step="0.01" required>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Security Deposit') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" name="deposit_amount" step="0.01">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Currency') }}</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" name="currency">
                                                    <option value="KES">KES (Kenyan Shilling)</option>
                                                    <option value="USD">USD (US Dollar)</option>
                                                    <option value="EUR">EUR (Euro)</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Property Details -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Property Details') }}</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Bedrooms') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" name="bedrooms" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Bathrooms') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" name="bathrooms" min="0" step="0.5">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Square Feet') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" name="square_feet" min="0">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Parking Spaces') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="number" class="form-control" name="parking_spaces" min="0">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Description -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Description') }}</h6>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Property Description') }}</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control" name="description" rows="5"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Features and Amenities -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Features & Amenities') }}</h6>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Features') }}</label>
                                            <div class="form-control-wrap">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="air_conditioning" name="features[]" value="air_conditioning">
                                                            <label class="custom-control-label" for="air_conditioning">{{ __('Air Conditioning') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="heating" name="features[]" value="heating">
                                                            <label class="custom-control-label" for="heating">{{ __('Heating') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="balcony" name="features[]" value="balcony">
                                                            <label class="custom-control-label" for="balcony">{{ __('Balcony') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="garden" name="features[]" value="garden">
                                                            <label class="custom-control-label" for="garden">{{ __('Garden') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Amenities') }}</label>
                                            <div class="form-control-wrap">
                                                <div class="row g-2">
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="swimming_pool" name="amenities[]" value="swimming_pool">
                                                            <label class="custom-control-label" for="swimming_pool">{{ __('Swimming Pool') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="gym" name="amenities[]" value="gym">
                                                            <label class="custom-control-label" for="gym">{{ __('Gym') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="security" name="amenities[]" value="security">
                                                            <label class="custom-control-label" for="security">{{ __('Security') }}</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox" class="custom-control-input" id="elevator" name="amenities[]" value="elevator">
                                                            <label class="custom-control-label" for="elevator">{{ __('Elevator') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Images -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Property Images') }}</h6>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Upload Images') }}</label>
                                            <div class="form-control-wrap">
                                                <input type="file" class="form-control" name="images[]" multiple accept="image/*">
                                                <div class="form-note">{{ __('You can upload multiple images. Supported formats: JPG, PNG, GIF') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Status and Settings -->
                                <div class="row mb-4">
                                    <div class="col-12">
                                        <h6 class="mb-3">{{ __('Status & Settings') }}</h6>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Status') }}</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" name="status">
                                                    <option value="active">{{ __('Active') }}</option>
                                                    <option value="inactive">{{ __('Inactive') }}</option>
                                                    <option value="pending">{{ __('Pending') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Availability') }}</label>
                                            <div class="form-control-wrap">
                                                <select class="form-select" name="is_vacant">
                                                    <option value="1">{{ __('Available for Rent') }}</option>
                                                    <option value="0">{{ __('Currently Occupied') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Featured Property') }}</label>
                                            <div class="form-control-wrap">
                                                <div class="custom-control custom-switch">
                                                    <input type="checkbox" class="custom-control-input" id="is_featured" name="is_featured" value="1">
                                                    <label class="custom-control-label" for="is_featured">{{ __('Mark as Featured') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Form Actions -->
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">
                                                <em class="icon ni ni-save me-2"></em>
                                                {{ __('Create Property') }}
                                            </button>
                                            <a href="{{ route('admin.rental-properties.index') }}" class="btn btn-outline-secondary">
                                                <em class="icon ni ni-arrow-left me-2"></em>
                                                {{ __('Cancel') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
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
document.addEventListener('DOMContentLoaded', function() {
    const propertyTypeSelect = document.getElementById('propertyType');
    const propertyForm = document.getElementById('propertyForm');
    
    propertyTypeSelect.addEventListener('change', function() {
        if (this.value) {
            propertyForm.style.display = 'block';
        } else {
            propertyForm.style.display = 'none';
        }
    });
});
</script>
@endpush
