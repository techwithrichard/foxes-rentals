@extends('layouts.landlord_layout')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Invoice')}} <strong
                                        class="text-primary small">#{{$invoice->invoice_id}}</strong></h3>
                                <div class="nk-block-des text-soft">
                                    <ul class="list-inline">
                                        <li>Invoice Date: <span
                                                class="text-base">{{ $invoice->invoice_date?->format('M d,Y') }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                            <x-back_link href="{{ route('landlord.invoices.index') }}"></x-back_link>
                                  
                     
                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block">
                        <div class="invoice">
                            <div class="invoice-action">
                                <a class="btn btn-icon btn-lg btn-white btn-dim btn-outline-primary"
                                   href="{{ route('landlord.invoices.print',$invoice->id) }}" target="_blank"><em
                                        class="icon ni ni-printer-fill"></em></a>
                            </div><!-- .invoice-actions -->
                            <div class="invoice-wrap">
                                <div class="invoice-brand text-center">
                                    <h3 class="title">{{__('LANDLORD INVOICE')}}</h3>

                                </div>
                                <div class="invoice-head">
                                    <div class="invoice-contact">
                                        <div class="invoice-contact-info">
                                            <h4 class="title">{{ $invoice->landlord->name }}</h4>
                                            <ul class="list-plain">
                                                <li>
                                                    <span>
                                                        {{ $invoice->landlord->email }}<br>
                                                        {{ $invoice->landlord->phone }}<br>
                                                        {{ $invoice->landlord->address }}
                                                    </span>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="invoice-desc">
                                        <ul class="list-plain">
                                            <li class="invoice-id my-0 py-0">
                                                <span>{{ __('Invoice ID')}}</span>:<span>{{ $invoice->invoice_id }}</span>
                                            </li>
                                            <li class="invoice-date my-0 py-0">
                                                <span>{{ __('Invoice Date')}}</span>:<span>{{ $invoice->invoice_date?->format('M d,Y') }}</span>
                                            </li>
                                            <li class="invoice-date my-0 py-0">
                                                <span>{{ __('Due Date')}}</span>:<span>{{ $invoice->due_date?->format('M d,Y') }}</span>
                                            </li>
                                            <li class="invoice-date my-0 py-0">
                                                <span>{{ __('Property')}}</span>:<span>{{ $invoice->property?->name??'-' }}</span>
                                            </li>
                                            <li class="invoice-date my-0 py-0">
                                                <span>{{ __('House')}}</span>:<span>{{ $invoice->house?->name??'-' }}</span>
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
                                            @foreach($invoice->items as $item)
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
                                                <td>{{ __('Grand Total')}}
                                                    : {{ number_format($invoice->items->sum('cost'),2) }}</td>
                                            </tr>
                                            </tfoot>
                                        </table>
                                        <div class="nk-notes ff-italic fs-12px text-soft">
                                            {{ __('Invoice was created on a computer and is valid without the signature and
                                            seal.')}}
                                        </div>
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
