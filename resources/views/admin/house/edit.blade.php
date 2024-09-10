@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Update House Details')}}</h3>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.houses.index') }}"></x-back_link>
                     
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->

                    @livewire('admin.house.update-house-component',['houseId'=>$id])
                </div>
            </div>
        </div>
    </div>
@endsection
