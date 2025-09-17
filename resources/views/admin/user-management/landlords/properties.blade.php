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
                            <h3 class="nk-block-title page-title">{{ __('Landlord Properties Management') }}</h3>
                            <div class="nk-block-des text-soft">
                                <p>{{ __('Manage properties owned by landlords') }}</p>
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
                                            <a href="{{ route('admin.landlords.create') }}" class="btn btn-primary">
                                                <em class="icon ni ni-plus"></em>
                                                <span>{{ __('Add Landlord') }}</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('admin.properties.create') }}" class="btn btn-outline-primary">
                                                <em class="icon ni ni-home"></em>
                                                <span>{{ __('Add Property') }}</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Landlords List -->
                <div class="nk-block">
                    <div class="row g-gs">
                        @forelse($landlords as $landlord)
                        <div class="col-lg-6 col-xl-4">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                            <em class="icon ni ni-user-circle text-primary" style="font-size: 1.5rem;"></em>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0">{{ $landlord->name }}</h6>
                                            <span class="text-muted small">{{ $landlord->email }}</span>
                                        </div>
                                        <div class="dropdown">
                                            <a href="#" class="dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                                <em class="icon ni ni-more-h"></em>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <ul class="link-list-opt no-bdr">
                                                    <li><a href="{{ route('admin.landlords.show', $landlord->id) }}"><em class="icon ni ni-eye"></em><span>{{ __('View Details') }}</span></a></li>
                                                    <li><a href="{{ route('admin.landlords.edit', $landlord->id) }}"><em class="icon ni ni-edit"></em><span>{{ __('Edit') }}</span></a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Property Statistics -->
                                    <div class="row g-2 mb-3">
                                        <div class="col-6">
                                            <div class="bg-light rounded-3 p-2 text-center">
                                                <h6 class="mb-0 text-primary">{{ $landlord->properties_count }}</h6>
                                                <small class="text-muted">{{ __('Properties') }}</small>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="bg-light rounded-3 p-2 text-center">
                                                <h6 class="mb-0 text-success">{{ $landlord->houses_count }}</h6>
                                                <small class="text-muted">{{ __('Units') }}</small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Recent Properties -->
                                    @if($landlord->properties->count() > 0)
                                    <div class="mb-3">
                                        <h6 class="mb-2">{{ __('Recent Properties') }}</h6>
                                        @foreach($landlord->properties as $property)
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="bg-info bg-opacity-10 rounded-2 p-1 me-2">
                                                <em class="icon ni ni-home text-info"></em>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="fw-medium">{{ $property->name }}</span>
                                                <br>
                                                <small class="text-muted">{{ $property->address ?? 'No address' }}</small>
                                            </div>
                                            <div class="text-end">
                                                <span class="badge bg-success">{{ $property->status ?? 'Active' }}</span>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @endif

                                    <!-- Actions -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.landlords.show', $landlord->id) }}" class="btn btn-outline-primary btn-sm flex-grow-1">
                                            <em class="icon ni ni-eye me-1"></em>
                                            {{ __('View') }}
                                        </a>
                                        <a href="{{ route('admin.user-management.landlords.payments', ['landlord' => $landlord->id]) }}" class="btn btn-outline-success btn-sm">
                                            <em class="icon ni ni-tranx me-1"></em>
                                            {{ __('Payments') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body text-center py-5">
                                    <div class="bg-light rounded-circle p-4 d-inline-block mb-3">
                                        <em class="icon ni ni-user-circle text-muted" style="font-size: 3rem;"></em>
                                    </div>
                                    <h5 class="mb-2">{{ __('No Landlords Found') }}</h5>
                                    <p class="text-muted mb-4">{{ __('There are no landlords in the system yet.') }}</p>
                                    <a href="{{ route('admin.landlords.create') }}" class="btn btn-primary">
                                        <em class="icon ni ni-plus me-2"></em>
                                        {{ __('Add First Landlord') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>

                <!-- Summary Statistics -->
                @if($landlords->count() > 0)
                <div class="nk-block">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title">{{ __('Summary Statistics') }}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-gs">
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center">
                                        <h4 class="text-primary mb-1">{{ $landlords->count() }}</h4>
                                        <span class="text-muted">{{ __('Total Landlords') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center">
                                        <h4 class="text-success mb-1">{{ $landlords->sum('properties_count') }}</h4>
                                        <span class="text-muted">{{ __('Total Properties') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center">
                                        <h4 class="text-info mb-1">{{ $landlords->sum('houses_count') }}</h4>
                                        <span class="text-muted">{{ __('Total Units') }}</span>
                                    </div>
                                </div>
                                <div class="col-md-3 col-sm-6">
                                    <div class="text-center">
                                        <h4 class="text-warning mb-1">{{ $landlords->where('properties_count', '>', 0)->count() }}</h4>
                                        <span class="text-muted">{{ __('Active Landlords') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
