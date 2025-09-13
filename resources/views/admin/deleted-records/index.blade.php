@extends('layouts.main')

@section('content')
    <div class="nk-content">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Deleted Records')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <p>{{ __('Manage deleted houses, leases, and tenants. Restore or permanently delete records.')}}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="nk-block">
                        <div class="row g-gs">
                            <!-- Houses Card -->
                            <div class="col-md-4">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('Deleted Houses')}}</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <span class="text-soft">{{ __('Total Deleted Houses')}}</span>
                                                    <h4 class="text-primary">{{ \App\Models\House::onlyTrashed()->count() }}</h4>
                                                </div>
                                                <div class="text-primary">
                                                    <em class="icon ni ni-home-fill" style="font-size: 2rem;"></em>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('admin.deleted-records.houses') }}" class="btn btn-outline-primary btn-sm">
                                                    {{ __('View Deleted Houses')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Leases Card -->
                            <div class="col-md-4">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('Deleted Leases')}}</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <span class="text-soft">{{ __('Total Deleted Leases')}}</span>
                                                    <h4 class="text-warning">{{ \App\Models\Lease::onlyTrashed()->count() }}</h4>
                                                </div>
                                                <div class="text-warning">
                                                    <em class="icon ni ni-file-text-fill" style="font-size: 2rem;"></em>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('admin.deleted-records.leases') }}" class="btn btn-outline-warning btn-sm">
                                                    {{ __('View Deleted Leases')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Tenants Card -->
                            <div class="col-md-4">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="card-title-group">
                                            <div class="card-title">
                                                <h6 class="title">{{ __('Deleted Tenants')}}</h6>
                                            </div>
                                        </div>
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <span class="text-soft">{{ __('Total Deleted Tenants')}}</span>
                                                    <h4 class="text-danger">{{ \App\Models\User::onlyTrashed()->whereHas('roles', function($query) { $query->where('name', 'tenant'); })->count() }}</h4>
                                                </div>
                                                <div class="text-danger">
                                                    <em class="icon ni ni-user-fill" style="font-size: 2rem;"></em>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('admin.deleted-records.tenants') }}" class="btn btn-outline-danger btn-sm">
                                                    {{ __('View Deleted Tenants')}}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Information Card -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card card-bordered">
                                    <div class="card-inner">
                                        <div class="alert alert-info">
                                            <h6>{{ __('Important Information')}}</h6>
                                            <ul class="mb-0">
                                                <li>{{ __('Deleted records are moved to this section instead of being permanently removed')}}</li>
                                                <li>{{ __('You can restore deleted records to bring them back to active status')}}</li>
                                                <li>{{ __('Only administrators can permanently delete records from the system')}}</li>
                                                <li>{{ __('Restored records will reappear in their original sections')}}</li>
                                            </ul>
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
