@extends('layouts.auth_layout')

@section('content')
    <div class="card card-bordered">
        <div class="card-inner card-inner-lg">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <h4 class="nk-block-title">{{ __('Sign-In')}}</h4>
                    <div class="nk-block-des">
                        <p>{{ __('Access the admin panel using your email and passcode.')}}</p>
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


            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <div class="form-label-group">
                        <label class="form-label" for="default-01">{{ __('Email or Username')}}</label>
                    </div>
                    <div class="form-control-wrap">
                        <input id="email" type="email"
                               class="form-control form-control-lg @error('email') is-invalid @enderror" name="email"
                               value="{{ old('email') }}" required autocomplete="email" autofocus>

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
                        @if (Route::has('password.request'))
                            <a class="link link-primary link-sm" href="{{ route('password.request') }}">
                                {{ __('Forgot Your Password?') }}
                            </a>
                        @endif

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
                               required autocomplete="current-password">

                        @error('password')
                        <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                        @enderror
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-lg btn-primary btn-block">{{ __('Sign in')}}</button>
                </div>
            </form>
            <div class="form-note-s2 text-center pt-4"> {{ __('New on our platform?')}} <a
                    href="">{{ __('Create an account')}}</a>
            </div>

        </div>
    </div>
@endsection
