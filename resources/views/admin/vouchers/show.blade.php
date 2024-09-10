@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Voucher')}} <strong
                                        class="text-primary small">#{{$voucher->voucher_id}}</strong></h3>
                                <div class="nk-block-des text-soft">
                                    <ul class="list-inline">
                                        <li>{{ __('Voucher Date')}}: <span
                                                class="text-base">{{ $voucher->voucher_date->format('M d,Y') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('admin.vouchers.index') }}"></x-back_link>
                               
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block">
                        <div class="invoice">
                            <div class="invoice-action">
                                <a class="btn btn-icon btn-lg btn-white btn-dim btn-outline-primary"
                                   href="{{ route('admin.vouchers.print',$voucher->id) }}" target="_blank"><em
                                        class="icon ni ni-printer-fill"></em></a>
                            </div><!-- .invoice-actions -->
                            <div class="invoice-wrap">
                                <div class="invoice-brand text-center">
                                    <h3 class="title">{{$voucher->type}} {{ __('VOUCHER')}}</h3>

                                </div>
                                <div class="invoice-head">
                                    <div class="invoice-contact">
                                        <div class="invoice-contact-info">
                                            <h4 class="title">{{ $voucher->landlord->name }}</h4>
                                            <ul class="list-plain">
                                                <li>
                                                    <span>
                                                        {{ $voucher->landlord->email }}<br>
                                                        {{ $voucher->landlord->phone }}<br>
                                                        {{ $voucher->landlord->address }}
                                                    </span>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="invoice-desc">
                                        <ul class="list-plain">
                                            <li class="invoice-id my-0 py-0">
                                                <span>{{ __('Voucher ID')}}</span>:<span>{{ $voucher->voucher_id }}</span></li>
                                            <li class="invoice-date my-0 py-0">
                                                <span>{{ __('Voucher Date')}}</span>:<span>{{ $voucher->voucher_date->format('M d,Y') }}</span>
                                            </li>
                                            <li class="invoice-date my-0 py-0">
                                                <span>{{ __('Property')}}</span>:<span>{{ $voucher->property?->name??'-' }}</span>
                                            </li>
                                            <li class="invoice-date my-0 py-0">
                                                <span>{{ __('House')}}</span>:<span>{{ $voucher->house?->name??'-' }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div><!-- .invoice-head -->
                                <div class="invoice-bills">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                            <tr>
                                                <th class="w-50px">#</th>
                                                <th class="w-60">{{ __('Description')}}</th>
                                                <th>{{ __('Qty')}}</th>
                                                <th>{{ __('Amount')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($voucher->items as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ $item->description }}</td>
                                                    <td>{{ $item->quantity }}</td>
                                                    <td>{{ number_format($item->cost,2) }}</td>
                                                </tr>
                                            @endforeach

                                            </tbody>
                                            <tfoot>

                                            <tr>
                                                <td colspan="3"></td>
                                                <td>{{ __('Grand Total')}} : {{ number_format($voucher->items->sum('cost'),2) }}</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                       
                                    </div>
                                </div><!-- .invoice-bills -->
                            </div><!-- .invoice-wrap -->
                        </div><!-- .invoice -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>

@endsection
