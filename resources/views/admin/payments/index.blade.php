@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Payments History')}}</h3>


                            </div>
                            <!-- .nk-block-head-content -->
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

                                @livewire('widgets.all-payments-filter-component')



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
                                            <span class="sub-text">{{ __('Payment Date')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Amount')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Payment Method')}}</span>
                                        </th>
                                        <th data-priority="3" class="nk-tb-col">
                                            <span class="sub-text">{{ __('Building')}}</span>
                                        </th>

                                        <th class="nk-tb-col">
                                            {{ __('House')}}
                                        </th>

                                        <th class="nk-tb-col">
                                            {{ __('Reference')}}
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

    <div class="modal fade" tabindex="-1" id="modal-confirm-payment">
        @livewire('admin.invoice.approve-payment-component')
    </div>

    <div class="modal fade" tabindex="-1" id="modal-reject-payment">
        @livewire('admin.invoice.reject-payment-component')
    </div>

@endsection

@push('scripts')

    <script>
        $(document).ready(function () {
            const dom_normal = '<"row justify-between g-2"<"col-7 col-sm-4 text-start"><"col-5 col-sm-8 text-end"<"datatable-filter"<"d-flex justify-content-end g-2">>>><"datatable-wrap my-3"t><"row align-items-center"<"col-7 col-sm-12 col-md-9"p><"col-5 col-sm-12 col-md-3 text-start text-md-end"i>>';

            let oTable = $("#tenants-list").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    dom: dom_normal,
                    "bInfo": true,
                    "bLengthChange": true,
                    ajax: {
                        url: '{!! route('admin.payments.list') !!}',
                        data: function (d) {
                            d.from_date = $("#from-date-filter").val();
                            d.to_date = $("#to-date-filter").val();
                            d.status = $("#status-filter").val();
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
                        $(row).find('td:eq(8)').addClass('nk-tb-col');
                        $(row).find('td:eq(9)').addClass('nk-tb-col nk-tb-col-tools');
                        // $(row).find('td:eq(5)').addClass('nk-tb-col');
                        // $(row).find('td:eq(6)').addClass('nk-tb-col');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'tenant', name: 'tenant.name', searchable: true},
                        {data: 'paid_at', name: 'paid_at', searchable: true},
                        {data: 'amount', name: 'amount', searchable: true},
                        {data: 'payment_method', name: 'payment_method', searchable: true, orderable: false},
                        {data: 'property', name: 'invoice.property.name', searchable: true, orderable: false},
                        {data: 'house', name: 'invoice.house.name', searchable: true, orderable: false},
                        {data: 'reference_number', name: 'reference_number', searchable: true},
                        {data: 'status', name: 'status', orderable: false, searchable: false},
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

            window.livewire.on('showApprovePaymentModal', function () {
                $('#modal-confirm-payment').modal('show');
            });
            window.livewire.on('showRejectPaymentModal', function () {
                $('#modal-reject-payment').modal('show');
            });

            //when #searchInput is changed, search
            $('#searchInput').on('keyup', function () {
                oTable.search(this.value).draw();
            });

            //when #date-filter is changes,filter table
            $('#status-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });

            //when both start-date-filter and end-date-filter are changed,filter table
            $('#to-date-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });

            $('#from-date-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });

            //when to-date-filter is changed,ensure from-date-filter is not greater than to-date-filter
            $('#to-date-filter').on('change', function () {
                let from_date = $('#from-date-filter').val();
                let to_date = $('#to-date-filter').val();
                if (from_date > to_date) {
                    $('#from-date-filter').val(to_date);
                }
            });

            //when from-date-filter is changed,ensure to-date-filter is not less than from-date-filter
            $('#from-date-filter').on('change', function () {
                let from_date = $('#from-date-filter').val();
                let to_date = $('#to-date-filter').val();
                if (from_date > to_date) {
                    $('#to-date-filter').val(from_date);
                }
            });


            //listen on refreshTable event and refresh table
            window.livewire.on('refreshTable', function () {
                $('#modal-reject-payment').modal('hide');
                $('#modal-confirm-payment').modal('hide');
                oTable.draw();
            });

        });

        // Function to delete payment
        function deletePayment(paymentId) {
            if (confirm('{{ __('Are you sure you want to delete this payment entry ?')}}')) {
                $.ajax({
                    url: '/admin/payments/' + paymentId,
                    type: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function(response) {
                        if (response.success) {
                            // Show success message
                            alert(response.message);
                            // Refresh the table
                            $('#tenants-list').DataTable().ajax.reload();
                        }
                    },
                    error: function(xhr) {
                        alert('{{ __('An error occurred while deleting the payment.')}}');
                    }
                });
            }
        }
    </script>

@endpush
