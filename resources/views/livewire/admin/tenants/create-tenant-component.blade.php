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
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text" id="basic-addon1">
                                                    254
                                                </span>
                                            </div>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                                   placeholder="701234567"
                                                   id="phone" wire:model.defer="phone">
                                            @error('phone')
                                            <span class="invalid-feedback">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="phone">{{ __('Occupation Status')}}</label>
                                    <div class="form-control-wrap">
                                        <div class="form-control-select">
                                            <select class="form-control" id="occupation_status"
                                                    wire:model.defer="occupation_status">
                                                <option value="">{{ __('Occupation Status')}}</option>
                                                <option value="Employed">{{ __('Employed')}}</option>
                                                <option value="Self Employed">{{ __('Self Employed')}}</option>
                                                <option value="Unemployed">{{ __('Unemployed')}}</option>
                                                <option value="Student">{{ __('Student')}}</option>
                                                <option value="Retired">{{ __('Retired')}}</option>
                                                <option value="Other">{{ __('Other')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--col-->
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="identity_no">{{ __('Occupation Place')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                               class="form-control @error('occupation_place') is-invalid @enderror"
                                               id="occupation_place" wire:model.defer="occupation_place">
                                        @error('occupation_place')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label" for="identity_no">{{ __('Identity Number')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                               class="form-control @error('identity_no') is-invalid @enderror"
                                               id="identity_no" wire:model.defer="identity_no">
                                        @error('identity_no')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <!--col-->

                            <!--col-->

                            <!--col-->
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Identity Document')}}</label>
                                    <div class="form-control-wrap">

                                        <input type="file"
                                               class="form-control @error('identity_document') is-invalid @enderror"
                                               id="nid" wire:model.defer="identity_document">

                                        @error('identity_document')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror

                                    </div>
                                </div>
                            </div>
                            <!--col-->
                            <div class="col-xxl-5 col-md-8">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Address')}}</label>
                                    <div class="form-control-wrap">
                                        <input type="text"
                                               class="form-control @error('address') is-invalid @enderror"
                                               id="nid" wire:model="address">
                                        @error('address')
                                        <span class="invalid-feedback">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <!--col-->
                        </div>
                        <!--row-->
                    </div>
                </div><!-- .card-inner -->
                <div class="card-inner">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h5 class="title nk-block-title">{{ __('Emergency Contact')}}</h5>
                            <p>{{ __('Who to contact incase of emergency.')}} </p>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="row gy-4">
                            <!--col-->
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Contact Name')}}</label>
                                    <input type="text" class="form-control" id="height"
                                           wire:model.defer="emergency_name">
                                </div>
                            </div>
                            <!--col-->
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Contact Email')}}</label>
                                    <input type="email"
                                           class="form-control  @error('emergency_email') is-invalid @enderror"
                                           id="weight"
                                           wire:model.defer="emergency_email">
                                    @error('emergency_email')
                                    <span class="invalid-feedback">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                            <!--col-->
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Contact Phone Number')}}</label>
                                    <input type="text" class="form-control" id="bp"
                                           wire:model.defer="emergency_contact">
                                </div>
                            </div>
                            <!--col-->
                            <div class="col-xxl-6 col-md-8">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Relationship With Tenant')}}</label>
                                    <input type="text" class="form-control" id="pulse"
                                           wire:model.defer="emergency_relationship">
                                </div>
                            </div>
                            <!--col-->

                        </div>
                        <!--row-->
                    </div>
                </div><!-- .card-inner -->


                <div class="card-inner">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h5 class="title nk-block-title">{{ __('Next Of Kin')}}</h5>
                            <p>{{ __('Details of tenant\'s next of kin.')}} </p>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="row gy-4">
                            <!--col-->
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Kin Name')}}</label>
                                    <input type="text" class="form-control" id="height"
                                           wire:model.defer="kin_name">
                                </div>
                            </div>
                            <!--col-->
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Kin Phone Number')}}</label>
                                    <input type="tel" class="form-control" id="weight"
                                           wire:model.defer="kin_phone">
                                </div>
                            </div>
                            <!--col-->
                            <div class="col-xxl-3 col-md-4">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Kin Identity Number')}}</label>
                                    <input type="text" class="form-control" id="bp"
                                           wire:model.defer="kin_identity">
                                </div>
                            </div>
                            <!--col-->
                            <div class="col-xxl-6 col-md-8">
                                <div class="form-group">
                                    <label class="form-label">{{ __('Relationship With Tenant')}}</label>
                                    <input type="text" class="form-control" id="pulse"
                                           wire:model.defer="kin_relationship">
                                </div>
                            </div>
                            <!--col-->

                        </div>
                        <!--row-->
                    </div>
                </div><!-- .card-inner -->

                <div class="card-inner">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h5 class="title nk-block-title">{{ __('Identification Documents')}}</h5>
                            <p>{{ __('Upload any other identification documents identifying a tenant,like passport,drivers license among others')}} </p>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="row gy-4">
                            <!--col-->
                            <div class="col-xxl-12 col-md-12">
                                <div class="form-group">
                                    <x-form-pond wire:model="identity_documents" multiple="true"/>

                                    <div class="form-text">
                                        {{ __(' For better file identification,rename the file to the actual identity document name,for example,if the document is a passport,then rename the file to passport.jpg')}}

                                    </div>

                                </div>
                            </div>


                        </div>
                        <!--row-->
                    </div>
                </div><!-- .card-inner -->

                <div class="card-inner">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <h5 class="title nk-block-title">{{ __('Welcome Email Notification')}}</h5>
                            <p>{{ __('Define whether tenants should receive welcome notification to set up their password')}} </p>
                        </div>
                    </div>
                    <div class="nk-block">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="custom-control custom-checkbox ml-3">
                                    <input type="checkbox" value="true" class="custom-control-input" id="customCheck1"
                                           wire:model="shouldSendWelcomeEmail">
                                    <label class="custom-control-label" for="customCheck1">
                                        {{ __('Send welcome emails to tenants to set up password ?')}}
                                    </label>
                                </div>
                            </div>
                            <!--Show all error messages if there is any validation errors in livewire component-->
                            @if ($errors->any())
                                <div class="alert alert-danger my-2">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            @if(session()->has('error'))
                                <div class="alert alert-danger my-2">
                                    {{ session('error') }}
                                </div>

                            @endif


                            <div class="col-12 mt-4">
                                <div class="float-end">
                                    <x-button wire:click="submit" loading="{{__('Saving...')}}" class="btn btn-primary">
                                        {{ __('Save Tenant') }}
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
