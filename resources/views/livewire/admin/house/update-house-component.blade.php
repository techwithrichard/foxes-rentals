<div>

    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h5 class="title nk-block-title">{{ __('Update House Details')}}</h5>
                            <p>{{ __('Update important details for the house.Updating rent will not affect the current
                                lease.Adjusting rent on the lease should be done in when updating lease details
                                section.')}} </p>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="row gy-4">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="lead-name">{{ __('House Name/Number')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               wire:model.defer="name"
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
                                    <label class="form-label">{{ __('House Type')}}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control @error('type') is-invalid @enderror"
                                                    wire:model="type">
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
                                    <label class="form-label">{{ __('Status')}}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control @error('status') is-invalid @enderror"
                                                    wire:model="status">
                                                <option value="">{{ __('Status')}}</option>
                                                <option
                                                    value="{{ \App\Enums\HouseStatusEnum::VACANT }}">{{ __('Vacant')}}</option>
                                                <option
                                                    value="{{ \App\Enums\HouseStatusEnum::OCCUPIED }}">{{ __('Occupied')}}</option>
                                                <option value="{{ \App\Enums\HouseStatusEnum::UNDER_MAINTENANCE }}">
                                                    {{ __('Under Maintenance')}}</option>

                                            </select>

                                            @error('status')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label" for="lead-name">{{ __('Rent')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control @error('rent') is-invalid @enderror"
                                               wire:model.defer="rent"
                                               id="lead-name">
                                        @error('rent')
                                        <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
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
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Landlord')}}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control @error('landlord') is-invalid @enderror"
                                                    wire:model="landlord">
                                                <option value="">{{ __('Select Landlord')}}</option>
                                                @foreach($landlords as $landlord)
                                                    <option value="{{ $landlord->id }}">{{ $landlord->name }}
                                                        ({{ $landlord->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('landlord')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="close-deal">{{ __('Description')}}</label>
                                    <input type="text" class="form-control" id="close-deal"
                                           wire:model.defer="description">
                                </div>
                            </div>

                            <div class="col-12">
                                <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2">
                                    <x-button wire:click="submit" loading="{{ __('Updating...')}}"
                                              class="btn-primary">{{ __('Update House')}}
                                    </x-button>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div><!-- .card-inner -->

            </div>
        </div><!-- .card -->
    </div><!-- .nk-block -->


</div>
