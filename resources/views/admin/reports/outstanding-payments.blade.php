@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Outstanding Payments')}}</h3>
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

                                <div class="row g-1 mb-2">
                                    <div class="col-md-5">
                                        <div class="form-group">
                                            {{--                                            <label class="form-label">Datepicker Range</label>--}}
                                            <div class="form-control-wrap">
                                                <div class="input-daterange  input-group"
                                                     id="filters-range">
                                                    <input type="text"
                                                           class="form-control bg-white"
                                                           placeholder="{{ __('From date')}}"
                                                           data-provide="datepicker"
                                                           data-date-format="yyyy-mm-dd"
                                                           readonly
                                                           id="date-from-filter"
                                                    />
                                                    <div class="input-group-addon">TO</div>
                                                    <input
                                                        type="text"
                                                        class="form-control bg-white"
                                                        placeholder="{{ __('To Date')}}"
                                                        readonly
                                                        id="date-to-filter"
                                                    />

                                                    <div class="input-group-append">
                                                        <button type="button" class="btn btn-light" id="btn-filter">
                                                            {{ __('Filter')}}
                                                        </button>
                                                        <button type="reset" class="btn btn-white btn-outline-lighter"
                                                                id="btn-reset">{{ __('Reset')}}
                                                        </button>
                                                    </div>
                                                    {{--                                                    <div class="input-group-addon">FILTER</div>--}}
                                                </div>
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


                                <table class="datatable table dt-responsive nowrap nk-tb-list nk-tb-ulist is-compact"
                                       id="tenants-list">

                                    {{--                                <table class="datatable nowrap" id="tenants-list">--}}
                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col nk-tb-col-check">
                                            #
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Invoice')}}</span>
                                        </th>
                                        <th data-priority="1" class="nk-tb-col">
                                            <span class="sub-text">{{ __('Tenant')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Property')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('House')}}</span>
                                        </th>
                                        <th data-priority="3" class="nk-tb-col">
                                            <span class="sub-text">{{ __('Invoice Date')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            {{ __('Balance Due')}}
                                        </th>
                                        <th class="nk-tb-col">
                                            {{ __('Status')}}
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

    <div class="modal fade" tabindex="-1" id="modal-notify-tenant" wire:ignore.self>
        @livewire('admin.invoice.notify-tenant-payment-component')
    </div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function () {

            $('#filters-range').datepicker({
                format: "yyyy-mm-dd"
            });



            const dom_normal = '<"row justify-between g-2"<"col-7 col-sm-4 text-start"><"col-5 col-sm-8 text-end"<"datatable-filter"<"d-flex justify-content-end g-2"l>>>><"datatable-wrap my-3"t><"row align-items-center"<"col-7 col-sm-12 col-md-9"p><"col-5 col-sm-12 col-md-3 text-start text-md-end"i>>';

            let oTable = $("#tenants-list").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    dom: dom_normal,
                    "bInfo": true,
                    "bLengthChange": false,
                    ajax: {
                        url: '{!! route('admin.reports.outstanding_payments') !!}',
                        data: function (d) {
                            d.date_from = $("#date-from-filter").val();
                            d.date_to = $("#date-to-filter").val();
                        }
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
                        $(row).find('td:eq(7)').addClass('nk-tb-col');
                        $(row).find('td:eq(8)').addClass('nk-tb-col nk-tb-col-tools');
                        // $(row).find('td:eq(5)').addClass('nk-tb-col');
                        // $(row).find('td:eq(6)').addClass('nk-tb-col');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'invoice_id', name: 'invoice_id', searchable: true},
                        {data: 'tenant', name: 'tenant.name', searchable: true},
                        {data: 'property', name: 'property.name', searchable: true},
                        {data: 'house', name: 'house.name', searchable: true},
                        {data: 'created_at', name: 'created_at', searchable: false, orderable: false},
                        {data: 'balance', name: 'balance', searchable: true},
                        {data: 'status', name: 'status', searchable: true},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    drawCallback: function () {
                        $(".dataTables_paginate > .pagination").addClass("mt-3")
                    }
                });

            //livewire on showTenantPaymentModal open modal
            window.livewire.on('showTenantPaymentModal', function () {
                $('#modal-notify-tenant').modal('show');
            });

            window.livewire.on('showPayInvoiceModal', function () {
                $('#add-payment-modal').modal('show');
            });

            window.livewire.on('notificationSent', function () {
                $('#modal-notify-tenant').modal('hide');
            });

            // when modal is closed,emit event to livewire
            $('#modal-notify-tenant').on('hidden.bs.modal', function () {
                window.livewire.emit('resetDetails');
            });

            //listen on refreshTable event and refresh table
            window.livewire.on('refreshTable', function () {
                $('#add-payment-modal').modal('hide');
                oTable.draw();
            });

            // $('.input-daterange').datepicker({
            //     startDate: "yyyy-mm-dd",
            //     endDate: "yyyy-mm-dd",
            // });

            $('#btn-filter').on('click', function () {
                if ($("#date-from-filter").val() !== '' && $("#date-to-filter").val() !== '') {
                    oTable.ajax.reload(null, false);
                } else {
                    alert('Please select a date range');
                }

            });

            //btn reset filter
            $('#btn-reset').on('click', function () {
                $("#date-from-filter").val('');
                $("#date-to-filter").val('');
                oTable.ajax.reload(null, false);
            });

        });
    </script>


@endpush
