@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head mb-0">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Add Remittance')}}</h3>
                                <div class="nk-block-des text-soft">
                                    {{ __('Add landlord paid rental amount for a defined period')}}
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.landlord-remittance.index') }}"></x-back_link>
                                  

                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    @livewire('admin.accounting.create-landlord-remittance-component')
                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js" defer></script>
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
@endpush

@push('scripts')
    <script
        src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js">
    </script>
    <script
        src="https://unpkg.com/filepond-plugin-file-validate-size/dist/filepond-plugin-file-validate-size.js">
    </script>
    {{--<script--}}
    {{--    src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>--}}
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script>
        FilePond.registerPlugin(FilePondPluginFileValidateType);
        FilePond.registerPlugin(FilePondPluginFileValidateSize);
        // FilePond.registerPlugin(FilePondPluginImagePreview);
    </script>


@endpush

