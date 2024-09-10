<div class="card-inner card-inner-lg">
    <div class="nk-block-head nk-block-head-lg">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">{{ __('Company Details')}}</h5>
                <span>{{ __('Update company details that will be used in invoices and vouchers.')}}</span>
            </div><!-- .nk-block-head-content -->
            <div class="nk-block-head-content align-self-start d-lg-none">
                <a href="#" class="toggle btn btn-icon btn-trigger mt-n1"
                   data-target="userAside"><em class="icon ni ni-menu-alt-r"></em></a>
            </div>
        </div>
    </div><!-- .nk-block-head -->
    <div class="nk-block">
        <div class="gy-3 form-settings">
            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="site-name">{{ __('Company Name')}}</label>
                        <span class="form-note">{{ __('Specify the name of your company.')}}</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" class="form-control @error('company_name') is-invalid @enderror"
                                   id="site-name" wire:model="company_name">

                            @error('company_name')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="site-name">{{ __('Company Phone')}}</label>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" class="form-control @error('company_phone') is-invalid  @enderror"
                                   id="site-name" wire:model="company_phone">
                            @error('company_phone')
                            <div class="text-danger">
                                {{ $message }}

                            </div>

                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="site-name">{{ __('Company Email')}}</label>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="site-name" wire:model="company_email">

                            @error('company_email')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="site-address">{{ __('Company Address')}}</label>
                        <span
                            class="form-note">{{ __('Specify the name of your company address.')}}</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="site-address" wire:model="company_address">

                            @error('company_address')
                            <div class="text-danger">
                                {{ $message }}
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="form-group mt-2">
                        <x-button loading="{{__('Updating...')}}" wire:click="submit" class="btn btn-primary">
                            {{ __('Update Company Details')}}
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
