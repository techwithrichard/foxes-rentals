<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body modal-body-lg text-center">
            <div class="nk-modal">
                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                <h4 class="nk-modal-title">{{ __('Reject Payment!')}}</h4>
                <div class="nk-modal-text">
                    <p class="lead">
                        {{ __('Confirm that you want to reject this payment.')}}
                    </p>
                </div>
                <div class="nk-modal-action mt-5">
                    <a href="#" class="btn btn-lg btn-mw btn-light" data-bs-dismiss="modal">{{ __('Close')}}</a>
                    <button type="button"
                            wire:click="submit"
                            class="btn btn-lg btn-mw btn-danger">{{ __('Proceed')}}</button>
                </div>
            </div>
        </div><!-- .modal-body -->
    </div>
</div>
