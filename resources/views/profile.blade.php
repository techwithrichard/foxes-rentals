@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ __('User Profile') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>Manage your profile information</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-body text-center">
                                    <div class="avatar avatar-xl bg-primary mb-3">
                                        <span class="avatar-text text-white fs-2x">{{ substr($user->name, 0, 2) }}</span>
                                    </div>
                                    <h5>{{ $user->name }}</h5>
                                    <p class="text-muted">{{ $user->email }}</p>
                                    <div class="d-flex flex-wrap justify-content-center gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="badge badge-sm badge-outline-primary">{{ ucfirst($role->name) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">{{ __('Profile Information') }}</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Name') }}</label>
                                            <div class="form-control-plaintext">{{ $user->name }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Email') }}</label>
                                            <div class="form-control-plaintext">{{ $user->email }}</div>
                                        </div>
                                        @if($user->phone)
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Phone') }}</label>
                                            <div class="form-control-plaintext">{{ $user->phone }}</div>
                                        </div>
                                        @endif
                                        @if($user->address)
                                        <div class="col-md-12">
                                            <label class="form-label">{{ __('Address') }}</label>
                                            <div class="form-control-plaintext">{{ $user->address }}</div>
                                        </div>
                                        @endif
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Member Since') }}</label>
                                            <div class="form-control-plaintext">{{ $user->created_at->format('M d, Y') }}</div>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">{{ __('Email Verified') }}</label>
                                            <div class="form-control-plaintext">
                                                @if($user->email_verified_at)
                                                    <span class="badge badge-sm badge-success">{{ __('Verified') }}</span>
                                                @else
                                                    <span class="badge badge-sm badge-warning">{{ __('Not Verified') }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
