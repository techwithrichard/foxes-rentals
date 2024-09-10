@extends('layouts.main')


@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-content-wrap">
                        <div class="nk-block-head">
                            <div class="nk-block-between g-3">
                                <div class="nk-block-head-content">
                                    <h3 class="nk-block-title page-title">{{ __('Support Ticket')}}</h3>
                                    <div class="nk-block-des text-soft">
{{--                                        <p>{{ __('You have total')}} {{ $total_tickets }} {{ __('tickets.')}}</p>--}}
                                    </div>
                                </div><!-- .nk-block-head-content -->
                                <div class="nk-block-head-content">

                                </div><!-- .nk-block-head-content -->
                            </div><!-- .nk-block-between -->
                        </div><!-- .nk-block-head -->
                        <div class="nk-block">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="row mb-4 g-2">
                                        <div class="col-sm-3">
                                            <div class="form-control-wrap">
                                                <div class="form-control-select">


                                                    <select class="form-control" id="property-filter">
                                                        <option value="">{{ __('Filter Houses')}}</option>
                                                        @foreach($properties as $name => $key)
                                                            <option value="{{ $key }}">{{ $name }}</option>
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>


                                        </div>
                                        <div class="col-sm-2">
                                            <div class="form-control-wrap">
                                                <div class="form-control-select">


                                                    <select class="form-control" id="status-filter">
                                                        <option value="">{{ __('Ticket Status')}}</option>
                                                        <option
                                                            value="{{ \App\Enums\TicketStatusEnum::Open }}">{{ __('Open')}}</option>
                                                        <option
                                                            value="{{ \App\Enums\TicketStatusEnum::Closed }}">{{ __('Closed')}}</option>
                                                        <option
                                                            value="{{ \App\Enums\TicketStatusEnum::OnHold }}">{{ __('On Hold')}}</option>
                                                        <option
                                                            value="{{ \App\Enums\TicketStatusEnum::Pending }}">{{ __('Pending')}}</option>
                                                        <option
                                                            value="{{ \App\Enums\TicketStatusEnum::InProgress }}">{{ __('In Progress')}}</option>

                                                    </select>
                                                </div>
                                            </div>


                                        </div>

                                        <div class="col-md-4 col-lg-3 col-sm-6 ms-auto">
                                            <div class="form-control-wrap">
                                                <div class="form-icon form-icon-right"><em
                                                        class="icon ni ni-search"></em>
                                                </div>
                                                <input type="text" class="form-control" id="input-search"
                                                       placeholder="{{ __('Search')}} ...">
                                            </div>
                                        </div>
                                    </div>


                                    <table class="datatable datatable-wrap wrap table" id="tenants-list">
                                        <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>{{ __('Title')}}</th>
                                            <th>{{ __('Sent On')}}</th>
                                            <th>{{ __('Tenant')}}</th>
                                            <th>{{ __('Property')}}</th>
                                            <th>{{ __('Status')}}</th>
                                            <th class="text-end">{{ __('Actions')}}</th>
                                        </tr>
                                        </thead>

                                        <tbody>

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>


                    </div>
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
                    dom: '<"top">rt<"d-flex justify-content-between align-items-center"ipl><"clear">',
                    "bLengthChange": true,
                    ajax: {
                        url: '{!! route('admin.support-tickets.index') !!}',
                        data: function (d) {
                            d.property_filter = $("#property-filter").val();
                            d.status_filter = $("#status-filter").val();
                        }
                    },
                    autoWidth: false,
                    responsive: {
                        details: true,
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
                        $(row).find('td:eq(6)').addClass('nk-tb-col nk-tb-col-tools');
                    },
                    columns: [
                        {data: 'ticket_id', name: 'ticket_id', orderable: false, searchable: true},
                        {data: 'subject', name: 'subject', searchable: true},
                        {data: 'created_at', name: 'created_at', searchable: true},
                        {data: 'user', name: 'user.name', searchable: true},
                        {data: 'property', name: 'property.name', searchable: false, orderable: false},
                        {data: 'status', name: 'status', searchable: false},
                        {data: 'action', name: 'action', orderable: false, searchable: false},
                    ],
                    drawCallback: function () {
                        $(".dataTables_paginate > .pagination").addClass("mt-3")
                    }
                });

            Livewire.on('clear-files', function () {
                $('#submitTicketModal').modal('hide');
            })

            //when search is filled,search
            $('#input-search').on('keyup', function () {
                oTable.search(this.value).draw();
            });

            $('#property-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });

            $('#status-filter').on('change', function () {
                oTable.ajax.reload(null, false);
            });


        });
    </script>

@endpush
