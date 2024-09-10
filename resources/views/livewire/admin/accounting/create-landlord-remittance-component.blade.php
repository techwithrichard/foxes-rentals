<div class="nk-block mt-0">
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="row gy-4">

                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="last-name">{{ __('Landlord')}}</label>
                        <div class="form-control-select">
                            <select class="form-control" wire:model="landlord">
                                <option value="">{{ __('Select Landlord')}}</option>
                                @foreach($landlords as $user)
                                    <option value="{{ $user->id }}">{{$user->name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label">{{ __('Period From')}}</label>
                        <x-form.form-date
                            wire:model="period_from" id="period-from"
                            placeholder="{{ __('Start Collection Period')}}"/>


                        @error('period_from')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>

                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label">{{ __('Period To')}}</label>
                        <x-form.form-date
                            wire:model="period_to" id="period-to"
                            placeholder="{{ __('End Collection Period')}}"/>


                        @error('period_to')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>

                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label">{{ __('Paid on')}}</label>
                        <x-form.form-date wire:model="paid_on" id="paid-on"/>

                        @error('paid_on')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>
                </div>

                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="phone-no">{{ __('Amount')}}</label>
                        <input type="number" class="form-control @error('amount') is-invalid @enderror" id="phone-no"
                               wire:model.defer="amount">
                        @error('amount')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="phone-no">{{ __('Payment Method')}}</label>
                        <div class="form-control-select">
                            <select class="form-control" wire:model="payment_method">
                                <option value="">{{ __('Select Payment Method')}}</option>
                                @foreach($payment_methods as $method)
                                    <option value="{{ $method }}">{{$method}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="phone-no">{{ __('Payment Reference')}}</label>
                        <input type="text" class="form-control @error('payment_reference') is-invalid @enderror"
                               id="phone-no"
                               wire:model.defer="payment_reference">
                        @error('payment_reference')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-md-12 col-lg-12 col-xxl-12">
                    <div class="form-group">
                        <label class="form-label" for="address">{{ __('Remarks')}}</label>
                        <input type="text" class="form-control" id="address" wire:model.defer="remarks">
                    </div>
                </div>
                <!--col-->
                <div class="col-md-12 col-lg-12 col-xxl-12">
                    <div class="form-group">
                        <label class="form-label">{{ __('Upload Receipt')}}</label>
                        <x-form.filepond wire:model="attachment"/>
                        @error('attachment')
                        <div class="text-danger">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-12">
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>

                    @endif
                </div>
                <!--col-->
                <div class="col-sm-12">
                    <x-button wire:click="submit" loading="{{__('creating...')}}" class="btn-primary">
                        {{ __('Add Remittance')}}
                    </x-button>
                </div>
                <!--col-->
            </div>
            <!--row-->
        </div><!-- .card-inner-group -->
    </div><!-- .card -->
</div><!-- .nk-block -->
