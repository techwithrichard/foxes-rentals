@extends('layouts.main')

@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Deleted Houses')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>{{ __('Manage deleted houses. Restore or permanently delete records.')}}</p>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <div class="toggle-wrap nk-block-tools-toggle">
                                    <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                        <em class="icon ni ni-more-v"></em>
                                    </a>
                                    <div class="toggle-expand-content" data-content="pageMenu">
                                        <ul class="nk-block-tools g-3">
                                            <li>
                                                <a href="{{ route('admin.deleted-records.index') }}" class="btn btn-outline-primary">
                                                    <em class="icon ni ni-arrow-left"></em>
                                                    <span>{{ __('Back to Deleted Records')}}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="card card-bordered card-preview">
                            <div class="card-inner">
                                <table class="datatable table dt-responsive nowrap nk-tb-list nk-tb-ulist is-compact" id="deleted-houses-list">
                                    <thead>
                                    <tr class="nk-tb-item nk-tb-head">
                                        <th class="nk-tb-col nk-tb-col-check">
                                            #
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('House Name')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Property')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Type')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Rent')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Landlord')}}</span>
                                        </th>
                                        <th class="nk-tb-col">
                                            <span class="sub-text">{{ __('Deleted At')}}</span>
                                        </th>
                                        <th class="nk-tb-col nk-tb-col-tools text-end">
                                            <span class="sub-text">{{ __('Actions')}}</span>
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
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
        const dom_normal = '<"row justify-between g-2"<"col-7 col-sm-4 text-start"><"col-5 col-sm-8 text-end"<"datatable-filter"<"d-flex justify-content-end g-2"l>>>><"datatable-wrap my-3"t><"row align-items-center"<"col-7 col-sm-12 col-md-9"p><"col-5 col-sm-12 col-md-3 text-start text-md-end"i>>';

        var oTable = $("#deleted-houses-list").DataTable({
            processing: true,
            serverSide: true,
            dom: dom_normal,
            ajax: '{!! route('admin.deleted-records.houses') !!}',
            autoWidth: false,
            responsive: {
                details: {
                    renderer: function (api, rowIdx) {
                        var data = api.cells(rowIdx, ':hidden').eq(0).map(function (cell) {
                            var header = $(api.column(cell.column).header());
                            return '<tr>' +
                                '<td class="fw-bold">' + header.text() + ':' + '</td> ' +
                                '<td>' + api.cell(cell).data() + '</td>' +
                                '</tr>';
                        }).toArray().join('');
                        return data ? $('<table/>').append(data) : false;
                    }
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
                {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                {data: 'name', name: 'name', searchable: true},
                {data: 'property', name: 'property.name', searchable: true},
                {data: 'type', name: 'type', searchable: true},
                {data: 'rent', name: 'rent', searchable: false},
                {data: 'landlord', name: 'landlord.name', searchable: true},
                {data: 'deleted_at', name: 'deleted_at', searchable: false},
                {data: 'actions', name: 'actions', orderable: false, searchable: false},
            ],
            drawCallback: function () {
                $(".dataTables_paginate > .pagination").addClass("mt-3")
            }
        });
    });

    // Restore house function
    function restoreHouse(houseId) {
        if (confirm('Are you sure you want to restore this house?')) {
            $.ajax({
                url: '/admin/deleted-records/houses/' + houseId + '/restore',
                type: 'POST',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('House restored successfully');
                        $('#deleted-houses-list').DataTable().ajax.reload();
                    }
                },
                error: function() {
                    alert('Error restoring house');
                }
            });
        }
    }

    // Permanently delete house function
    function permanentlyDeleteHouse(houseId) {
        if (confirm('Are you sure you want to permanently delete this house? This action cannot be undone!')) {
            $.ajax({
                url: '/admin/deleted-records/houses/' + houseId + '/permanent-delete',
                type: 'DELETE',
                data: {
                    _token: $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        alert('House permanently deleted');
                        $('#deleted-houses-list').DataTable().ajax.reload();
                    }
                },
                error: function() {
                    alert('Error permanently deleting house');
                }
            });
        }
    }
</script>
@endpush
