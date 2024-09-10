@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Register Landlord')}}</h3>
                                <div class="nk-block-des text-soft">
                                    {{ __('Input new landlord details carefully')}}
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.landlords.index') }}"></x-back_link>
                      
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    @livewire('admin.landlords.create-landlord-component')
                </div>
            </div>
        </div>
    </div>

@endsection
