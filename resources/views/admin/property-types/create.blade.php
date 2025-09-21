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
                                        <h5 class="nk-block-title">{{ __('Create Property Type') }}</h5>
                                        <span>{{ __('Add a new property type to your system') }}</span>
                                    </div>
                                    <div class="nk-block-head-content">
                                        <a href="{{ route('admin.property-types.index') }}" class="btn btn-outline-secondary">
                                            <em class="icon ni ni-arrow-left"></em>
                                            <span>{{ __('Back to Property Types') }}</span>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <form action="{{ route('admin.property-types.store') }}" method="POST" class="form-validate">
                                @csrf
                                
                                <div class="row g-4">
                                    <!-- Basic Information -->
                                    <div class="col-lg-8">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h6 class="title">{{ __('Basic Information') }}</h6>
                                                
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="name">{{ __('Property Type Name') }} <span class="text-danger">*</span></label>
                                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                                   id="name" name="name" value="{{ old('name') }}" 
                                                                   placeholder="{{ __('e.g., Single-Family Homes') }}" required>
                                                            @error('name')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label class="form-label" for="category">{{ __('Category') }} <span class="text-danger">*</span></label>
                                                            <select class="form-select @error('category') is-invalid @enderror" 
                                                                    id="category" name="category" required>
                                                                <option value="">{{ __('Select Category') }}</option>
                                                                <option value="residential" {{ old('category') == 'residential' ? 'selected' : '' }}>
                                                                    🏠 {{ __('Residential') }}
                                                                </option>
                                                                <option value="commercial" {{ old('category') == 'commercial' ? 'selected' : '' }}>
                                                                    🏢 {{ __('Commercial') }}
                                                                </option>
                                                                <option value="industrial" {{ old('category') == 'industrial' ? 'selected' : '' }}>
                                                                    🏭 {{ __('Industrial') }}
                                                                </option>
                                                                <option value="land" {{ old('category') == 'land' ? 'selected' : '' }}>
                                                                    🌿 {{ __('Land') }}
                                                                </option>
                                                                <option value="mixed-use" {{ old('category') == 'mixed-use' ? 'selected' : '' }}>
                                                                    🏘️ {{ __('Mixed-Use') }}
                                                                </option>
                                                            </select>
                                                            @error('category')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="description">{{ __('Description') }}</label>
                                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                                      id="description" name="description" rows="4" 
                                                                      placeholder="{{ __('Describe this property type...') }}">{{ old('description') }}</textarea>
                                                            @error('description')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Settings & Appearance -->
                                    <div class="col-lg-4">
                                        <div class="card">
                                            <div class="card-inner">
                                                <h6 class="title">{{ __('Settings & Appearance') }}</h6>
                                                
                                                <div class="row g-3">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="icon">{{ __('Icon Class') }}</label>
                                                            <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                                                   id="icon" name="icon" value="{{ old('icon', 'ni ni-building') }}" 
                                                                   placeholder="{{ __('e.g., ni ni-home') }}">
                                                            <small class="form-text text-muted">{{ __('Use NioIcon classes (e.g., ni ni-home, ni ni-building)') }}</small>
                                                            @error('icon')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="color">{{ __('Color') }}</label>
                                                            <input type="color" class="form-control form-control-color @error('color') is-invalid @enderror" 
                                                                   id="color" name="color" value="{{ old('color', '#4CAF50') }}">
                                                            @error('color')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label class="form-label" for="sort_order">{{ __('Sort Order') }}</label>
                                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}" 
                                                                   min="0" placeholder="0">
                                                            <small class="form-text text-muted">{{ __('Lower numbers appear first') }}</small>
                                                            @error('sort_order')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <div class="custom-control custom-switch">
                                                                <input type="checkbox" class="custom-control-input" 
                                                                       id="is_active" name="is_active" value="1" 
                                                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                                                <label class="custom-control-label" for="is_active">
                                                                    {{ __('Active') }}
                                                                </label>
                                                            </div>
                                                            <small class="form-text text-muted">{{ __('Only active property types will be available for selection') }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Form Actions -->
                                <div class="row g-3 mt-4">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <button type="submit" class="btn btn-primary">
                                                <em class="icon ni ni-save"></em>
                                                <span>{{ __('Create Property Type') }}</span>
                                            </button>
                                            <a href="{{ route('admin.property-types.index') }}" class="btn btn-outline-secondary ms-2">
                                                <em class="icon ni ni-cross"></em>
                                                <span>{{ __('Cancel') }}</span>
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.querySelector('.form-validate');
    if (form) {
        form.addEventListener('submit', function(e) {
            const name = document.getElementById('name').value.trim();
            const category = document.getElementById('category').value;
            
            if (!name) {
                e.preventDefault();
                alert('{{ __("Property type name is required") }}');
                document.getElementById('name').focus();
                return false;
            }
            
            if (!category) {
                e.preventDefault();
                alert('{{ __("Category is required") }}');
                document.getElementById('category').focus();
                return false;
            }
        });
    }
    
    // Preview icon
    const iconInput = document.getElementById('icon');
    const colorInput = document.getElementById('color');
    
    function updatePreview() {
        const iconClass = iconInput.value || 'ni ni-building';
        const color = colorInput.value || '#4CAF50';
        
        // You can add a preview element here if needed
        console.log('Icon:', iconClass, 'Color:', color);
    }
    
    iconInput.addEventListener('input', updatePreview);
    colorInput.addEventListener('input', updatePreview);
});
</script>
@endpush
@endsection
