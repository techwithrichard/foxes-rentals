@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Leases')}}</h3>
                            </div><!-- .nk-block-head-content -->


                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                       data-target="pageMenu"><em class="icon ni ni-more-v"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li>
                                                <div>
                                                    <livewire:exports.leases-export-component/>

                                                </div>
                                            </li>

                                            @can('create lease')
                                                <li class="nk-block-tools-opt">
                                                    <a href="{{ route('admin.leases.create') }}"
                                                       class="btn btn-primary">
                                                        <em class="icon ni ni-plus"></em>
                                                        <span>{{ __('Place Tenant')}}</span>
                                                    </a>
                                                </li>
                                            @endcan
                                        </ul>
                                    </div>
                                </div>
                            </div><!-- .nk-block-head-content -->


                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->


                    @if(session()->has('success'))
                        <div class="alert alert-success alert-pro alert-icon alert-dismissible">
                            <em class="icon ni ni-check-circle"></em>
                            <strong>{{ session('success') }}</strong>
                            <button class="close" data-bs-dismiss="alert"></button>
                        </div>

                    @endif
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">

                                <div class="row mb-4">
                                    <div class="col-md-4 me-auto">
                                        <div class="form-group">
                                            <div class="form-control-wrap ">
                                                <div class="form-control-select">
                                                    <select class="form-control" id="property-filter">
                                                        <option value="">{{ __('Filter By Building')}}</option>
                                                        @foreach($properties as $key => $name)
                                                            <option value="{{ $key}}">
                                                                {{ $name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="search-input"
                                               placeholder="{{ __('Search')}}">
                                    </div>
                                </div>


                                <table
                                    class="datatable table datatable-wrap dt-responsive nowrap nk-tb-list nk-tb-ulist is-compact"
                                    id="tenants-list">

                                    {{--                                <table class="datatable nowrap" id="tenants-list">--}}
                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col nk-tb-col-check">
                                            #
                                        </th>
                                        <th data-priority="1" class="nk-tb-col">
                                            <span class="tb-lead">{{ __('Lease ID')}}</span>
                                        </th>

                                        <th data-priority="1" class="nk-tb-col">
                                            <span class="tb-lead">{{ __('Tenant')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="tb-lead">{{ __('Property')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="tb-lead">{{ __('House')}}</span>
                                        </th>
                                        <th data-priority="3" class="nk-tb-col">
                                            <span
                                                class="tb-lead">{{ __('Start Date')}}</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            {{ __('All Bills')}}
                                        </th>
                                        <th class="nk-tb-col">
                                            {{ __('Cycle')}}
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
            var oTable = $("#tenants-list").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    dom: '<"top">rt<"d-flex justify-content-between align-items-center"ipl><"clear">',
                    // "bInfo": false,
                    "bLengthChange": true,
                    ajax: {
                        url: '{!! route('admin.leases.index') !!}',
                        data: function (d) {
                            d.property_filter = $("#property-filter").val();
                        }
                    },
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
                        $(row).find('td:eq(5)').addClass('nk-tb-col');
                        $(row).find('td:eq(6)').addClass('nk-tb-col');
                        $(row).find('td:eq(7)').addClass('nk-tb-col');
                        $(row).find('td:eq(8)').addClass('nk-tb-col nk-tb-col-tools');
                        // $(row).find('td:eq(5)').addClass('nk-tb-col');
                        // $(row).find('td:eq(6)').addClass('nk-tb-col');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'lease_id', name: 'lease_id', searchable: true},
                        {data: 'tenant', name: 'tenant.name', searchable: true},
                        {data: 'property', name: 'property.name', searchable: true},
                        {data: 'house', name: 'house.name', searchable: true},
                        {data: 'start_date', name: 'start_date', searchable: false, orderable: false},
                        {data: 'rent_and_bills', name: 'rent_and_bills', searchable: true},
                        {data: 'rent_cycle', name: 'rent_cycle', searchable: true},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    drawCallback: function () {
                        $(".dataTables_paginate > .pagination").addClass("mt-3")
                    }
                });

            $('#search-input').on('input', function () {
                oTable.search($(this).val()).draw();
            });

            $('#property-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });
        });
    </script>


@endpush
