<div class="card-inner card-inner-lg">
    <div class="nk-block-head nk-block-head-lg">
        <div class="nk-block-between">
            <div class="nk-block-head-content">
                <h5 class="nk-block-title">{{ __('General settings')}}</h5>
                <span>{{ __('These settings helps you modify application settings.')}}</span>
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
                            <input type="text" class="form-control" id="site-name"
                                   wire:model.defer="app_name">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="site-address">{{ __('Currency Name')}}</label>
                        <span class="form-note">{{ __('Name of your currency,e.g US Dollar')}}</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="site-address"
                                   wire:model.defer="currency_name">
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="site-copyright">{{ __('Currency Symbol')}}</label>
                        <span
                            class="form-note">{{ __('Symbol of default currency,e.g $')}}</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" class="form-control" id="site-copyright"
                                   wire:model.defer="currency_symbol">
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="form-group mt-2">

                        <x-button class="btn btn-primary" type="button" loading="{{__('Saving ...')}}" wire:click="submit">
                            {{__('Update Settings')}}
                        </x-button>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- .nk-block-head -->
</div>
