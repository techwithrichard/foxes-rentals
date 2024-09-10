<div class="modal-dialog" role="document">
    <div class="modal-content">
        <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross"></em></a>
        <div class="modal-body modal-body-lg text-center">
            <div class="nk-modal">
                <div class="d-flex justify-content-center">
                    <div wire:loading wire:target="submit" class="spinner-border text-primary"
                         style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">{{ __('Loading')}}...</span>
                    </div>

                    <div wire:loading wire:target="notifyViaSms" class="spinner-border text-primary"
                         style="width: 3rem; height: 3rem;" role="status">
                        <span class="visually-hidden">{{ __('Sending SMS')}}...</span>
                    </div>
                </div>

                <em wire:loading.remove class="icon icon-circle icon-circle-lg ni ni-send-alt bg-info"></em>

                <h4 wire:loading.remove class="nk-modal-title">{{ __('Send Email Alert')}}</h4>
                <h4 wire:loading wire:target="submit" class="nk-modal-title">{{ __('Sending now')}}...</h4>
                <div class="nk-modal-text">
                    <div class="caption-text">{{ __('Notify tenant')}} <strong> {{ $tenant_name }}</strong>
                        {{ __('to pay pending bills amounting to')}}
                        <strong>{{setting('currency_symbol')}} {{ $balance }}</strong>
                        {{ __('for the month of')}} <strong>{{$month_year}}</strong>

                    </div>

                </div>
                <div class="nk-modal-action">

                    <x-button loading="{{ __('Sending email,please wait...') }}" wire:click="submit"
                              class="btn btn-primary">
                        {{ __('Send Mail Notification')}}
                    </x-button>

                </div>

                <div class="nk-modal-action">

                    <x-button loading="{{ __('Sending SMS,please wait...') }}" wire:click="notifyViaSms"
                              class="btn btn-info">
                        Notify Via SMS
                    </x-button>

                </div>
            </div>
        </div><!-- .modal-body -->
        <div class="modal-footer bg-lighter">
            <div class="text-center w-100">
                <p>{{ __('Already sent')}} ? <a href="#" data-bs-dismiss="modal">{{ __('Close Window')}}</a></p>
            </div>
        </div>
    </div>
</div>
