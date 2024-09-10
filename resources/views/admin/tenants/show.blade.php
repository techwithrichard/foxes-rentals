@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ $tenant->name }}
                                    @if ($tenant->deleted_at)
                                        <span class="badge bg-lg bg-danger">{{ __('Archived Account')}}</span>
                                    @endif
                                </h3>


                            </div>


                            <div class="nk-block-head-content">
                                <x-back_link href="{{ route('admin.tenants.index') }}"></x-back_link>


                            </div>
                        </div>
                    </div><!-- .nk-block-head -->

                    @if (session()->has('success'))
                        <div class="nk-block">
                            <div class="alert alert-info alert-icon"><em class="icon ni ni-alert-circle"></em>
                                <strong>
                                    {{ session()->get('success') }}
                                </strong>
                            </div>
                        </div>
                    @endif

                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered">
                            <div class="card-aside-wrap">
                                <div class="card-content">
                                    <ul class="nav nav-tabs nav-tabs-mb-icon nav-tabs-card">
                                        <li class="nav-item">
                                            <a class="nav-link active" data-bs-toggle="tab" href="#tabItem1">
                                                <em class="icon ni ni-user-circle-fill"></em>
                                                <span>{{ __('Personal information') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tabItem2">
                                                <em class="icon ni ni-folder-list"></em>
                                                <span>{{ __('Lease History') }}</span>
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-bs-toggle="tab" href="#tabDocuments">
                                                <em class="icon ni ni-folder-list"></em>
                                                <span>{{ __('Identifying Documents') }}</span>
                                            </a>
                                        </li>


                                    </ul>
                                    <div class="card-inner">
                                        <div class="tab-content">
                                            <div class="tab-pane active" id="tabItem1">
                                                @include('admin.tenants.partials.tenant_tab_details')
                                            </div>
                                            <div class="tab-pane" id="tabItem2">
                                                <div
                                                    class="nk-tb-list border border-light rounded overflow-hidden is-compact">
                                                    <div class="nk-tb-item nk-tb-head">
                                                        <div class="nk-tb-col nk-tb-col-check">
                                                            <span class="lead-text">#</span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span class="lead-text">{{ __('Property')}}</span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span class="lead-text">{{ __('House')}}</span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span class="lead-text">{{ __('Start Date')}}</span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span
                                                                class="lead-text d-none d-sm-inline">{{ __('End Date')}}</span>
                                                        </div>
                                                        <div class="nk-tb-col">
                                                            <span
                                                                class="lead-text d-none d-sm-inline">{{ __('Status')}}</span>
                                                        </div>
                                                        <div class="nk-tb-col nk-tb-col-tools">
                                                            <span class="lead-text">&nbsp;</span>
                                                        </div>
                                                    </div>
                                                    @foreach ($tenant->all_leases as $lease)
                                                        <div class="nk-tb-item">
                                                            <div
                                                                class="nk-tb-col nk-tb-col-check"> {{ $loop->iteration }}</div>
                                                            <div class="nk-tb-col">
                                                                {{ $lease->property->name ?? '' }}
                                                            </div>
                                                            <div class="nk-tb-col">
                                                                {{ $lease->house->name ?? '' }}
                                                            </div>
                                                            <div class="nk-tb-col">
                                                                {{ $lease->start_date->format('d M Y') }}
                                                            </div>
                                                            <div class="nk-tb-col">
                                                                <span class="d-none d-sm-inline">
                                                                    {{ $lease->deleted_at ? $lease->deleted_at->format('d M Y') : '' }}
                                                                </span>
                                                            </div>
                                                            <div class="nk-tb-col">
                                                                @if ($lease->deleted_at)
                                                                    <span
                                                                        class="badge badge-dot badge-dot-xs bg-danger">
                                                                        {{ __('In Active')}}
                                                                    </span>
                                                                @else
                                                                    <span
                                                                        class="badge badge-dot badge-dot-xs bg-success">{{ __('Active')}}</span>
                                                                @endif


                                                            </div>
                                                            <div class="nk-tb-col nk-tb-col-tools">
                                                                <ul class="nk-tb-actions gx-1">

                                                                    @can('view lease')
                                                                        <li>
                                                                            <a href="{{ route('admin.leases.show', $lease->id) }}"
                                                                               class="btn btn-sm btn-icon btn-trigger me-n1"><em
                                                                                    class="icon ni ni-eye-alt text-info"></em></a>
                                                                        </li>
                                                                    @endcan
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>

                                            <div class="tab-pane" id="tabDocuments">
                                                @livewire('admin.tenants.show-tenant-documents-component',['tenantId'=>$tenant->id])

                                            </div>


                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>


                </div><!-- .nk-block -->


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

    <script>
        $(document).ready(function () {
            const dom_normal = '<"row justify-between g-2"<"col-7 col-sm-4 text-start"f><"col-5 col-sm-8 text-end"<"datatable-filter"<"d-flex justify-content-end g-2"l>>>><"datatable-wrap my-3"t><"row align-items-center"<"col-7 col-sm-12 col-md-9"p><"col-5 col-sm-12 col-md-3 text-start text-md-end"i>>';

            var oTable = $("#tenants-list").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    dom: dom_normal,
                    "bInfo": true,
                    "bLengthChange": true,
                    ajax: '{!! route('admin.tenants.index') !!}',
                    autoWidth: false,
                    responsive: {
                        details: {
                            type: 'column',
                            target: 'tr',
                            renderer: function (api, rowIdx) {

                                var data = api.cells(rowIdx, ':hidden').eq(0).map(function (cell) {
                                    var header = $(api.column(cell.column).header());

                                    return '<tr>' +
                                        '<td class="fw-bold">' +
                                        header.text() + ':' +
                                        '</td> ' +
                                        '<td>' +
                                        api.cell(cell).data() +
                                        '</td>' +
                                        '</tr>';
                                }).toArray().join('');

                                return data ?
                                    $('<table/>').append(data) :
                                    false;
                            },

                        }
                    },
                    columnDefs: [{
                        className: 'nk-tb-item',
                    }],
                    // order: [1, 'asc'],

                    language: {
                        search: "",
                        searchPlaceholder: "Type in to Search",
                        lengthMenu: "<span class='d-none d-sm-inline-block'>Show</span><div class='form-control-select'> _MENU_ </div>",
                        info: "_START_ -_END_ of _TOTAL_",
                        infoEmpty: "0",
                        infoFiltered: "( Total _MAX_  )",
                        paginate: {
                            "first": "First",
                            "last": "Last",
                            "next": "Next",
                            "previous": "Prev"
                        }
                    },

                    createdRow: function (row, data, dataIndex) {
                        $(row).find('td:eq(0)').addClass('nk-tb-col nk-tb-col-check');
                        $(row).find('td:eq(1)').addClass('nk-tb-col');
                        $(row).find('td:eq(2)').addClass('nk-tb-col');
                        $(row).find('td:eq(3)').addClass('nk-tb-col');
                        $(row).find('td:eq(4)').addClass('nk-tb-col');
                        $(row).find('td:eq(5)').addClass('nk-tb-col nk-tb-col-tools');
                        // $(row).find('td:eq(5)').addClass('nk-tb-col');
                        // $(row).find('td:eq(6)').addClass('nk-tb-col');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'name', name: 'name', searchable: true},
                        {data: 'email', name: 'email', searchable: true},
                        {data: 'phone', name: 'phone', searchable: true},
                        {data: 'leased_houses', name: 'leased_houses', searchable: false, orderable: false},

                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    drawCallback: function () {
                        $(".dataTables_paginate > .pagination").addClass("mt-3")
                    }
                });
        });
    </script>


@endpush

