@extends('layouts.main')
@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Create New User')}}</h3>
                                <div class="nk-block-des text-soft">
                                    {{ __('Created user will receive email to set up their password.')}}
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.users-management.index') }}"></x-back_link>
                                  
                    
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    @livewire('admin.users.create-user-component')
                </div>
            </div>
        </div>
    </div>

@endsection
