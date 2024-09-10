<div class="modal-dialog" role="document">
    <div class="modal-content">
        <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross"></em></a>
        <div class="modal-body modal-body-lg text-center">
            <div class="nk-modal">
                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-check bg-success"></em>
                <h4 class="nk-modal-title">{{ __('Approve Payment!')}}</h4>
                <div class="nk-modal-text">
                    <div class="caption-text">
                        {{ __('Confirm that you have received the payment for this invoice and its ready to be marked as paid.')}}
                    </div>

                </div>
                <div class="nk-modal-action">
                    <button wire:click="submit" type="button" class="btn btn-lg btn-mw btn-primary">
                        {{ __('OK')}}
                    </button>
                </div>
            </div>
        </div><!-- .modal-body -->

    </div>
</div>
