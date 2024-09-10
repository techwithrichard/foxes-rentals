@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Create Custom Invoice')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <ul class="list-inline">
                                        <li>{{ __('Best suited to send to tenants for dynamic bills,e.g water,electricity')}}</li>
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-block-head-content">

                            <x-back_link href="{{ route('admin.custom-invoice.index') }}"></x-back_link>
                                  
                        
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    @livewire('admin.accounting.create-invoice-component')
                </div>
            </div>
        </div>
    </div>

@endsection

