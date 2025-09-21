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
                                <h5 class="title mb-4">{{ __('Property Details')}}</h5>
                                <div class="row g-3">
                                    <div class="col-sm-6">
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
                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <div class="form-label-group">
                                                <label class="form-label">{{ __('Property Type')}}<span
                                                        class="text-danger">*</span></label>
                                            </div>
                                            <div class="form-control-wrap">
                                                <div class="form-control-select">
                                                    <select class="form-control @error('type') is-invalid @enderror"
                                                            id="propertyType" wire:model="type"
                                                            data-placeholder="{{ __('Select property type')}}">
                                                        <option label="Select type"></option>
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

                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label" for="default-06">Select Landlord</label>
                                            <div class="form-control-wrap ">
                                                <div class="form-control-select">
                                                    <select class="form-control @error('landlord') is-invalid @enderror"
                                                            id="default-06" wire:model="landlord">
                                                        <option value="">Select Landlord</option>
                                                        @foreach($landlords as $landlord)
                                                            <option value="{{ $landlord->id }}">{{ $landlord->name }}
                                                                <span> ({{$landlord->email}})</span></option>
                                                        @endforeach
                                                    </select>

                                                    @error('landlord')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label class="form-label"
                                                   for="cp1-profile-description">{{__('Description')}}</label>
                                            <div class="form-control-wrap">
                                                <textarea class="form-control form-control-sm valid"
                                                          wire:model.defer="description"
                                                          id="cp1-profile-description" name="cp1-profile-description"

                                                          required="">

                                                </textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label class="form-label">{{ __('Property Type')}}</label>
                                            <div class="form-control-wrap">
                                                <ul class="custom-control-group">
                                                    <li>
                                                        <div class="custom-control custom-radio">
                                                            <input type="radio" class="custom-control-input"
                                                                   name="cp1-project-type" id="cp1-public-project"
                                                                   wire:model.defer="is_single_unit"
                                                                   value="1">
                                                            <label class="custom-control-label"
                                                                   for="cp1-public-project">{{ __('Single Unit Property')}}</label>
                                                        </div>
                                                    </li>
                                                    <li>
                                                        <div class="custom-control custom-radio checked">
                                                            <input type="radio" class="custom-control-input"
                                                                   name="cp1-project-type" id="cp1-private-project"
                                                                   wire:model.defer="is_single_unit"
                                                                   value="0" required="">
                                                            <label class="custom-control-label"
                                                                   for="cp1-private-project">{{ __('Multi Unit Property')}}</label>
                                                        </div>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                        <ul class="nk-stepper-pagination pt-4 gx-4 gy-2">

                            <li class="" style="display: block;">
                                <x-button wire:click="submit" loading="{{ __('Validating...')}}"
                                          class="btn btn-primary">
                                    {{ __('Continue')}}  <em class="dd-indc icon ni ni-chevron-right"></em>
                                </x-button>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
