@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Payments Proofs')}}</h3>
                                <p>{{ __('You have total')}} {{ $pendingProofsCount }} 
                                    {{ __('payment proofs which have not been reviewed.')}}</p>


                            </div>
                            <!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1"
                                       data-target="pageMenu"><em class="icon ni ni-menu-alt-r"></em></a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                                                               <ul class="nk-block-tools g-3">

                                                                                   <li class="nk-block-tools-opt d-none d-sm-block">
                                                                                        <a href="{{ route('admin.leases.create') }}"
                                                                                          class="btn btn-primary"><em
                                                                                                class="icon ni ni-plus"></em><span>Place Tenant</span></a>
                                                                                    </li>
                                                                                    <li class="nk-block-tools-opt d-block d-sm-none">
                                                                                        <a href="{{ route('admin.leases.create') }}"
                                                                                           class="btn btn-icon btn-primary"><em
                                                                                                class="icon ni ni-plus"></em></a>
                                                                                    </li>
                                                                                </ul>
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
                                <table
                                    class="datatable table dt-responsive nowrap nk-tb-list nk-tb-ulist is-compact"
                                    id="tenants-list">

                                    {{--                                <table class="datatable nowrap" id="tenants-list">--}}
                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col nk-tb-col-check">
                                            #ID
                                        </th>
                                        <th data-priority="1" class="nk-tb-col">
                                            <span class="sub-text">{{ __('Tenant')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Document')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Amount')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Payment Method')}}</span>
                                        </th>
                                        <th data-priority="3" class="nk-tb-col">
                                            <span class="sub-text">{{ __('Submitted on')}}</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            {{ __('Invoice')}}
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
                    "bLengthChange": true,
                    ajax: '{!! route('admin.payments-proof.index') !!}',
                    autoWidth: false,
                    responsive: {
                        details: {
                            type: 'column',
                            target: 'tr',
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
                        $(row).find('td:eq(5)').addClass('nk-tb-col');
                        $(row).find('td:eq(6)').addClass('nk-tb-col');
                        $(row).find('td:eq(7)').addClass('nk-tb-col');
                        $(row).find('td:eq(8)').addClass('nk-tb-col nk-tb-col-tools');
                        // $(row).find('td:eq(5)').addClass('nk-tb-col');
                        // $(row).find('td:eq(6)').addClass('nk-tb-col');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'tenant', name: 'tenant.name', searchable: true},
                        {data: 'receipt_document', name: 'receipt_document', searchable: true},
                        {data: 'amount', name: 'amount', searchable: true},
                        {data: 'payment_method', name: 'payment_method', searchable: false, orderable: false},
                        {data: 'created_at', name: 'created_at', searchable: true},
                        {data: 'invoice_id', name: 'invoice_id', searchable: true},
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
