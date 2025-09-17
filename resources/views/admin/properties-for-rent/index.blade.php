@extends('layouts.main')

@section('content')
<div class="nk-content">
    <div class="container-fluid">
        <div class="nk-content-inner">
            <div class="nk-content-body">
                <!-- Page Header -->
                <div class="nk-block-head nk-block-head-sm">
                    <div class="nk-block-between">
                        <div class="nk-block-head-content">
                            <h3 class="nk-block-title page-title">{{ __('Properties for Rent') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Manage rental properties, applications, and active rentals') }}</p>
                            </div>
                        </div>
                        <div class="nk-block-head-content">
                            <div class="toggle-wrap nk-block-tools-toggle">
                                <a href="#" class="btn btn-icon btn-trigger toggle-expand me-n1" data-target="pageMenu">
                                    <em class="icon ni ni-menu-alt-r"></em>
                                </a>
                                <div class="toggle-expand-content" data-content="pageMenu">
                                    <ul class="nk-block-tools g-3">
                                        <li>
                                            <a href="{{ route('admin.properties-for-rent.vacant') }}" class="btn btn-outline-primary">
                                                <em class="icon ni ni-home"></em>
                                                <span>{{ __('Vacant Properties') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.properties-for-rent.applications') }}" class="btn btn-outline-info">
                                                <em class="icon ni ni-file-text"></em>
                                                <span>{{ __('Applications') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.properties-for-rent.active-rentals') }}" class="btn btn-outline-success">
                                                <em class="icon ni ni-check-circle"></em>
                                                <span>{{ __('Active Rentals') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.properties.create') }}" class="btn btn-primary">
                                                <em class="icon ni ni-plus"></em>
                                                <span>{{ __('Add Property') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="nk-block">
                    <div class="row g-gs">
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Total Rental Properties') }}</h6>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Vacant Properties') }}</h6>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Active Rentals') }}</h6>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-6 col-lg-3">
                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-title-group align-start mb-2">
                                        <div class="card-title">
                                            <h6 class="title">{{ __('Pending Applications') }}</h6>
                                        </div>
                                    </div>
                                    <div class="card-amount">
                                        <span class="amount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Properties Table -->
                <div class="nk-block">
                    <div class="card card-bordered">
                        <div class="card-inner">
                            <div class="card-title-group">
                                <div class="card-title">
                                    <h5 class="title">{{ __('All Rental Properties') }}</h5>
                                </div>
                                <div class="card-tools">
                                    <div class="dropdown">
                                        <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                            <em class="icon ni ni-more-h"></em>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <ul class="link-list-opt no-bdr">
                                                <li><a href="#"><span>{{ __('Export Data') }}</span></a></li>
                                                <li><a href="#"><span>{{ __('Print Report') }}</span></a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Filters -->
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <select class="form-select" id="status_filter">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="1">{{ __('Occupied') }}</option>
                                        <option value="0">{{ __('Vacant') }}</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select class="form-select" id="landlord_filter">
                                        <option value="">{{ __('All Landlords') }}</option>
                                        @foreach($landlords as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <input type="text" class="form-control" id="search_input" placeholder="{{ __('Search properties...') }}">
                                </div>
                                <div class="col-md-3">
                                    <button type="button" class="btn btn-primary" id="apply_filters">
                                        <em class="icon ni ni-search"></em>
                                        <span>{{ __('Apply Filters') }}</span>
                                    </button>
                                </div>
                            </div>

                            <div class="nk-tb-list is-separate mb-3">
                                <table class="table" id="properties-table">
                                    <thead>
                                        <tr>
                                            <th>{{ __('#') }}</th>
                                            <th>{{ __('Property Name') }}</th>
                                            <th>{{ __('Status') }}</th>
                                            <th>{{ __('Tenant') }}</th>
                                            <th>{{ __('Rent Amount') }}</th>
                                            <th>{{ __('Address') }}</th>
                                            <th>{{ __('Landlord') }}</th>
                                            <th>{{ __('Actions') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Data will be loaded via DataTables -->
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
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#properties-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: "{{ route('admin.properties-for-rent.index') }}",
            data: function(d) {
                d.status_filter = $('#status_filter').val();
                d.landlord_filter = $('#landlord_filter').val();
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
            {data: 'name', name: 'name'},
            {data: 'status', name: 'status', orderable: false, searchable: false},
            {data: 'tenant', name: 'tenant'},
            {data: 'rent_amount', name: 'rent_amount'},
            {data: 'address', name: 'address'},
            {data: 'landlord', name: 'landlord'},
            {data: 'actions', name: 'actions', orderable: false, searchable: false},
        ],
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
        drawCallback: function () {
            $(".dataTables_paginate > .pagination").addClass("mt-3")
        }
    });

    // Apply filters
    $('#apply_filters').on('click', function() {
        table.draw();
    });

    // Search functionality
    $('#search_input').on('keyup', function() {
        table.search(this.value).draw();
    });
});
</script>
@endpush
