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
            {{ __('Example of house name/no based on parameters provided:')}}
            <strong>{{ $prefix .''.$startNumber.''.$suffix }}</strong>
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
            </ul>
        </div>
    </div>
</div>
