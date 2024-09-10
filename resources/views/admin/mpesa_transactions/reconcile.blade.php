@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">
                                    Reconcile Mpesa Transaction
                                </h3>

                            </div><!-- .nk-block-head-content -->
                            <div class="nk-block-head-content">

                            </div><!-- .nk-block-head-content -->
                        </div><!-- .nk-block-between -->
                    </div><!-- .nk-block-head -->

                    <div class="nk-block">
                        <div class="row g-gs">
                            <div class="col-lg-12 col-xl-12 col-xxl-12">
                                <div class="card card-bordered">
                                    <div class="card-inner-group">
                                        <div class="card-inner">
                                            <div class="nk-block">
                                                <div class="overline-title-alt mb-2 mt-2">Transaction Details</div>
                                                <div class="profile-balance">
                                                    <div class="profile-balance-group gx-4">
                                                        <div class="profile-balance-sub">
                                                            <div class="profile-balance-amount">
                                                                <div
                                                                    class="number">{{ $transaction->TransAmount }}</div>
                                                            </div>
                                                            <div class="profile-balance-subtitle">Amount</div>
                                                        </div>
                                                        <div class="profile-balance-sub">
                                                        <span class="profile-balance-plus text-soft">

                                                        </span>
                                                            <div class="profile-balance-amount">
                                                                <div class="number">{{ $transaction->TransID }}</div>
                                                            </div>
                                                            <div class="profile-balance-subtitle">Trans ID</div>
                                                        </div>
                                                        <div class="profile-balance-sub">
                                                        <span class="profile-balance-plus text-soft">

                                                        </span>
                                                            <div class="profile-balance-amount">
                                                                <div class="number">{{ $transaction->FirstName }}</div>
                                                            </div>
                                                            <div class="profile-balance-subtitle">Paid By</div>
                                                        </div>
                                                        <div class="profile-balance-sub">
                                                        <span class="profile-balance-plus text-soft">

                                                        </span>
                                                            <div class="profile-balance-amount">
                                                                <div
                                                                    class="number">{{ $transaction->BillRefNumber }}</div>
                                                            </div>
                                                            <div class="profile-balance-subtitle">Reference</div>
                                                        </div>
                                                        <div class="profile-balance-sub">
                                                        <span class="profile-balance-plus text-soft">

                                                        </span>
                                                            <div class="profile-balance-amount">
                                                                <div class="number">
                                                                    {{ date('d-m-Y H:i:s', strtotime($transaction->TransTime)) }}
                                                                </div>
                                                            </div>
                                                            <div class="profile-balance-subtitle">Paid At</div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                        </div><!-- .card-inner -->

                                        @livewire('admin.invoice.reconcile-payment-component',
                                     ['transactionId' => $transaction->id,
                                     'transaction' => $transaction,
                                                        ])

                                    </div>

                                </div><!-- .card -->
                            </div><!-- .col -->
                        </div><!-- .row -->
                    </div><!-- .nk-block -->

                </div>
            </div>
        </div>
    </div>

@endsection

@push('css')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="{{ asset('assets/css/virtual-select.min.css') }}" rel="stylesheet">

    <style>
        .vscomp-toggle-button {
            border-radius: 0.375rem;
        }
    </style>
@endpush

@push('scripts')
    <script src="{{ asset('assets/js/virtual-select.min.js') }}"></script>
@endpush

