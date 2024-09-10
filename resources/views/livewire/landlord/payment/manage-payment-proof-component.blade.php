<div class="card card-bordered">
    <div class="card-inner">
        <div class="card-head">
            <h5 class="card-title">{{ __('Approval Settings')}}</h5>
        </div>
        <form action="#" class="gy-3">

            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label">{{ __('Update Status')}}</label>
                        <span class="form-note">{{ __('Approve or reject proof')}}.</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <ul class="custom-control-group g-3 align-center flex-wrap">
                        <li>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input"
                                       name="reg-public" id="reg-enable"
                                       wire:model="status"
                                       value="{{\App\Enums\PaymentProofStatusEnum::APPROVED}}">
                                <label class="custom-control-label" for="reg-enable">{{ __('Approve')}}</label>
                            </div>
                        </li>
                        <li>
                            <div class="custom-control custom-radio">
                                <input type="radio" class="custom-control-input"
                                       wire:model="status"
                                       name="reg-public" id="reg-disable"
                                       value="{{ \App\Enums\PaymentProofStatusEnum::REJECTED }}">

                                <label class="custom-control-label" for="reg-disable">{{ __('Reject')}}</label>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>

            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label" for="site-off">{{ __('Record Payment')}}</label>
                        <span class="form-note">{{ __('Save this payment proof in payments data.')}}</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" name="reg-public" id="site-off"
                                   wire:model="recordPayment"
                                   value="true">

                            <label class="custom-control-label" for="site-off">{{ __('Save Payment')}}</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row g-3 align-center">
                <div class="col-lg-5">
                    <div class="form-group">
                        <label class="form-label">{{ __('Remarks')}}</label>
                        <span class="form-note">{{ __('Specify why the proof was either rejected or approved.')}}</span>
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="form-group">
                        <div class="form-control-wrap">
                            <input type="text" class="form-control @error('remarks') is-invalid @enderror"
                                   name="site-url" wire:model.defer="remarks">
                        </div>
                    </div>
                </div>
            </div>

            @if($errors->any())
                <div class="row col-12">
                    <div class="alert alert-danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>

            @endif

            <div class="row g-3">
                <div class="col-lg-7 offset-lg-5">
                    <div class="form-group mt-2">
                        <x-form.submit>{{ __('Submit Updates')}}</x-form.submit>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
