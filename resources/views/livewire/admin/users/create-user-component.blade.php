<div>
    <div class="nk-block">
        <div class="card card-bordered">
            <div class="card-inner-group">
                <div class="card-inner">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h5 class="title nk-block-title">{{ __('Personal Info')}}</h5>
                            <p>{{ __('Add common information like Name, Email etc')}} </p>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="row gy-4">
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="full-name">{{ __('Full Name')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                                               id="full-name" wire:model.defer="name">
                                        @error('name')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="email">{{ __('Email Address')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                                               id="email" wire:model.defer="email">
                                        @error('email')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="phone">{{ __('Phone Number')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                               id="phone" wire:model.defer="phone">
                                        @error('phone')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-md-4">
                                    <div class="form-group">
                                        <label class="form-label" for="tnx-account">{{ __('Select Role')}}
                                            <x-form.required/>
                                        </label>
                                        <div class="form-control-wrap">
                                            <div class="form-control-select">


                                                <select class="form-control" id="user-roles" wire:model="role">
                                                    <option label="Select role"/>

                                                    @forelse($roles as $role)
                                                        <option value="{{ $role->id }}">{{ Str::ucfirst($role->name) }}</option>
                                                    @empty

                                                    @endforelse

                                                </select>
                                            </div>
                                        </div>

                                        @error('role')
                                        <p class="text-danger fs-12px"> {{ $message }}</p>
                                        @enderror

                                    </div>
                            </div>

                        </div>
                        <!--row-->
                    </div>
                </div><!-- .card-inner -->

                <div class="card-inner">
                    <div class="nk-block">
                        <div class="row">
                            <!--Show all error messages if there is any validation errors in livewire component-->
                            @if ($errors->any())
                                <div class="alert alert-danger mt-2 mb-2">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session()->has('error'))
                                <div class="alert alert-danger mt-2 mb-2">
                                    {{ session('error') }}
                                </div>

                            @endif


                            <div class="col-12 mt-4">
                                <div class="float-end">
                                    <x-button wire:click="submit" loading="{{__('Creating...')}}" class="btn-primary">
                                        {{ __('Create User') }}
                                    </x-button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- .card-inner -->
            </div>
        </div><!-- .card -->
    </div><!-- .nk-block -->
</div>
