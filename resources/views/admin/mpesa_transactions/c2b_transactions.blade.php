@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">Customer To Business Mpesa Transactions</h3>

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

                                <div class="row g-1 mb-2">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <div class="form-control-wrap ">
                                                <div class="form-control-select">
                                                    <select class="form-control" id="house-status-filter">
                                                        <option value="">{{ __('Reconciliation Status')}}</option>
                                                        <option
                                                            value="{{ \App\Enums\ReconciliationStatusEnum::PENDING->value }}">
                                                            Pending
                                                        </option>

                                                        <option
                                                            value="{{ \App\Enums\ReconciliationStatusEnum::IGNORED->value }}">
                                                            Ignored
                                                        </option>
                                                        <option
                                                            value="{{ \App\Enums\ReconciliationStatusEnum::RECONCILED->value }}">
                                                            Reconciled
                                                        </option>

                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-md-3 ms-auto">
                                        <!--show the default jquery datatables search input here-->
                                        <div id="defaultSearch"></div>


                                    </div>
                                </div>

                                <table class="datatable datatable-wrap table dt-responsive nowrap  is-compact"
                                       id="tenants-list">

                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col">
                                            #
                                        </th>
                                        <th data-priority="1" class="nk-tb-col">
                                            <span class="sub-text">Trans ID</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">Trans Time</span>
                                        </th>

                                        <th class="nk-tb-col" data-priority="2">
                                            Amount
                                        </th>

                                        <th class="nk-tb-col">
                                            Reference Number
                                        </th>

                                        <th class="nk-tb-col">
                                            Phone Number
                                        </th>
                                        <th class="nk-tb-col" data-priority="3">
                                            Name
                                        </th>
                                        <th class="nk-tb-col" data-priority="2">
                                            Reconciliation
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

    <form action="" method="POST" id="deleteTransactionForm">
        @csrf
        @method('DELETE')
        <div class="modal fade" tabindex="-1" id="modalDeleteTransaction">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body modal-body-lg text-center">
                        <div class="nk-modal">
                            <em class="nk-modal-icon icon icon-circle icon-circle-xxl ni ni-cross bg-danger"></em>
                            <h4 class="nk-modal-title">{{__('Confirm Transaction Deletion')}}</h4>
                            <div class="nk-modal-text">
                                <p class="text-soft">
                                    {{ __('Do you really want to delete transaction? This process cannot be undone.')}}
                                </p>
                            </div>
                            <div class="d-flex justify-content-center align-items-center mt-5">
                                <a href="#" class="btn btn-mw btn-light me-3"
                                   data-bs-dismiss="modal">{{ __('Cancel')}}</a>
                                <button class="btn  btn-mw btn-danger" type="submit">
                                    Proceed To Delete
                                </button>
                            </div>

                        </div>
                    </div><!-- .modal-body -->
                </div>
            </div>
        </div>
    </form>

@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            const dom_new = '<"top">rtf<"d-flex justify-content-between align-items-center"ipl><"clear">';
            let oTable = $("#tenants-list").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    dom: dom_new,
                    // dom: 'tpr',
                    "bInfo": true,
                    "bLengthChange": true,
                    //define initial length
                    "iDisplayLength": 15,
                    ajax: {
                        url: '{!! route('admin.mpesa-c2b-transactions') !!}',
                        data: function (d) {
                            d.status_filter = $("#house-status-filter").val();
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
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'TransID', name: 'TransID', searchable: true},
                        {data: 'TransTime', name: 'TransTime', searchable: true},
                        {data: 'TransAmount', name: 'TransAmount', searchable: true, orderable: false},
                        {data: 'BillRefNumber', name: 'BillRefNumber', searchable: true, orderable: false},
                        {data: 'MSISDN', name: 'MSISDN', searchable: true, orderable: false},
                        {data: 'FirstName', name: 'FirstName', searchable: true, orderable: false},
                        {
                            data: 'reconciliation_status',
                            name: 'reconciliation_status',
                            searchable: true,
                            orderable: false
                        },
                        {data: 'actions', name: 'actions', orderable: false, searchable: false},
                    ],
                    drawCallback: function () {
                        $(".dataTables_paginate > .pagination").addClass("mt-3")
                    }
                });

            $('#house-status-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });

            $("#defaultSearch").html($("#tenants-list_filter"));

            oTable.on('click', '.delete-item', function (e) {
                e.preventDefault();
                let trans_id = $(this).data('id');
                let url = '{{ route("admin.mpesa-c2b-transactions.destroy", ":id") }}';
                url = url.replace(':id', trans_id);
                $("#deleteTransactionForm").attr('action', url);
                $("#modalDeleteTransaction").modal('show');
            });
        });
    </script>

@endpush
