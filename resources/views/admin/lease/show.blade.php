@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">

                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Lease Details')}} </h3>
                            </div>


                            <div class="nk-block-head-content">
                                <x-back_link href="{{ route('admin.leases.index') }}"></x-back_link>


                            </div>
                        </div>
                    </div><!-- .nk-block-head -->


                    <div class="nk-block nk-block-lg">
                        <div class="card card-bordered">
                            <div class="card-content">
                                <ul class="nav nav-tabs nav-tabs-card">
                                    <li class="nav-item">
                                        <a class="nav-link active" data-bs-toggle="tab" href="#tabDetails">
                                            <span>{{ __('Lease Details')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tabBills">

                                            <span>{{ __('Lease Bills')}}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link" data-bs-toggle="tab" href="#tabDocuments">
                                            <span>{{ __('Documents')}}</span></a>
                                    </li>


                                </ul><!-- .nav-tabs -->
                                <div class="tab-content">
                                    <div class="tab-pane active" id="tabDetails">
                                        <div class="card-inner">
                                            <div class="nk-block">
                                                <div class="nk-block-head">
                                                    <div class="nk-block-between d-flex justify-content-between">
                                                        <div class="nk-block-head-content">
                                                            <h4 class="nk-block-title">{{ __('Lease Information')}}</h4>

                                                        </div>
                                                        <div class="nk-tab-actions me-n1">
                                                            @can('edit lease')
                                                                <a class="btn btn-icon btn-trigger"
                                                                   href="{{ route('admin.leases.edit',$lease->id) }}"><em
                                                                        class="icon ni ni-edit"></em></a>
                                                            @endcan
                                                        </div>
                                                    </div>
                                                </div><!-- .nk-block-head -->
                                                <div class="profile-ud-list">
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span class="profile-ud-label">{{ __('Lease ID')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ $lease->lease_id}}</span>
                                                        </div>
                                                    </div>

                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span class="profile-ud-label">{{ __('Tenant')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ $lease->tenant?->name }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span class="profile-ud-label">{{ __('Building')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ $lease->property?->name }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span class="profile-ud-label">{{ __('House')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ $lease->house?->name }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Recurring Rent')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ setting('currency_symbol') . ' ' . number_format($lease->rent, 2)}}</span>
                                                        </div>
                                                    </div>


                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Recurring Bills')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ setting('currency_symbol') . ' ' . number_format($lease->bills->sum('amount'), 2) }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span class="profile-ud-label">{{ __('Start Date')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ $lease->start_date->format('d M Y') }}</span>
                                                        </div>
                                                    </div>
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span class="profile-ud-label">{{ __('End Date')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ $lease->end_date?->format('d M Y')??'N/a' }}</span>
                                                        </div>
                                                    </div>
                                                </div><!-- .profile-ud-list -->
                                            </div><!-- .nk-block -->


                                            <div class="nk-block">
                                                <div class="nk-block-head nk-block-head-line">
                                                    <h6 class="title overline-title text-base">{{ __('Billing Cycle')}}</h6>
                                                </div><!-- .nk-block-head -->
                                                <div class="profile-ud-list">
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Billing Cycle')}}</span>
                                                            <span class="profile-ud-value">
                                                                {{ $lease->rent_cycle . ' ' . ($lease->rent_cycle > 1 ? __('months') : __('month')) }}

                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Next Billing')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ $lease->next_billing_date?->format('d M,Y')??'N/a' }}</span>
                                                        </div>
                                                    </div>

                                                </div><!-- .profile-ud-list -->
                                            </div><!-- .nk-block -->

                                            <div class="nk-block">
                                                <div class="nk-block-head nk-block-head-line">
                                                    <h6 class="title overline-title text-base">{{ __('Termination Notice')}}</h6>
                                                </div><!-- .nk-block-head -->
                                                <div class="profile-ud-list">

                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Termination Status')}}</span>
                                                            <span class="profile-ud-value">
                                                    @if($lease->termination_status !=null)
                                                                    <span
                                                                        class="badge bg-danger">{{ __('Terminated')}}</span>
                                                                @else
                                                                    <span
                                                                        class="badge badge-dim bg-success">{{ __('Still Active')}}</span>
                                                                @endif
                                                    </span>
                                                        </div>
                                                    </div>
                                                    <div class="profile-ud-item">
                                                        <div class="profile-ud wider">
                                                            <span
                                                                class="profile-ud-label">{{ __('Terminated On')}}</span>
                                                            <span
                                                                class="profile-ud-value">{{ $lease->deleted_at?$lease->deleted_at->format('d M,Y'):'N/a' }}</span>
                                                        </div>
                                                    </div>
                                                </div><!-- .profile-ud-list -->
                                            </div><!-- .nk-block -->

                                        </div>
                                    </div>

                                    <div class="tab-pane" id="tabBills">
                                        <div class="card-inner ">
                                            <div class="nk-block">
                                                <div class="card card-bordered">
                                                    <div class="nk-tb-list nk-tb-ulist is-compact">
                                                        <div class="nk-tb-item nk-tb-head">
                                                            <div class="nk-tb-col"><span class="fw-bold">#</span></div>

                                                            <div class="nk-tb-col"><span
                                                                    class="fw-bold">{{ __('Bill Name')}}</span></div>
                                                            <div class="nk-tb-col"><span
                                                                    class="fw-bold">{{ __('Bill Amount')}}</span></div>
                                                            <div class="nk-tb-col nk-tb-col-tools text-end"></div>
                                                        </div><!-- .nk-tb-item -->

                                                        @forelse($lease->bills as $bill)
                                                            <div class="nk-tb-item">
                                                                <div class="nk-tb-col">
                                                                    <span>{{ $loop->iteration }}</span>
                                                                </div>
                                                                <div class="nk-tb-col">
                                                                    <span>{{ $bill->name }}</span>
                                                                </div>
                                                                <div class="nk-tb-col">
                                                                    <span>{{ setting('currency_symbol') . ' ' . number_format($bill->amount, 2) }}</span>
                                                                </div>
                                                                <div class="nk-tb-col nk-tb-col-tools">
                                                                    <ul class="nk-tb-actions gx-2">
                                                                        <li class="nk-tb-action">
                                                                            <a href="{{ route('admin.leases.edit',$lease->id) }}"
                                                                               class="btn btn-sm btn-icon btn-trigger"
                                                                               data-bs-toggle="tooltip"
                                                                               data-bs-placement="top" title=""
                                                                               data-bs-original-title="Edit">
                                                                                <em class="icon ni ni-edit-fill"></em>
                                                                            </a>
                                                                        </li>

                                                                    </ul>

                                                                </div>
                                                            </div>

                                                        @empty

                                                        @endforelse

                                                    </div><!-- .nk-tb-list -->
                                                </div><!-- .card -->
                                            </div>
                                        </div>

                                    </div>

                                    <div class="tab-pane" id="tabDocuments">
                                        @livewire('admin.lease.show-lease-documents-component',['leaseId'=>$lease->id])

                                    </div>


                                </div>

                                <!--card inner-->
                            </div><!-- .card-content -->
                        </div>
                        <!--card-->
                    </div>
                    <!--nk block lg-->
                </div>
            </div>
        </div>
    </div>

@endsection
