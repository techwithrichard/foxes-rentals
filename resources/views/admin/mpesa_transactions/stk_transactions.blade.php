@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">
                                    {{ __('STK Transactions')}}
                                </h3>

                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">

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
                                <table class="datatable table dt-responsive nowrap  is-compact"
                                       id="tenants-list">

                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col nk-tb-col-check">
                                            #
                                        </th>
                                        <th data-priority="1" class="nk-tb-col">
                                            <span class="sub-text">Phone</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">Amount</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">Reference</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            <span class="sub-text">Status</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            <span class="sub-text">Mpesa Receipt</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            <span class="sub-text">Result Desc</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            <span class="sub-text">Transaction Date</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            <span class="sub-text">Merchant RequestID</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            <span class="sub-text">Checkout RequestID</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            {{ __('Recorded On')}}
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
                    ajax: '{!! route('admin.mpesa-stk-transactions') !!}',
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
                        $(row).find('td:eq(8)').addClass('nk-tb-col');
                        $(row).find('td:eq(9)').addClass('nk-tb-col nk-tb-col-tools');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'phone', name: 'phone', searchable: true},
                        {data: 'amount', name: 'amount', searchable: true},
                        {data: 'reference', name: 'reference', searchable: true},
                        {data: 'status', name: 'status', searchable: true},
                        {data: 'MpesaReceiptNumber', name: 'MpesaReceiptNumber', searchable: true},
                        {data: 'ResultDesc', name: 'ResultDesc', searchable: true},
                        {data: 'TransactionDate', name: 'TransactionDate', searchable: true},
                        {data: 'MerchantRequestID', name: 'MerchantRequestID', searchable: true},
                        {data: 'CheckoutRequestID', name: 'CheckoutRequestID', searchable: true},
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    drawCallback: function () {
                        $(".dataTables_paginate > .pagination").addClass("mt-3")
                    }
                });
        });
    </script>

@endpush
