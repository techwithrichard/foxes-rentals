<div class="nk-block mt-0">
    <div class="card card-bordered">
        <div class="card-inner">
            <div class="row gy-4">
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="first-name">{{ __('Expense Name')}}</label>
                        <input type="text" class="form-control @error('expense_name') is-invalid @enderror"
                               id="first-name" wire:model.defer="expense_name">

                        @error('expense_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>
                </div>
                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="last-name">{{ __('Expense Type')}}</label>
                        <div class="form-control-select">
                            <select class="form-control" wire:model="expense_type_id">
                                <option value="">{{ __('Select Expense Type')}}</option>
                                @foreach($categories as $key=>$value)
                                    <option value="{{ $key }}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="last-name">{{ __('Associate Landlord')}}</label>
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
                        <label class="form-label" for="last-name">{{ __('Associate Property')}}</label>
                        <div class="form-control-select">
                            <select class="form-control" wire:model="building">
                                <option value="">{{ __('Select Property')}}</option>
                                @foreach($landlord_properties as $index=>$property)
                                    <option value="{{ $property['id'] }}">{{$property['name']}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="last-name">{{ __('Associate Unit')}}</label>
                        <div class="form-control-select">
                            <select class="form-control" wire:model="unit">
                                <option value="">{{ __('Select Property')}}</option>
                                @foreach($houses as $key=>$value)
                                    <option value="{{ $key }}">{{$value}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <!--col-->
                <div class="col-md-6 col-lg-4 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label">{{ __('Incurred on')}}</label>
                        <x-form.form-date wire:model="incurred_on"/>

                        @error('incurred_on')
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

                <div class="col-md-6 col-lg-8 col-xxl-3">
                    <div class="form-group">
                        <label class="form-label" for="address">{{ __('Extra notes')}}</label>
                        <input type="text" class="form-control" id="address" wire:model.defer="notes">
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
                    <x-button wire:click="save" loading="{{__('updating...')}}" class="btn-primary">
                        {{ __('Update Expense')}}
                    </x-button>
                </div>
                <!--col-->
            </div>
            <!--row-->
        </div><!-- .card-inner-group -->
    </div><!-- .card -->
</div><!-- .nk-block -->

