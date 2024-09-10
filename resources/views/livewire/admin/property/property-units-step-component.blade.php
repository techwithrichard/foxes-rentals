<div>
    <div class="card card-bordered">
        <div class="row g-0 col-sep col-sep-md col-sep-xl">
            <div class="col-md-4 col-xl-4">
                <div class="card-inner">
                    <ul class="nk-stepper-nav nk-stepper-nav-s1 stepper-nav is-vr">
                        @foreach($steps as $step)
                            <li class="{{ $step->isPrevious()?"done":"" }} {{ $step->isCurrent()?"current":"" }}">
                                <div class="step-item">
                                    <div class="step-text">
                                        <div class="lead-text">{{ $step->label }}</div>
                                        <div class="sub-text">{{ $step->description }}</div>
                                    </div>
                                </div>
                            </li>
                        @endforeach


                    </ul>
                </div>
            </div>
            <div class="col-md-8 col-xl-8">
                <div class="card-inner">
                    <div class="">
                        <div class="nk-stepper-steps stepper-steps">
                            <div class="nk-stepper-step active">
                                <h5 class="title mb-2">{{ __('Property Units')}}</h5>
                                <p class="">{{ __('Units can be added later onto the property')}}</p>

                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="email">Prefix</label>
                                            <input type="text"
                                                   class="form-control @error('prefix') is-invalid @enderror" id="email"
                                                   wire:model="prefix">
                                            <span class="form-text">Sets the first part of name</span>
                                            @error('prefix')
                                            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="phone-no">Total Houses</label>
                                            <input type="number" class="form-control" id="phone-no"
                                                   wire:model.defer="numberOfHouses">
                                            <span class="form-text">No of houses to create</span>
                                            @error('numberOfHouses')
                                            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="start-number">Numbering From</label>
                                            <input type="number" class="form-control" id="start-number"
                                                   wire:model="startNumber">
                                            <span class="form-text">Starts from number</span>
                                            @error('startNumber')
                                            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                            @enderror

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="suffix">Suffix</label>
                                            <input type="text" class="form-control" id="suffix" wire:model="suffix"
                                                   placeholder="Address">
                                            <span class="form-text">Sets the last part of name</span>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label" for="lead-address">Rent</label>
                                            <input type="text"
                                                   class="form-control @error('baseRent') is-invalid @enderror"
                                                   id="lead-address"
                                                   wire:model.defer="baseRent">
                                            <span class="form-text">Defines base rent for all units</span>
                                            @error('baseRent')
                                            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label class="form-label">House Type</label>
                                            <div class="form-control-wrap">
                                                <div class="form-control-select">
                                                    <select class="form-control @error('type') is-invalid @enderror"
                                                            wire:model="baseType">
                                                        <option value="">House type</option>
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


                                    <div class="col-12">
                                        <div class="alert alert-pro alert-primary">
                                            <div class="alert-text">
                                                <p> Example of house name/no based on parameters provided:
                                                    <strong>{{ $prefix .''.$startNumber.''.$suffix }}</strong> </p>
                                            </div>
                                        </div>
                                    </div>



                                </div>


                            </div>

                        </div>

                        @if(session()->has('error'))
                            <div class="row mt-1 mb-1">
                                <div class="col-12">
                                    <div class="alert alert-danger">
                                        {{ session('error') }}
                                    </div>

                                </div>
                            </div>
                        @endif


                        <ul class="nk-stepper-pagination pt-4 gx-4 gy-2">

                            <li class="" style="display: block;">
                                <button wire:click="previousStep"
                                        class="btn btn-dim btn-primary">
                                    <em class="dd-indc icon ni ni-chevron-left"></em>
                                    {{ __('Back')}}</button>
                            </li>

                            <li class="" style="display: block;">


                                <x-form.submit>{{ __('Create Property With Units')}}</x-form.submit>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
