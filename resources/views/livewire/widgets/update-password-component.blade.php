<div class="modal-body">

        <div class="form-group">
            <label class="form-label" for="full-name">{{ __('Current Password')}}</label>
            <div class="form-control-wrap">
                <input type="password" wire:model.defer="current_password"
                       class="form-control @error('current_password') is-invalid @enderror" id="full-name">
                @error('current_password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror

            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="email-address">{{ __('New Password')}}</label>
            <div class="form-control-wrap">
                <input type="password" wire:model.defer="password"
                       class="form-control @error('password') is-invalid @enderror" id="email-address">
                @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
        <div class="form-group">
            <label class="form-label" for="phone-no">{{ __('Confirm Password')}}</label>
            <div class="form-control-wrap">
                <input type="password" wire:model.defer="password_confirmation" class="form-control" id="phone-no">
            </div>
        </div>
        <div class="form-group">
            <x-button loading="{{__('Updating...')}}" wire:click="submit" class="btn btn-primary">
            {{__('Update Password')}}
            </x-button>
        </div>

</div>
