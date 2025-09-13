<div>
    <div class="row gy-4">

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label">{{ __('Parent Property')}}</label>
                <div class="form-control-wrap">
                    <div class="form-control-select">
                        <select class="form-control @error('basePropertyId') is-invalid @enderror"
                                wire:model="basePropertyId">
                            <option value="">{{ __('Select property')}}</option>
                            @foreach($properties as $key=>$value)
                                <option value="{{ $key }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        @error('basePropertyId')
                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="form-group">
                <label class="form-label">{{ __('Select Owner')}}</label>
                <div class="form-control-wrap">
                    <div class="form-control-select">
                        <select class="form-control @error('property_id') is-invalid @enderror"
                                wire:model="landlord">
                            <option value="">{{ __('Select Landlord')}}</option>
                            @foreach($landlords as $landlord)
                                <option value="{{ $landlord->id }}">{{ $landlord->name }} ({{ $landlord->email }})
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

        <!-- Simple Numbering Options -->
        @if($numberingScheme === 'simple')
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="email">{{ __('Prefix')}}</label>
                <input type="text" class="form-control @error('prefix') is-invalid @enderror" id="email"
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
                    <li>{{ __('Specify how many houses/rooms are on each floor or floor range')}}</li>
                </ol>
            </div>
        </div>
        
        <!-- Number of Floors -->
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">{{ __('Number of Floors')}}</label>
                <input type="number" class="form-control" 
                       wire:model="numberOfFloors" 
                       min="1" max="10" placeholder="How many floors?">
                <span class="form-text">{{ __('Total number of floors in the building')}}</span>
            </div>
        </div>
        
        <!-- Ground Floor Label -->
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">{{ __('Ground Floor Label')}}</label>
                <input type="text" class="form-control" 
                       wire:model="groundFloorLabel" 
                       maxlength="2" placeholder="G">
                <span class="form-text">{{ __('How will you label the ground floor? (e.g., G, GF, 0)')}}</span>
            </div>
        </div>
        
        <!-- Floor Configuration Options -->
        <div class="col-md-12">
            <div class="form-group">
                <div class="alert alert-light">
                    <small class="text-muted">{{ __('Choose one option below (or leave both unchecked for individual floor configuration):')}}</small>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" 
                                   wire:model="sameHousesPerFloor" 
                                   id="sameHousesPerFloor">
                            <label class="form-check-label" for="sameHousesPerFloor">
                                {{ __('Same number of houses on each floor')}}
                            </label>
                        </div>
                        <span class="form-text">{{ __('Check this if all floors have the same number of houses')}}</span>
                    </div>
                    <div class="col-md-6">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" 
                                   wire:model="useFloorRanges" 
                                   id="useFloorRanges">
                            <label class="form-check-label" for="useFloorRanges">
                                {{ __('Some floors have different houses')}}
                            </label>
                        </div>
                        <span class="form-text">{{ __('Check this if some floors have different house counts (e.g., floors 1-6 have 8 houses, floor 7 has 2 houses)')}}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Houses Per Floor (when same houses per floor is checked) -->
        @if($sameHousesPerFloor)
        <div class="col-md-6">
            <div class="form-group">
                <label class="form-label">{{ __('Houses Per Floor')}}</label>
                <input type="number" class="form-control" 
                       wire:model="housesPerFloor" 
                       min="1" placeholder="How many houses per floor?">
                <span class="form-text">{{ __('This number will be applied to all floors')}}</span>
            </div>
        </div>
        @endif
        
        <!-- Floor Ranges Configuration -->
        @if($useFloorRanges)
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label">{{ __('Floor Ranges Configuration')}}</label>
                <div class="alert alert-info">
                    <h6>{{ __('Example: Floors 1-6 have 8 houses each, Floor 7 has 2 houses')}}</h6>
                    <p class="mb-0">{{ __('Create ranges to specify different house counts for different floor groups')}}</p>
                </div>
                
                @foreach($floorRanges as $index => $range)
                <div class="row mb-3">
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
                        <label class="form-label">{{ __('Houses Per Floor')}}</label>
                        <input type="number" class="form-control" 
                               wire:model="floorRanges.{{ $index }}.houses_per_floor" 
                               min="1">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">&nbsp;</label>
                        @if(count($floorRanges) > 1)
                        <button type="button" class="btn btn-danger btn-sm d-block" 
                                wire:click="removeFloorRange({{ $index }})">
                            {{ __('Remove')}}
                        </button>
                        @endif
                    </div>
                </div>
                @endforeach
                
                <button type="button" class="btn btn-success btn-sm" 
                        wire:click="addFloorRange">
                    {{ __('Add Another Range')}}
                </button>
                
                <button type="button" class="btn btn-primary btn-sm ml-2" 
                        wire:click="applyFloorRanges">
                    {{ __('Apply Ranges')}}
                </button>
            </div>
        </div>
        @endif
        
        <!-- Floor Configuration -->
        @if(!empty($floorConfig) && !$sameHousesPerFloor && !$useFloorRanges)
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label">{{ __('Floor Configuration')}}</label>
                <div class="row">
                    @foreach($floorConfig as $index => $floor)
                    <div class="col-md-4 mb-3">
                        <div class="form-group">
                            <label class="form-label">{{ $floor['floor_name'] }} ({{ $floor['prefix'] }})</label>
                            <input type="number" class="form-control" 
                                   wire:model="floorConfig.{{ $index }}.houses_per_floor" 
                                   min="0" placeholder="Houses per floor">
                        </div>
                    </div>
                    @endforeach
                </div>
                <span class="form-text">{{ __('Configure how many houses/rooms to create on each floor')}}</span>
            </div>
        </div>
        @endif
        
        <!-- Floor Summary (when same houses per floor is checked) -->
        @if(!empty($floorConfig) && $sameHousesPerFloor && !$useFloorRanges)
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label">{{ __('Floor Summary')}}</label>
                <div class="alert alert-success">
                    <h6>{{ __('All floors will have the same number of houses')}}</h6>
                    <div class="row">
                        @foreach($floorConfig as $floor)
                        <div class="col-md-3 mb-2">
                            <strong>{{ $floor['floor_name'] }} ({{ $floor['prefix'] }}):</strong> {{ $floor['houses_per_floor'] }} houses
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Floor Summary (when floor ranges are used) -->
        @if(!empty($floorConfig) && $useFloorRanges)
        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label">{{ __('Floor Summary')}}</label>
                <div class="alert alert-warning">
                    <h6>{{ __('Floor ranges configuration')}}</h6>
                    <div class="row">
                        @foreach($floorConfig as $floor)
                        <div class="col-md-3 mb-2">
                            <strong>{{ $floor['floor_name'] }} ({{ $floor['prefix'] }}):</strong> {{ $floor['houses_per_floor'] }} houses
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif
        @endif

        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="phone-no">{{ __('Total Houses')}}</label>
                <input type="number" class="form-control" id="phone-no"
                       wire:model.defer="numberOfHouses">
                <span class="form-text">{{ __('No of houses to create')}}</span>
                @error('numberOfHouses')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="start-number">{{ __('Numbering From')}}</label>
                <input type="number" class="form-control" id="start-number" wire:model="startNumber">
                <span class="form-text">{{ __('Starts from number')}}</span>
                @error('startNumber')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror

            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="suffix">{{ __('Suffix')}}</label>
                <input type="text" class="form-control" id="suffix" wire:model="suffix">
                <span class="form-text">{{ __('Sets the last part of name')}}</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label" for="lead-address">{{ __('Rent')}}</label>
                <input type="text" class="form-control @error('baseRent') is-invalid @enderror" id="lead-address"
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
                        <select class="form-control @error('type') is-invalid @enderror" wire:model="baseType">
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
                    <input type="number" class="form-control @error('commission') is-invalid @enderror"
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
                    <input type="text" class="form-control @error('electricity_id') is-invalid @enderror"
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
            <h6>{{ __('Preview of house names:')}}</h6>
            <div class="row">
                @php
                    $previewNames = [];
                    switch($numberingScheme) {
                        case 'simple':
                            $previewNames = [$prefix . $startNumber . $suffix];
                            break;
                        case 'floor_based':
                            $previewNames = [];
                            foreach($floorConfig as $floor) {
                                if($floor['houses_per_floor'] > 0) {
                                    for($i = 1; $i <= min(3, $floor['houses_per_floor']); $i++) {
                                        $previewNames[] = $floor['prefix'] . $i;
                                    }
                                    if($floor['houses_per_floor'] > 3) {
                                        $previewNames[] = '...';
                                    }
                                }
                            }
                            break;
                        case 'alphabetical':
                            $alphabet = range('A', 'Z');
                            for($i = 0; $i < min(5, $numberOfHouses); $i++) {
                                $previewNames[] = $alphabet[$i % count($alphabet)] . ($i >= count($alphabet) ? floor($i / count($alphabet)) + 1 : '');
                            }
                            break;
                        case 'numeric_sequence':
                            for($i = 0; $i < min(5, $numberOfHouses); $i++) {
                                $previewNames[] = 'Unit ' . str_pad($startNumber + $i, 3, '0', STR_PAD_LEFT);
                            }
                            break;
                        case 'custom':
                            $previewNames = ['Custom Unit 1', 'Custom Unit 2', 'Custom Unit 3'];
                            break;
                    }
                @endphp
                
                @foreach($previewNames as $name)
                    <div class="col-md-3 mb-2">
                        <span class="badge badge-primary">{{ $name }}</span>
                    </div>
                @endforeach
                
                @if(count($previewNames) < $numberOfHouses && $numberingScheme !== 'floor_based')
                    <div class="col-md-3 mb-2">
                        <span class="badge badge-secondary">...</span>
                    </div>
                @endif
            </div>
        </div>

        @if(session()->has('error'))
            <div class="col-12 my-1">


                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="col-12 my-1">
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
            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                <x-button wire:click="submit" loading="{{ __('Creating...')}}"
                          class="btn-primary">{{ __('Create Multiple Houses')}}
                </x-button>
                @if($basePropertyId)
                <x-button wire:click="deleteHousesByScheme" 
                          class="btn-danger">
                    <em class="icon ni ni-trash"></em>
                    {{ __('Delete All Houses from Property')}}
                </x-button>
                @endif
            </ul>
        </div>
    </div>
</div>

<!-- Delete by Scheme Confirmation Modal -->
<div class="modal fade" tabindex="-1" id="modalDeleteByScheme" wire:ignore.self>
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Delete All Houses from Property')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <em class="icon ni ni-alert-circle"></em>
                    <strong>{{ __('Warning!')}}</strong>
                    {{ __('This action will permanently delete ALL houses from the selected property and their associated leases. This action cannot be undone.')}}
                </div>
                
                <p>{{ __('Are you sure you want to delete all houses from this property?')}}</p>
                
                <div class="mt-3">
                    <strong>{{ __('Selected Property:')}}</strong>
                    <p class="text-muted">{{ $properties[$basePropertyId] ?? 'Unknown Property' }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    {{ __('Cancel')}}
                </button>
                <button type="button" class="btn btn-danger" wire:click="confirmDeleteByScheme">
                    <em class="icon ni ni-trash"></em>
                    {{ __('Delete All Houses')}}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Listen for Livewire events
    document.addEventListener('livewire:load', function () {
        Livewire.on('showDeleteBySchemeModal', () => {
            $('#modalDeleteByScheme').modal('show');
        });
        
        Livewire.on('refreshTable', () => {
            $('#modalDeleteByScheme').modal('hide');
        });
    });
</script>
