@extends('layouts.landlord_layout')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Vouchers')}}</h3>
                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                       data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">

                                    </div>
                                </div><!-- .toggle-wrap -->
                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->


                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable table dt-responsive nowrap nk-tb-list nk-tb-ulist is-compact"
                                       id="tenants-list">

                                    {{--                                <table class="datatable nowrap" id="tenants-list">--}}
                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col nk-tb-col-check">
                                            #{{ __('ID')}}
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Property')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Unit')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Voucher Date')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Type')}}</span>
                                        </th>
                                        <th data-priority="3" class="nk-tb-col">
                                            <span class="sub-text">{{ __('Amount')}}</span>
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


            let oTable = $("#tenants-list").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    dom: dom_normal,
                    "bInfo": true,
                    "bLengthChange": false,
                    ajax: {
                        url: '{!! route('landlord.vouchers.index') !!}',
                    },
                    autoWidth: false,
                    responsive: {
                        details: {
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
                        $(row).find('td:eq(0)').addClass('nk-tb-col');
                        $(row).find('td:eq(1)').addClass('nk-tb-col');
                        $(row).find('td:eq(2)').addClass('nk-tb-col');
                        $(row).find('td:eq(3)').addClass('nk-tb-col');
                        $(row).find('td:eq(4)').addClass('nk-tb-col');
                        $(row).find('td:eq(5)').addClass('nk-tb-col');
                        $(row).find('td:eq(6)').addClass('nk-tb-col');
                        $(row).find('td:eq(7)').addClass('nk-tb-col nk-tb-col-tools');
                    },
                    columns: [
                        {data: 'voucher_id', name: 'voucher_id', orderable: false, searchable: true},
                        {data: 'property', name: 'property.name', searchable: true},
                        {data: 'house', name: 'house.name', searchable: true},
                        {data: 'voucher_date', name: 'voucher_date', searchable: false, orderable: false},
                        {data: 'type', name: 'type', searchable: true},
                        {data: 'amount', name: 'amount', searchable: true},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    drawCallback: function () {
                        $(".dataTables_paginate > .pagination").addClass("mt-3")
                    }
                });

            $('#search-input').on('input', function () {
                oTable.search($(this).val()).draw();
            });


            window.livewire.on('notificationSent', function () {
                $('#modal-notify-tenant').modal('hide');
            });

            // when modal is closed,emit event to livewire
            $('#modal-notify-tenant').on('hidden.bs.modal', function () {
                window.livewire.emit('resetDetails');
            });

        });
    </script>


@endpush
