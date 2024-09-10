<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-body modal-body-lg text-center">
            <div class="nk-modal">
                <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                <h4 class="nk-modal-title">{{__('Confirm House Deletion!')}}</h4>
                <div class="nk-modal-text">
                    <p class="text-soft">
                        {{ __('Proceed with caution.Delete a house that is already leased to a tenant will result to the
                        termination of the lease agreement. Lease history associated with this house will be cleared as
                        well.')}}
                        .</p>
                    <p class="text-soft">{{ __('If you really want to delete the house,proceed.')}}</p>
                </div>
                <div class="d-flex justify-content-center align-items-center mt-5">
                    <a href="#" class="btn btn-lg btn-mw btn-light me-3" data-bs-dismiss="modal">{{ __('Cancel')}}</a>
                    <x-button loading="{{ __('Deleting...') }}"
                              wire:click="submit"
                              class="btn btn-lg btn-mw btn-danger">
                        {{ __('Delete House') }}
                    </x-button>
                </div>

            </div>
        </div><!-- .modal-body -->
    </div>
</div>
