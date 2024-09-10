@extends('layouts.auth_layout')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner card-inner-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">{{ __('Set Up Password')}}</h4>
                    <div class="nk-block-des">
                        <p>{{ __('Set up new password to continue')}}</p>
                    </div>
                </div>
            </div>

            @if(session('status'))
                <div class="mt-2 mb-2">
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                </div>
            @endif

            <input type="hidden" name="email" value="{{ $user->email }}"/>

            <form method="POST">
                @csrf
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="password">{{ __('Password')}}</label>
                    </div>
                    <div class="form-control-wrap">
                        <input id="password" type="password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                               name="password" required autocomplete="new-password">

                        @error('password')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror

                    </div>
                </div>


                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="confirm-password">{{ __('Confirm Password')}}</label>
                    </div>
                    <div class="form-control-wrap">
                        <input id="password-confirm" type="password" name="password_confirmation" required
                               class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                               autocomplete="new-password">

                        @error('password_confirmation')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror

                    </div>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('Save Password and Login')}}</button>
                </div>
            </form>


        </div>
    </div>
@endsection

