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
                                <h5 class="title mb-4">{{ __('Extra Property Details')}}</h5>
                                <div class="row g-3">
                                    <div class="col-sm-6">
                                        <div class="form-group">

                                            <label class="form-label" for="pname">{{ __('Property Rent')}}</label>


                                            <input type="number"
                                                   class="form-control @error('rent') is-invalid @enderror "
                                                   id="pname" wire:model.defer="rent">
                                            <div
                                                class="form-text">{{ __('Required when property is single unit')}}</div>

                                            @error('rent')
                                            <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                            @enderror
                                        </div>

                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">
                                            <label class="form-label" for="commission">Commission</label>
                                            <div class="form-control-wrap">
                                                <div class="input-group">
                                                    <input type="number" wire:model="commission"
                                                           class="form-control @error('commission') is-invalid @enderror">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="commission">%</span>
                                                    </div>

                                                    @error('commission')
                                                    <span class="invalid-feedback">
                                                        {{ $message }}
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-sm-6">
                                        <div class="form-group">

                                            <label class="form-label" for="pname">{{ __('Electricity ID')}}
                                            </label>


                                            <input type="number"
                                                   class="form-control @error('electricity_id') is-invalid @enderror "
                                                   id="pname" wire:model.defer="electricity_id">

                                            @error('electricity_id')
                                            <span class="invalid-feedback">
                                        {{ $message }}
                                    </span>
                                            @enderror
                                        </div>
                                    </div>


                                </div>
                            </div>

                        </div>
                        <ul class="nk-stepper-pagination pt-4 gx-4 gy-2">

                            <li class="" style="display: block;">
                                <button wire:click="previousStep"
                                        class="btn btn-dim btn-primary">

                                    <em class="dd-indc icon ni ni-chevron-left"></em> {{ __('Back')}}</button>
                            </li>

                            <li class="" style="display: block;">
                                <x-button wire:click="submit" loading="{{ __('Validating...')}}"
                                          class="btn btn-primary">
                                    {{ __('Continue')}} <em class="dd-indc icon ni ni-chevron-right"></em>
                                </x-button>
                            </li>

                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
