<div class="nk-block">
    <div class="card card-bordered">
        <div class="card-inner-group">
            <div class="card-inner">
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h5 class="title nk-block-title">{{ __('Property Details')}}</h5>
                        <p>{{ __('Add common information like Name, Type, Electricity ID etc')}} </p>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="row gy-4">
                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">

                                <label class="form-label" for="pname">{{ __('Property Name')}} <span
                                        class="text-danger">*</span></label>


                                <input type="text"
                                       class="form-control @error('propertyName') is-invalid @enderror "
                                       id="pname" wire:model.defer="propertyName">

                                @error('propertyName')
                                <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror
                            </div>

                        </div>
                        <!--col-->
                        <div class="col-xxl-3 col-md-3">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label">{{ __('Property Type')}}<span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="form-control-wrap">
                                    <div class="form-control-select">
                                        <select class="form-control @error('type') is-invalid @enderror"
                                                id="propertyType" wire:model="property_type"
                                                data-placeholder="{{ __('Select property type')}}">
                                            <option value="">{{ __('Select Property Type') }}</option>
                                            @foreach($propertyTypes as $propertyType)
                                                <option value="{{ $propertyType->name }}">
                                                    @switch($propertyType->category)
                                                        @case('residential')
                                                            🏠 {{ $propertyType->name }}
                                                            @break
                                                        @case('office')
                                                            🏢 {{ $propertyType->name }}
                                                            @break
                                                        @case('retail')
                                                            🛍️ {{ $propertyType->name }}
                                                            @break
                                                        @case('industrial')
                                                            🏭 {{ $propertyType->name }}
                                                            @break
                                                        @case('hospitality')
                                                            🏨 {{ $propertyType->name }}
                                                            @break
                                                        @case('healthcare')
                                                            🏥 {{ $propertyType->name }}
                                                            @break
                                                        @case('mixed-use')
                                                            🏘️ {{ $propertyType->name }}
                                                            @break
                                                        @case('land')
                                                            🌿 {{ $propertyType->name }}
                                                            @break
                                                        @default
                                                            {{ $propertyType->name }}
                                                    @endswitch
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                @error('type')
                                <p class="text-danger fs-12px">
                                    {{ $message }}
                                </p>
                                @enderror


                            </div>
                        </div>
                        <!--col-->
                        <div class="col-xxl-3 col-md-5">
                            <div class="form-group">
                                <label class="form-label" for="default-06">{{ __('Select Landlord')}}</label>
                                <div class="form-control-wrap ">
                                    <div class="form-control-select">
                                        <select class="form-control @error('property_landlord') is-invalid @enderror"
                                                id="default-06" wire:model="property_landlord">
                                            <option value="">{{ __('Select Landlord')}}</option>
                                            @foreach($landlords as $landlord)
                                                <option value="{{ $landlord->id }}">{{ $landlord->name }}
                                                    <span> ({{$landlord->email}})</span></option>
                                            @endforeach
                                        </select>

                                        @error('property_landlord')
                                        <span class="invalid-feedback">
                                            {{ $message }}
                                        </span>
                                        @enderror


                                    </div>

                                    <div class="form-text">
                                        {{ __('Leave blank if units are multi-owned.')}}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--col-->
                        <div class="col-xxl-3 col-md-4">
                            <div class="form-group">
                                <label class="form-label">{{ __('Electricity ID')}}</label>
                                <input type="text"
                                       class="form-control @error('electricity_id') is-invalid @enderror "
                                       id="pname" wire:model.defer="electricity_id">

                                @error('electricity_id')
                                <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                @enderror


                                <div class="form-text">
                                    <span>{{ __('Enter the electricity ID of the property')}}</span>
                                </div>

                            </div>
                        </div>
                        <!--col-->
                        <div class="col-xxl-9 col-md-8">
                            <div class="form-group">
                                <label class="form-label"
                                       for="cp1-profile-description">{{__('Property Description')}}</label>
                                <div class="form-control-wrap">
                                    <input class="form-control" wire:model.defer="description">
                                </div>
                            </div>
                        </div>
                        <!--col-->
                    </div>
                    <!--row-->
                </div>
            </div><!-- .card-inner -->
            <div class="card-inner">
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <h5 class="title nk-block-title">{{ __('Property Address')}}</h5>
                        <p>{{ __('Add location where property is located')}} </p>
                    </div>
                </div>
                <div class="nk-block">
                    <div class="row gy-4">

                        <div class="col-xxl-3 col-md-6">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label">{{ __('Address Line 1')}} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="form-control-group">
                                    <input type="text"
                                           class="form-control @error('address1') is-invalid @enderror "
                                           name="street_address" id="street-address"
                                           placeholder="{{ __('Street address')}}" wire:model="address1">

                                    @error('address1')
                                    <p class="text-danger fs-12px">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div>
                        <div class="col-xxl-3 col-md-6">
                            <div class="form-group">

                                <div class="form-label-group">
                                    <label class="form-label">{{ __('Address 2')}} <span
                                            class="text-danger">*</span></label>
                                </div>

                                <div class="form-control-group">
                                    <input type="text" class="form-control" id="address2"
                                           placeholder="{{ __('Address 2,e.g apartment,suite,floor')}}"
                                           wire:model.defer="address2">
                                </div>
                            </div>
                        </div><!-- .col -->


                        <div class="col-xxl-3 col-md-6">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label">{{ __('City')}} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="form-control-group">
                                    <input type="text"
                                           class="form-control @error('city') is-invalid @enderror"
                                           id="locality" name="locality" wire:model="city">
                                    @error('city')
                                    <p class="text-danger fs-12px">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label">{{ __('State')}}</label>
                                </div>
                                <div class="form-control-group">
                                    <input type="text" class="form-control" id="state" name="state"
                                           wire:model.defer="state">
                                </div>
                            </div>
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label">{{ __('Country')}} <span
                                            class="text-danger">*</span></label>
                                </div>
                                <div class="form-control-group">
                                    <input type="text"
                                           class="form-control @error('country') is-invalid @enderror"
                                           id="country" name="country" wire:model.defer="country">

                                    @error('country')
                                    <p class="text-danger fs-12px">
                                        {{ $message }}
                                    </p>
                                    @enderror
                                </div>
                            </div>
                        </div><!-- .col -->
                        <div class="col-xxl-3 col-md-6">
                            <div class="form-group">
                                <div class="form-label-group">
                                    <label class="form-label">{{ __('Zip Code')}}</label>
                                </div>
                                <div class="form-control-group">
                                    <input type="text" class="form-control" id="postcode" name="postcode"
                                           wire:model.defer="zip">
                                </div>
                            </div>
                        </div><!-- .col -->

                        <!--col-->
                    </div>
                    <!--row-->
                </div>
            </div><!-- .card-inner -->
            <div class="card-inner">
                <div class="nk-block-head nk-block-head-sm">

                    <div class="nk-block-between">
                        <div class="nk-block-head-content me-3">
                            <h5 class="title nk-block-title">{{ __('Add Units')}}</h5>
                            <p>{{ __('Defines whether to attach multiple house units to this property. This step is optional
                                and units can be added at a later time by navigating to the Houses section.Note that
                                below step will only generate houses which has same landlord,same property type and same rent and
                                agency commission.For more control over units,please add them manually by navigating to the Houses section.')}}
                            </p>
                        </div>

                        <div class="nk-block-head-content">
                            <div class="custom-control custom-switch">
                                <input
                                    type="checkbox"
                                    wire:model="shouldGenerateUnits"
                                    class="custom-control-input"
                                    id="customSwitch1">
                                <label class="custom-control-label fw-bolder" for="customSwitch1">{{ __('Generate Units')}}</label>
                            </div>
                        </div>
                    </div>

                </div>

                @if($shouldGenerateUnits)
                    <div class="nk-block">
                        <div class="row gy-4">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Select Owner')}}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control @error('property_id') is-invalid @enderror"
                                                    wire:model="landlord">
                                                <option value="">{{ __('Select Landlord')}}</option>
                                                @foreach($landlords as $landlord)
                                                    <option value="{{ $landlord->id }}">{{ $landlord->name }}
                                                        ({{ $landlord->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('property_id')
                                            <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Numbering Scheme Selection -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Numbering Scheme')}}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control @error('numberingScheme') is-invalid @enderror" wire:model="numberingScheme">
                                                <option value="simple">{{ __('Simple (Prefix + Number + Suffix)')}}</option>
                                                <option value="floor_based">{{ __('Floor-Based (G1, A1, B1, etc.)')}}</option>
                                                <option value="alphabetical">{{ __('Alphabetical (A, B, C, etc.)')}}</option>
                                                <option value="numeric_sequence">{{ __('Numeric Sequence (Unit 001, 002, etc.)')}}</option>
                                                <option value="custom">{{ __('Custom Names')}}</option>
                                            </select>
                                            @error('numberingScheme')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Floor-Based Configuration -->
                            @if($numberingScheme === 'floor_based')
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6>{{ __('Floor-Based Numbering Setup')}}</h6>
                                    <p class="mb-0">{{ __('Follow these steps to set up floor-based house numbering:')}}</p>
                                    <ol class="mb-0 mt-2">
                                        <li>{{ __('Enter the total number of floors in your building')}}</li>
                                        <li>{{ __('Choose how to label the ground floor (e.g., G, GF, 0)')}}</li>
                                        <li>{{ __('Choose your configuration method:')}}</li>
                                        <ul class="mb-0">
                                            <li>{{ __('Leave both unchecked: Configure each floor individually')}}</li>
                                            <li>{{ __('Check "Same houses": All floors have the same number of houses')}}</li>
                                            <li>{{ __('Check "Different houses": Use floor ranges for mixed configurations')}}</li>
                                        </ul>
                                    </ol>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Number of Floors')}}</label>
                                    <input type="number" class="form-control @error('numberOfFloors') is-invalid @enderror"
                                           wire:model="numberOfFloors" min="1">
                                    @error('numberOfFloors')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Ground Floor Label')}}</label>
                                    <input type="text" class="form-control @error('groundFloorLabel') is-invalid @enderror"
                                           wire:model="groundFloorLabel" placeholder="G">
                                    <span class="form-text">{{ __('How to label the ground floor')}}</span>
                                    @error('groundFloorLabel')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Total Houses')}}</label>
                                    <input type="number" class="form-control @error('numberOfHouses') is-invalid @enderror"
                                           wire:model="numberOfHouses" min="1" readonly 
                                           style="background-color: #f8f9fa; cursor: not-allowed;">
                                    <span class="form-text">{{ __('Automatically calculated based on floor configuration')}}</span>
                                    @error('numberOfHouses')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Quick Setup Section -->
                            <div class="col-md-12">
                                <div class="alert alert-success">
                                    <h6><i class="icon ni ni-check-circle"></i> {{ __('Quick Setup - Same Houses Per Floor')}}</h6>
                                    <p class="mb-3">{{ __('Most common setup: All floors have the same number of houses.')}}</p>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Number of Houses per Floor')}} <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control @error('housesPerFloor') is-invalid @enderror"
                                                       wire:model="housesPerFloor" min="1" placeholder="e.g., 8"
                                                       style="font-size: 18px; font-weight: bold;">
                                                <span class="form-text">{{ __('This applies to ALL floors including ground floor')}}</span>
                                                @error('housesPerFloor')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">{{ __('Auto-Apply to All Floors')}}</label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" 
                                                           wire:model="sameHousesPerFloor" id="sameHousesPerFloor">
                                                    <label class="custom-control-label" for="sameHousesPerFloor">
                                                        <strong>{{ __('Apply same number to all floors')}}</strong>
                                                        <br><small class="text-muted">{{ __('Automatically distribute houses evenly')}}</small>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    @if($sameHousesPerFloor && $housesPerFloor > 0)
                                    <div class="alert alert-info mt-3">
                                        <strong>{{ __('Preview:')}}</strong> {{ __('Each floor will have')}} <strong>{{ $housesPerFloor }}</strong> {{ __('houses')}}
                                        <br><strong>{{ __('Total Houses:')}}</strong> <span class="badge badge-primary">{{ $numberOfFloors * $housesPerFloor }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            
                            <!-- Advanced Options -->
                            <div class="col-md-12">
                                <div class="alert alert-warning">
                                    <h6><i class="icon ni ni-setting"></i> {{ __('Advanced Options')}}</h6>
                                    <p class="mb-2">{{ __('Need different configurations? Use these options:')}}</p>
                                </div>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" 
                                               wire:model="useFloorRanges" id="useFloorRanges">
                                        <label class="custom-control-label" for="useFloorRanges">
                                            <strong>{{ __('Use floor ranges for different configurations')}}</strong>
                                            <br><small class="text-muted">{{ __('Check this to define different house counts for different floor ranges')}}</small>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            @if(!$sameHousesPerFloor && !$useFloorRanges)
                            <div class="col-md-12">
                                <h6>{{ __('Floor Configuration')}}</h6>
                                @foreach($floorConfig as $index => $floor)
                                <div class="row mb-2">
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Floor')}} {{ $floor['floor'] }} ({{ $floor['label'] }})</label>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="number" class="form-control" 
                                               wire:model="floorConfig.{{ $index }}.houses" 
                                               wire:change="updatedFloorConfig($event.target.value, {{ $index }})" min="0">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                            
                            <!-- Floor Ranges Configuration -->
                            @if($useFloorRanges)
                            <div class="col-md-12">
                                <h6>{{ __('Floor Ranges Configuration')}}</h6>
                                <p class="text-muted">{{ __('Define different house counts for different floor ranges')}}</p>
                                
                                @foreach($floorRanges as $index => $range)
                                <div class="row mb-3 p-3 border rounded">
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Start Floor')}}</label>
                                        <input type="number" class="form-control" 
                                               wire:model="floorRanges.{{ $index }}.start_floor" 
                                               min="1" max="{{ $numberOfFloors }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('End Floor')}}</label>
                                        <input type="number" class="form-control" 
                                               wire:model="floorRanges.{{ $index }}.end_floor" 
                                               min="1" max="{{ $numberOfFloors }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">{{ __('Houses per Floor')}}</label>
                                        <input type="number" class="form-control" 
                                               wire:model="floorRanges.{{ $index }}.houses_per_floor" 
                                               min="0">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div>
                                            @if(count($floorRanges) > 1)
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    wire:click="removeFloorRange({{ $index }})">
                                                {{ __('Remove')}}
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                                
                                <button type="button" class="btn btn-sm btn-outline-primary" 
                                        wire:click="addFloorRange">
                                    {{ __('Add Floor Range')}}
                                </button>
                                
                                <button type="button" class="btn btn-sm btn-primary ml-2" 
                                        wire:click="applyFloorRanges">
                                    {{ __('Apply Floor Ranges')}}
                                </button>
                            </div>
                            @endif
                            @endif
                            
                            <!-- Simple Numbering Options -->
                            @if($numberingScheme === 'simple')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="email">{{ __('Prefix')}}</label>
                                    <input type="text" class="form-control @error('prefix') is-invalid @enderror"
                                           id="email"
                                           wire:model="prefix">
                                    <span class="form-text">{{ __('Sets the first part of name')}}</span>
                                    @error('prefix')
                                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            
                            <!-- Alphabetical Numbering Options -->
                            @if($numberingScheme === 'alphabetical')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Start Letter')}}</label>
                                    <input type="text" class="form-control @error('alphabeticalStart') is-invalid @enderror"
                                           wire:model="alphabeticalStart" maxlength="1">
                                    @error('alphabeticalStart')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('End Letter')}}</label>
                                    <input type="text" class="form-control @error('alphabeticalEnd') is-invalid @enderror"
                                           wire:model="alphabeticalEnd" maxlength="1">
                                    @error('alphabeticalEnd')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            
                            <!-- Custom Numbering Options -->
                            @if($numberingScheme === 'custom')
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <h6>{{ __('Custom Numbering Setup')}}</h6>
                                    <p class="mb-0">{{ __('Define custom names for each unit. You can specify different prefixes and suffixes for each unit.')}}</p>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <h6>{{ __('Custom Prefixes')}}</h6>
                                        @for($i = 0; $i < $numberOfHouses; $i++)
                                        <div class="form-group mb-2">
                                            <label class="form-label">{{ __('Unit')}} {{ $i + 1 }} {{ __('Prefix')}}</label>
                                            <input type="text" class="form-control" 
                                                   wire:model="customPrefixes.{{ $i }}" 
                                                   placeholder="e.g., Suite, Room, Apartment">
                                        </div>
                                        @endfor
                                    </div>
                                    <div class="col-md-6">
                                        <h6>{{ __('Custom Suffixes')}}</h6>
                                        @for($i = 0; $i < $numberOfHouses; $i++)
                                        <div class="form-group mb-2">
                                            <label class="form-label">{{ __('Unit')}} {{ $i + 1 }} {{ __('Suffix')}}</label>
                                            <input type="text" class="form-control" 
                                                   wire:model="customSuffixes.{{ $i }}" 
                                                   placeholder="e.g., 101, A, North">
                                        </div>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Common fields for simple, alphabetical, and numeric sequence -->
                            @if(in_array($numberingScheme, ['simple', 'alphabetical', 'numeric_sequence']))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="phone-no">{{ __('Total Houses')}}</label>
                                    <input type="number" class="form-control" id="phone-no"
                                           wire:model="numberOfHouses">
                                    <span class="form-text">{{ __('No of houses to create')}}</span>
                                    @error('numberOfHouses')
                                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                    @enderror
                                </div>
                            </div>
                            @endif
                            
                            <!-- Start Number (only for simple numbering) -->
                            @if($numberingScheme === 'simple')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="start-number">{{ __('Numbering From')}}</label>
                                    <input type="number" class="form-control" id="start-number"
                                           wire:model="startNumber">
                                    <span class="form-text">{{ __('Starts from number')}}</span>
                                    @error('startNumber')
                                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                    @enderror

                                </div>
                            </div>
                            @endif
                            
                            <!-- Suffix (only for simple numbering) -->
                            @if($numberingScheme === 'simple')
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="suffix">{{ __('Suffix')}}</label>
                                    <input type="text" class="form-control" id="suffix" wire:model="suffix">
                                    <span class="form-text">{{ __('Sets the last part of name')}}</span>
                                </div>
                            </div>
                            @endif
                            
                            <!-- Real-Time Unit Names Preview -->
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Real-Time Preview of Units')}}</label>
                                    <div class="form-control-wrap">
                                        <div class="alert alert-info">
                                            @if($shouldGenerateUnits)
                                                @php
                                                    $previewNames = [];
                                                    $totalUnits = 0;
                                                    
                                                    switch ($numberingScheme) {
                                                        case 'simple':
                                                            $totalUnits = $numberOfHouses;
                                                            for ($i = 0; $i < min($numberOfHouses, 15); $i++) {
                                                                $previewNames[] = $prefix . ($startNumber + $i) . $suffix;
                                                            }
                                                            break;
                                                            
                                                        case 'floor_based':
                                                            $houseIndex = 0;
                                                            foreach ($floorConfig as $floorIndex => $floor) {
                                                                if ($floor['houses'] > 0) {
                                                                    $totalUnits += $floor['houses'];
                                                                    $maxPreview = min($floor['houses'], 3); // Show max 3 per floor
                                                                    for ($i = 0; $i < $maxPreview && $houseIndex < 15; $i++) {
                                                                        $previewNames[] = $floor['label'] . ($i + 1);
                                                                        $houseIndex++;
                                                                    }
                                                                    if ($floor['houses'] > 3 && $houseIndex < 15) {
                                                                        $previewNames[] = '...';
                                                                        $houseIndex++;
                                                                    }
                                                                }
                                                            }
                                                            break;
                                                            
                                                        case 'alphabetical':
                                                            $totalUnits = $numberOfHouses;
                                                            $alphabet = range($alphabeticalStart, $alphabeticalEnd);
                                                            for ($i = 0; $i < min($numberOfHouses, 15); $i++) {
                                                                $previewNames[] = $alphabet[$i % count($alphabet)] . ($i >= count($alphabet) ? floor($i / count($alphabet)) + 1 : '');
                                                            }
                                                            break;
                                                            
                                                        case 'numeric_sequence':
                                                            $totalUnits = $numberOfHouses;
                                                            for ($i = 0; $i < min($numberOfHouses, 15); $i++) {
                                                                $previewNames[] = 'Unit ' . str_pad($startNumber + $i, 3, '0', STR_PAD_LEFT);
                                                            }
                                                            break;
                                                            
                                                        case 'custom':
                                                            $totalUnits = $numberOfHouses;
                                                            for ($i = 0; $i < min($numberOfHouses, 15); $i++) {
                                                                $prefix = $customPrefixes[$i] ?? 'Unit';
                                                                $suffix = $customSuffixes[$i] ?? ($i + 1);
                                                                $previewNames[] = $prefix . ' ' . $suffix;
                                                            }
                                                            break;
                                                    }
                                                @endphp
                                                
                                                <div class="row">
                                                    <div class="col-md-12 mb-2">
                                                        <strong>{{ __('Total Units to be Created:')}} {{ $totalUnits }}</strong>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <strong>{{ __('Preview Names:')}}</strong>
                                                        <div class="row mt-2">
                                                            @foreach($previewNames as $name)
                                                                <div class="col-md-2 col-sm-3 col-4 mb-1">
                                                                    <span class="badge badge-primary">{{ $name }}</span>
                                                                </div>
                                                            @endforeach
                                                            @if($totalUnits > 15)
                                                                <div class="col-md-2 col-sm-3 col-4 mb-1">
                                                                    <span class="badge badge-secondary">+{{ $totalUnits - 15 }} more</span>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                @if($numberingScheme === 'floor_based' && !empty($floorConfig))
                                                    <div class="row mt-3">
                                                        <div class="col-md-12">
                                                            <strong>{{ __('Floor Distribution:')}}</strong>
                                                            <div class="row mt-2">
                                                                @foreach($floorConfig as $floor)
                                                                    @if($floor['houses'] > 0)
                                                                        <div class="col-md-3 col-sm-4 col-6 mb-1">
                                                                            <span class="badge badge-outline-primary">
                                                                                {{ $floor['label'] }}: {{ $floor['houses'] }} units
                                                                            </span>
                                                                        </div>
                                                                    @endif
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            @else
                                                <em>{{ __('Enable "Generate Units" and configure the numbering scheme above to see preview')}}</em>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="lead-address">{{ __('Rent')}}</label>
                                    <input type="text" class="form-control @error('baseRent') is-invalid @enderror"
                                           id="lead-address"
                                           wire:model.defer="baseRent">
                                    <span class="form-text">{{ __('Defines base rent for all units')}}</span>
                                    @error('baseRent')
                                    <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('House Type')}}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control @error('type') is-invalid @enderror"
                                                    wire:model="baseType">
                                                <option value="">{{ __('House type')}}</option>
                                                @foreach($types as $houseType)
                                                    <option value="{{ $houseType }}">{{ $houseType }}</option>
                                                @endforeach
                                            </select>

                                            @error('type')
                                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="lead-name">{{ __('Commission')}}</label>

                                    <div class="form-control-wrap">
                                        <div class="form-text-hint">
                                            <span class="overline-title">%</span>
                                        </div>
                                        <input type="number"
                                               class="form-control @error('commission') is-invalid @enderror"
                                               wire:model.defer="commission"
                                               id="lead-name">
                                        @error('commission')
                                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                                        @enderror
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="lead-name">{{ __('Electricity ID')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                               class="form-control @error('electricity_id') is-invalid @enderror"
                                               wire:model.defer="electricity_id"
                                               id="lead-name">
                                        @error('electricity_id')
                                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 bg-white-1 p-3 m-2">
                                {{ __('Example of house name/no based on parameters provided:')}}
                                <strong>{{ $prefix .''.$startNumber.''.$suffix }}</strong>
                            </div>

                        </div>
                    </div>
                @endif
            </div><!-- .card-inner -->

            <div class="card-inner">
                <div class="nk-block">
                    <div class="row gy-4">

                        @if(session()->has('error'))
                            <div class="col-md-12">
                                <div class="alert alert-danger">
                                    {{ session('error') }}
                                </div>
                            </div>

                        @endif

                        @if($errors->any())
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        @endif

                        <div class="col-12">
                            <x-button loading="{{__('Saving...')}}" wire:click="submit" class="btn btn-lg btn-primary">
                                {{__('Add Property')}}
                            </x-button>
                        </div>
                        <!--col-->
                    </div>
                    <!--row-->
                </div>
            </div><!-- .card-inner -->
        </div>
    </div><!-- .card -->
</div><!-- .nk-block -->
