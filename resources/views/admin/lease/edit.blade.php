@extends('layouts.main')
@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Update Lease')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>{{ __('Update lease details,manage termination notices,alter monthly bills and monthly rent')}}</p>
                                </div>
                            </div><!-- .nk-block-head-content -->

                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.leases.index') }}"></x-back_link>
                                  
                            
                            </div>


                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->
                    @livewire('admin.lease.update-lease-component',['leaseId' =>$id ])
                </div>
            </div>
        </div>
    </div>

@endsection
