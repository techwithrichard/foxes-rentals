@extends('layouts.main')

@section('content')

    <div class="nk-content ">
        <div class="container-fluid ">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head nk-block-head-sm">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Payment Proof')}}</h3>
                                <div class="nk-block-des text-soft">
                                    <ul class="list-inline">
                                                                                <li>{{ __('Submitted By')}}: <span class="text-base">KID000844</span></li>
                                                                                <li>{{ __('Submitted At')}}: <span class="text-base">18 Dec, 2019 01:02 PM</span></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.payments-proof.index') }}"></x-back_link>
                                  
                          
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->


                    @if(session()->has('success'))
                        <div class="nk-block">
                            <div class="alert alert-success">
                                <strong>{{ session('success') }}</strong>
                            </div>
                        </div>

                    @endif
                    <div class="nk-block">
                        <div class="row gy-5">
                            <div class="col-lg-{{$proof->status==\App\Enums\PaymentProofStatusEnum::PENDING?'6':'10'}}">

                                <div class="card card-bordered">
                                    <ul class="data-list is-compact">
                                        <li class="data-item">
                                            <div class="data-col">
                                                <div class="data-label">{{ __('Submitted By')}}</div>
                                                <div class="data-value">{{ $proof->tenant->name }}</div>
                                            </div>
                                        </li>
                                        <li class="data-item">
                                            <div class="data-col">
                                                <div class="data-label">{{ __('Submitted At')}}</div>
                                                <div
                                                    class="data-value">{{ $proof->created_at->format('d M Y, h:i A') }}</div>
                                            </div>
                                        </li>
                                        <li class="data-item">
                                            <div class="data-col">
                                                <div class="data-label">{{ __('Status')}}</div>
                                                <div class="data-value">

                                                    @switch($proof->status)
                                                        @case(\App\Enums\PaymentProofStatusEnum::PENDING)
                                                        <span class="badge  badge-dim badge-sm bg-outline-warning">{{ __('Pending')}}</span>
                                                        @break
                                                        @case(\App\Enums\PaymentProofStatusEnum::APPROVED)
                                                        <span class="badge  badge-dim badge-sm bg-outline-success">{{ __('Approved')}}</span>
                                                        @break
                                                        @case(\App\Enums\PaymentProofStatusEnum::REJECTED)
                                                        <span class="badge  badge-dim badge-sm bg-outline-danger">{{ __('Rejected')}}</span>
                                                        @break
                                                        @default
                                                        <span class="badge badge-dim badge-sm bg-outline-warning">{{ __('Pending')}}</span>

                                                    @endswitch

                                                </div>
                                            </div>
                                        </li>

                                        <li class="data-item">
                                            <div class="data-col">
                                                <div class="data-label">{{ __('Remarks')}}</div>
                                                <div class="data-value">
                                                    {{ $proof->remarks }}
                                                </div>
                                            </div>
                                        </li>
                                        <li class="data-item">
                                            <div class="data-col">
                                                <div class="data-label">{{ __('Amount Paid')}}</div>
                                                <div class="data-value">
                                                    {{ setting('currency_symbol') . ' ' . number_format($proof->amount, 2) }}
                                                </div>
                                            </div>
                                        </li>
                                        <li class="data-item">
                                            <div class="data-col">
                                                <div class="data-label">{{ __('Payment Method')}}</div>
                                                <div class="data-value">{{ $proof->payment_method }}</div>
                                            </div>
                                        </li>
                                        <li class="data-item">
                                            <div class="data-col">
                                                <div class="data-label">{{ __('Reference Number')}}</div>
                                                <div class="data-value">{{ $proof->reference_number }}</div>
                                            </div>
                                        </li>

                                        <li class="data-item">
                                            <div class="data-col">
                                                <div class="data-label">{{ __('View Receipt')}}</div>
                                                <div class="data-value">
                                                    <em class="icon ni ni-download"></em>
                                                    <a href="" class="">{{ __('Download')}}</a>
                                                </div>
                                            </div>
                                        </li>
                                    </ul>
                                </div><!-- .card -->

                            </div><!-- .col -->
                            <div class="col-lg-6">
                                @if($proof->status==\App\Enums\PaymentProofStatusEnum::PENDING)
                                    @livewire('admin.payment.manage-payment-proof-component',['proof' => $proof])
                                @endif
                            </div><!-- .col -->
                        </div><!-- .row -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>

@endsection
