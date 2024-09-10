<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
        <div class="modal-body modal-body-md">
            <h5 class="modal-title">{{ __('Record Deposit Refund')}}</h5>
            <div class="mt-2">
                <div class="row g-gs">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="payment-name-edit">{{ __('Refunded Amount')}} <span
                                    class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('amount') is-invalid @enderror"
                                   id="payment-name-edit" wire:model.defer="amount">
                            @error('amount')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>


                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="date">{{ __('Refund Date')}} <span class="text-danger">*</span></label>
                            <x-form.form-date wire:model="refund_date"/>
                            @error('refund_date')
                            <span class="text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label" for="email-edit">{{ __('Refund Payment Receipt')}}</label>
                            <x-form-pond wire:model="refund_receipt"/>
                        </div>
                    </div>


                    <div class="col-12">
                        <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2 mt-3">
                            <li>
                                <x-button wire:click="submit" loading="{{ __('Refunding,please wait...') }}"
                                          class="btn btn-primary">{{ __('Refund Deposit')}}
                                </x-button>

                            </li>
                            <li>
                                <a href="#" class="link" data-bs-dismiss="modal">{{ __('Cancel')}}</a>
                            </li>
                        </ul>
                    </div>
                </div>
</div>
        </div><!-- .modal-body -->
    </div><!-- .modal-content -->
</div>
