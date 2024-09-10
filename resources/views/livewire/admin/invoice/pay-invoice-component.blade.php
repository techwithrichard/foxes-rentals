<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <a href="#" class="close" data-bs-dismiss="modal"><em class="icon ni ni-cross-sm"></em></a>
        <div class="modal-body modal-body-md">
            <h5 class="modal-title">{{ __('Record Invoice Payment')}}</h5>

            <div class="row g-gs mt-2">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="payment-name-edit">{{ __('Payment Amount')}} <span
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
                        <label class="form-label" for="edit-status">{{ __('Payment Method')}} <span
                                class="text-danger">*</span> </label>
                        <select class="form-select" id="edit-status" wire:model="payment_method">
                            <option selected>{{ __('Payment Method')}}</option>
                            @foreach($paymentMethods as $method)
                                <option value="{{ $method }}">{{ $method }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="date">{{ __('Payment Date')}} <span
                                class="text-danger">*</span></label>
                        <x-form.form-date wire:model="paid_at" id="payment-date-component"/>
                        @error('paid_at')
                        <span class="text-danger" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="email-edit">{{ __('Reference Number')}}</label>
                        <input type="text" class="form-control" id="email-edit" wire:model.defer="reference_number">

                    </div>
                </div>

                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label" for="payment-status">{{ __('Payment Status')}} <span
                                class="text-danger">*</span> </label>
                        <select class="form-select" id="payment-status" wire:model="payment_status">
                            <option value="{{\App\Enums\PaymentStatusEnum::PENDING}}">{{__('Pending')}}</option>
                            <option value="{{\App\Enums\PaymentStatusEnum::PAID}}">{{__('Paid')}}</option>
                            <option
                                value="{{\App\Enums\PaymentStatusEnum::CANCELLED}}">{{__('Cancelled')}}</option>

                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label" for="email-edit">{{ __('Upload Payment Receipt')}}</label>
                        <x-form-pond wire:model="receipt"/>

                    </div>
                </div>


                <div class="col-12">
                    <ul class="align-center flex-wrap flex-sm-nowrap gx-4 gy-2 mt-3">
                        <li>
                            <x-button wire:click="submit" loading="{{__('Recording ...')}}" class="btn btn-primary">
                                {{ __('Record Payment')}}
                            </x-button>
                        </li>
                        <li>
                            <a href="#" class="link" data-bs-dismiss="modal">{{ __('Cancel')}}</a>
                        </li>
                    </ul>
                </div>
            </div>

        </div><!-- .modal-body -->
    </div><!-- .modal-content -->
</div>
