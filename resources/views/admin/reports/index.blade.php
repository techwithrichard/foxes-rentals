@extends('admin.layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="nk-block nk-block-lg">
    <div class="nk-block-head">
        <div class="nk-block-head-content">
            <h4 class="nk-block-title">Reports Dashboard</h4>
            <p class="text-muted">Generate and view various system reports</p>
        </div>
    </div>
    
    <div class="row g-gs">
        <!-- Financial Reports -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-3">
                        <div class="card-title">
                            <h6 class="title">Financial Reports</h6>
                            <p class="text-muted">Income and expense reports</p>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.reports.landlord_income') }}" class="btn btn-outline-primary btn-sm mb-2">
                            <em class="icon ni ni-money"></em> Landlord Income
                        </a>
                        <a href="{{ route('admin.reports.property_income') }}" class="btn btn-outline-primary btn-sm mb-2">
                            <em class="icon ni ni-building"></em> Property Income
                        </a>
                        <a href="{{ route('admin.reports.company_income') }}" class="btn btn-outline-primary btn-sm mb-2">
                            <em class="icon ni ni-briefcase"></em> Company Income
                        </a>
                        <a href="{{ route('admin.reports.landlord_expenses') }}" class="btn btn-outline-danger btn-sm mb-2">
                            <em class="icon ni ni-money-cut"></em> Landlord Expenses
                        </a>
                        <a href="{{ route('admin.reports.company_expenses') }}" class="btn btn-outline-danger btn-sm">
                            <em class="icon ni ni-briefcase"></em> Company Expenses
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Operational Reports -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-3">
                        <div class="card-title">
                            <h6 class="title">Operational Reports</h6>
                            <p class="text-muted">Property and lease management</p>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <a href="{{ route('admin.reports.outstanding_payments') }}" class="btn btn-outline-warning btn-sm mb-2">
                            <em class="icon ni ni-clock"></em> Outstanding Payments
                        </a>
                        <a href="{{ route('admin.reports.expiring_leases') }}" class="btn btn-outline-info btn-sm mb-2">
                            <em class="icon ni ni-calendar"></em> Expiring Leases
                        </a>
                        <a href="{{ route('admin.reports.maintenance') }}" class="btn btn-outline-secondary btn-sm mb-2">
                            <em class="icon ni ni-tools"></em> Maintenance Reports
                        </a>
                        <a href="{{ route('admin.reports.occupancy') }}" class="btn btn-outline-success btn-sm">
                            <em class="icon ni ni-home"></em> Occupancy Reports
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="col-md-6 col-lg-4">
            <div class="card h-100">
                <div class="card-inner">
                    <div class="card-title-group align-start mb-3">
                        <div class="card-title">
                            <h6 class="title">Quick Stats</h6>
                            <p class="text-muted">Overview metrics</p>
                        </div>
                    </div>
                    <div class="d-flex flex-column">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <h6 class="mb-1">{{ \App\Models\Property::count() }}</h6>
                                        <small class="text-muted">Properties</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <h6 class="mb-1">{{ \App\Models\Lease::where('status', 'active')->count() }}</h6>
                                        <small class="text-muted">Active Leases</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <h6 class="mb-1">{{ \App\Models\Tenant::count() }}</h6>
                                        <small class="text-muted">Tenants</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="card bg-light">
                                    <div class="card-body text-center p-2">
                                        <h6 class="mb-1">{{ \App\Models\Invoice::where('status', 'pending')->count() }}</h6>
                                        <small class="text-muted">Pending Invoices</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
