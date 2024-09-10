@extends('layouts.main')

@section('content')

    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Activity Logs')}}</h3>
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


                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <div class="row mb-4">
                                    <div class="col-md-4 me-auto">
                                        <div class="form-group">

                                            <div class="form-control-wrap focused">
                                                <div class="form-icon form-icon-left">
                                                    <em class="icon ni ni-calendar"></em>
                                                </div>
                                                <input type="text" class="form-control date-picker"
                                                       data-date-format="yyyy-mm-dd"
                                                       id="date-filter"
                                                       placeholder="Filter by date">
                                            </div>

                                        </div>


                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" id="searchInput" placeholder="{{ __('Search')}}">
                                    </div>
                                </div>


                                <table
                                    class="datatable table datatable-wrap dt-responsive nowrap nk-tb-list nk-tb-ulist is-compact"
                                    id="tenants-list">

                                    {{--                                <table class="datatable nowrap" id="tenants-list">--}}
                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th data-priority="2" class="nk-tb-col">
                                            #
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Subject')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Description')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Performed By')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Performed On')}}</span>
                                        </th>
                                        <th class="nk-tb-col none" data-class="none">
                                            <span class="sub-text">{{ __('Changed Attributes')}}</span>
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
            const dom_normal = '<"row justify-between g-2"<"col-7 col-sm-4 text-start"><"col-5 col-sm-8 text-end"<"datatable-filter"<"d-flex justify-content-end g-2"l>>>><"datatable-wrap my-3"t><"row align-items-center"<"col-7 col-sm-12 col-md-9"p><"col-5 col-sm-12 col-md-3 text-start text-md-end"i>>';

            let oTable = $("#tenants-list").DataTable(
                {
                    processing: true,
                    serverSide: true,
                    dom: '<"top">rt<"d-flex justify-content-between align-items-center"ipl><"clear">',
                    // "bInfo": false,
                    "bLengthChange": true,
                    ajax: {
                        url: '{!! route('admin.activity-log.index') !!}',
                        data: function (d) {
                            d.date_filter = $("#date-filter").val();
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
                        // $(row).find('td:eq(8)').addClass('nk-tb-col nk-tb-col-tools');
                        // $(row).find('td:eq(5)').addClass('nk-tb-col');
                        // $(row).find('td:eq(6)').addClass('nk-tb-col');
                    },
                    columns: [
                        {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                        {data: 'subject', name: 'subject', searchable: true},
                        {data: 'description', name: 'description', searchable: true},
                        {data: 'causer', name: 'causer.name', searchable: true},
                        {data: 'created_at', name: 'created_at', searchable: true, orderable: false},
                        {data: 'properties', name: 'properties', searchable: false, orderable: false},
                        // {data: 'actions', name: 'actions', orderable: false, searchable: false},
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

            //when #searchInput is changed, search
            $('#searchInput').on('keyup', function () {
                oTable.search(this.value).draw();
            });

            //when #date-filter is changes,filter table
            $('#date-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });

        });
    </script>


@endpush
