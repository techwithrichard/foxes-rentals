<div>
    <div class="row gy-4">
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label">{{ __('Parent Property')}}</label>
                <div class="form-control-wrap">
                    <div class="form-control-select">
                        <select class="form-control @error('property_id') is-invalid @enderror"
                                wire:model="property_id">
                            <option value="">{{ __('Select property')}}</option>
                            @foreach($properties as $key=>$value)
                                <option value="{{ $key }}">{{ $value }}</option>
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
                <label class="form-label" for="lead-name">{{ __('House Name/Number')}}</label>
                <div class="form-control-wrap">
                    <input type="text" class="form-control @error('name') is-invalid @enderror" wire:model.defer="name"
                           id="lead-name">
                    @error('name')
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
        <div class="col-md-4">
            <div class="form-group">
                <label class="form-label">{{ __('House Type')}}</label>
                <div class="form-control-wrap">
                    <div class="form-control-select">
                        <select class="form-control @error('type') is-invalid @enderror" wire:model="type">
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
                <label class="form-label">{{ __('House Status')}}</label>
                <div class="form-control-wrap">
                    <div class="form-control-select">
                        <select class="form-control @error('house_status') is-invalid @enderror"
                                wire:model="house_status">
                            <option value="">{{ __('House Status')}}</option>
                            <option value="{{ \App\Enums\HouseStatusEnum::VACANT->value }}">{{ __('Vacant')}}</option>
                            <option value="{{ \App\Enums\HouseStatusEnum::UNDER_MAINTENANCE->value }}">
                                {{ __('Under Maintenance')}}
                            </option>

                        </select>

                        @error('house_status')
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
                <label class="form-label" for="lead-name">{{ __('Rent')}}</label>
                <div class="form-control-wrap">
                    <input type="text" class="form-control @error('rent') is-invalid @enderror" wire:model.defer="rent"
                           id="lead-name">
                    @error('rent')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                    @enderror
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



        <div class="col-md-12">
            <div class="form-group">
                <label class="form-label" for="close-deal">{{ __('Description')}}</label>
                <input type="text" class="form-control" id="close-deal" wire:model.defer="description">
            </div>
        </div>

        <div class="col-12">
            <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                <x-button wire:click="submit" loading="{{ __('Creating...')}}"
                          class=" btn-primary">{{ __('Create House')}}
                </x-button>
            </ul>
        </div>
    </div>
</div>
