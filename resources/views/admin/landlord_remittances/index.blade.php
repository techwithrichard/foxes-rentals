@extends('layouts.main')

@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Landlord Remittances')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p class="me-5">
                                        {{ __('Records are kept of payments made to landlords on a monthly basis. It is possible for more than one payment to be made to the same landlord, depending on the frequency at which rent is paid.')}}
                                    </p>
                                </div>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                       data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        @can('create landlord remittance')
                                            <ul class="nk-block-tools g-3">

                                                <li class="nk-block-tools-opt d-none d-sm-block">
                                                    <a href="{{ route('admin.landlord-remittance.create') }}"
                                                       class="btn btn-primary"><em
                                                            class="icon ni ni-plus"></em><span>{{ __('Add Remittance')}}</span></a>
                                                </li>
                                                <li class="nk-block-tools-opt d-block d-sm-none">
                                                    <a href="{{ route('admin.landlord-remittance.create') }}"
                                                       class="btn btn-icon btn-primary"><em
                                                            class="icon ni ni-plus"></em></a>
                                                </li>
                                            </ul>
                                        @endcan
                                    </div>
                                </div><!-- .toggle-wrap -->
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->


                    @if(session()->has('success'))
                        <div class="alert alert-pro alert-success alert-icon alert-dismissible">
                            <em class="icon ni ni-check-circle"></em>
                            <strong>{{ session('success') }}</strong>
                            <button class="close" data-bs-dismiss="alert"></button>
                        </div>

                    @endif
                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">

                                <div class="row g-1 mb-3">
                                    <div class="col-md-4">
                                        <div class="form-control-wrap ">
                                            <div class="form-control-select">
                                                <select class="form-control" id="landlord-filter">
                                                    <option value="">{{ __('Filter Landlord')}}</option>

                                                    @foreach($landlords as $id=> $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-3 ms-auto">
                                        <div class="form-group">

                                            <div class="form-control-wrap ">
                                                <input type="search" class="form-control" id="search-input"
                                                       placeholder="{{ __('Type in to search')}}">
                                            </div>
                                        </div>

                                    </div>
                                </div>


                                <table class="datatable table datatable-wrap nowrap pt-4"
                                       id="tenants-list">

                                    {{--                                <table class="datatable nowrap" id="tenants-list">--}}
                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col nk-tb-col-check">
                                            #
                                        </th>
                                        <th data-priority="1" class="nk-tb-col">
                                            <span class="sub-text">{{ __('Landlord')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Payment Period')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Amount')}}</span>
                                        </th>
                                        <th data-priority="3" class="nk-tb-col">
                                            {{ __('Paid On')}}
                                        </th>

                                        <th class="nk-tb-col">
                                            {{ __('Paid Via')}}
                                        </th>
                                        <th class="nk-tb-col">
                                            {{ __('Reference No')}}
                                        </th>


                                        <th data-priority="1" class="nk-tb-col  nk-tb-col-tools text-end"></th>

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

            let oTable = $("#tenants-list").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    // dom: dom_normal,

                    dom: '<"top">rt<"d-flex justify-content-between align-items-center"ipl><"clear">',
                    "bInfo": true,
                    "bLengthChange": true,
                    ajax: {
                        url: '{!! route('admin.landlord-remittance.index') !!}',
                        data: function (d) {
                            d.landlord_filter = $("#landlord-filter").val();
                        }
                    },
                    autoWidth: false,
                    responsive: {
                        details: {
                            // type: 'column',
                            // target: 'tr',
                            renderer: function (api, rowIdx) {

                                let data = api.cells(rowIdx, ':hidden').eq(0).map(function (cell) {
                                    let header = $(api.column(cell.column).header());

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
                        //

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
                        {data: 'landlord', name: 'landlord.name', searchable: true},
                        {data: 'period', name: 'period', searchable: false},
                        {data: 'amount', name: 'amount', searchable: true},
                        {data: 'paid_on', name: 'paid_on', searchable: false, orderable: false},
                        {data: 'payment_method', name: 'payment_method', searchable: true},
                        {data: 'payment_reference', name: 'payment_reference', searchable: true},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ],
                    drawCallback: function () {
                        $(".dataTables_paginate > .pagination").addClass("mt-3")
                    }
                });

            $('#search-input').on('input', function () {
                oTable.search($(this).val()).draw();
            });

            $('#landlord-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });


        });
    </script>


@endpush
