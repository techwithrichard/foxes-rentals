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
                                <h5 class="title mb-4">{{ __('Add Property Address')}}</h5>
                                <div class="row g-3">

                                    <div class="col-md-12">
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
                                    <div class="col-md-12">
                                        <div class="form-group">

                                            <div class="form-control-group">
                                                <input type="text" class="form-control" id="address2"
                                                       placeholder="{{ __('Address 2,e.g apartment,suite,floor')}}"
                                                       wire:model.defer="address2">
                                            </div>
                                        </div>
                                    </div><!-- .col -->
                                    <div class="col-md-6">
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
                                    <div class="col-md-6">
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
                                    <div class="col-md-6">
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
                                    <div class="col-md-6">
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


                                </div>
                            </div>

                        </div>

                        @if(session()->has('error'))
                            <div class="row mt-1 mb-1">
                                <div class="col-12">
                                    <div class="alert alert-danger">
                                        {{ session('message') }}
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
