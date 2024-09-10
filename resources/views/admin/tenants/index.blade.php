@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Tenants')}}</h3>
                            </div><!-- .nk-block-head-content -->

                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                       data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li>
                                                <div>
                                                    <livewire:exports.tenants-export-component/>

                                                </div>
                                            </li>
                                            @can('create tenant')
                                                <li class="nk-block-tools-opt">
                                                    <a href="{{ route('admin.tenants.create') }}"
                                                       class="btn btn-primary">
                                                        <em class="icon ni ni-plus"></em>
                                                        <span>{{ __('Create Tenant')}}</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head-content -->


                            {{--                            <div class="nk-block-head-content">--}}
                            {{--                                <div class="toggle-wrap nk-block-tools-toggle">--}}
                            {{--                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"--}}
                            {{--                                       data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>--}}
                            {{--                                    <div class="toggle-expand-content" data-content="pageMenu">--}}
                            {{--                                        <ul class="nk-block-tools g-3">--}}

                            {{--                                            <li class="nk-block-tools-opt d-none d-sm-block">--}}
                            {{--                                                <a href="{{ route('admin.tenants.export') }}"--}}
                            {{--                                                   class="btn btn-secondary"><em--}}
                            {{--                                                        class="icon ni ni-file-pdf"></em><span>{{ __('Export')}}</span></a>--}}
                            {{--                                            </li>--}}
                            {{--                                            <li class="nk-block-tools-opt d-none d-sm-block">--}}
                            {{--                                                <a href="{{ route('admin.tenants.create') }}"--}}
                            {{--                                                   class="btn btn-primary"><em--}}
                            {{--                                                        class="icon ni ni-plus"></em><span>{{ __('Create Tenant')}}</span></a>--}}
                            {{--                                            </li>--}}
                            {{--                                            <li class="nk-block-tools-opt d-block d-sm-none">--}}
                            {{--                                                <a href="{{ route('admin.tenants.create') }}"--}}
                            {{--                                                   class="btn btn-icon btn-primary"><em--}}
                            {{--                                                        class="icon ni ni-plus"></em></a>--}}
                            {{--                                            </li>--}}
                            {{--                                            <li class="nk-block-tools-opt d-block d-sm-none">--}}
                            {{--                                                <a href="{{ route('admin.tenants.export') }}"--}}
                            {{--                                                   class="btn btn-icon btn-secondary"><em--}}
                            {{--                                                        class="icon ni ni-file-pdf"></em></a>--}}
                            {{--                                            </li>--}}
                            {{--                                        </ul>--}}
                            {{--                                    </div>--}}
                            {{--                                </div><!-- .toggle-wrap -->--}}
                            {{--                            </div><!-- .nk-block-head-content -->--}}
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->


                    @if(session()->has('success'))
                        <div class="alert alert-success alert-icon alert-dismissible">
                            <em class="icon ni ni-check-circle"></em>
                            <strong>{{ session('success') }}</strong>
                            <button class="close" data-bs-dismiss="alert"></button>
                        </div>

                    @endif
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable table dt-responsive nowrap nk-tb-list nk-tb-ulist is-compact"
                                       id="tenants-list">

                                    {{--                                <table class="datatable nowrap" id="tenants-list">--}}
                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col nk-tb-col-check">
                                            #
                                        </th>
                                        <th data-priority="1" class="nk-tb-col">
                                            <span class="sub-text">{{ __('User')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Email')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span
                                                class="sub-text">{{ __('Phone')}}</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            {{ __('Assigned Rooms')}}
                                        </th>

                                        <th data-priority="1" class="nk-tb-col nk-tb-col-tools text-end">
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>


                                    </tbody>
                                </table>
                            </div>
                        </div><!-- .card-preview -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>


@endsection

@push('scripts')

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
                        $(row).find('td:eq(0)').addClass('nk-tb-col');
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
