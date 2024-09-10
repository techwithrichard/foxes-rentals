@extends('layouts.auth_layout')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner card-inner-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">{{ __('Reset password')}}</h4>
                </div>
            </div>

            @if(session('status'))
                <div class="mt-2 mb-2">
                    <div class="alert alert-success">
                        {{ session('status') }}
                    </div>
                </div>
            @endif


            <form method="POST" action="{{ route('password.update') }}">
                @csrf


                <input type="hidden" name="token" value="{{ $request->route('token') }}">


                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="default-01">{{ __('Email')}}</label>
                    </div>
                    <div class="form-control-wrap">
                        <input id="email" type="email"
                               class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                               value="{{ old('email', $request->email) }}" required autocomplete="email" autofocus>

                        @error('email')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror

                    </div>
                </div>
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="password">{{ __('Password')}}</label>

                    </div>
                    <div class="form-control-wrap">
                        <a href="#" class="form-icon form-icon-right passcode-switch lg"
                           data-target="password">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>


                        <input id="password" type="password"
                               class="form-control form-control-lg @error('password') is-invalid @enderror"
                               name="password"
                               required >

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="password">{{ __('Password Confirmation')}}</label>

                    </div>
                    <div class="form-control-wrap">
                        <a href="#" class="form-icon form-icon-right passcode-switch lg"
                           data-target="password">
                            <em class="passcode-icon icon-show icon ni ni-eye"></em>
                            <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                        </a>


                        <input id="password_confirmation" type="password"
                               class="form-control form-control-lg @error('password_confirmation') is-invalid @enderror"
                               name="password_confirmation"
                               required >

                        @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('Reset Password')}}</button>
                </div>
            </form>


        </div>
    </div>
@endsection

