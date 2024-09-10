@extends('layouts.main')

@section('content')
    <div class="nk-content ">
        <div class="container-fluid">
            <div class="nk-content-inner">
                <div class="nk-content-body">
                    <div class="nk-block-head">
                        <div class="nk-block-between g-3">
                            <div class="nk-block-head-content">
                                <h3 class="nk-block-title page-title">{{ __('Rental Invoice')}} <strong
                                        class="text-primary small">#{{ $invoice->invoice_id }}</strong>
                                </h3>
                                <div class="nk-block-des text-soft">
                                    <ul class="list-inline">
                                        <li>Created At: <span
                                                class="text-base">{{ $invoice->created_at->toDayDateTimeString() }}</span>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                            <div class="nk-block-head-content">
                                <x-back_link href="{{ route('admin.rent-invoice.index') }}"></x-back_link>


                            </div>
                        </div>
                    </div><!-- .nk-block-head -->
                    <div class="nk-block">
                        <div class="invoice">
                            <div class="invoice-action">
                                <a class="btn btn-icon btn-lg btn-white btn-dim btn-outline-primary"
                                   href="{{ route('admin.rent-invoice.print',$invoice->id) }}" target="_blank"><em
                                        class="icon ni ni-printer-fill"></em></a>
                            </div><!-- .invoice-actions -->
                            <div class="invoice-wrap">
                                <div class="invoice-brand text-center">
                                    <img src="./images/logo-dark.png" srcset="./images/logo-dark2x.png 2x" alt="">
                                </div>
                                <div class="invoice-head">
                                    <div class="invoice-contact">
                                        <span class="overline-title">{{ __('Invoice To')}}</span>
                                        <div class="invoice-contact-info">
                                            <h4 class="title">{{ $invoice->tenant?->name }}</h4>
                                            <ul class="list-plain">
                                                <li>
                                                    <span>{{ $invoice->tenant?->email }}<br>
                                                        {{ $invoice->tenant?->phone }}<br>
                                                        {{ $invoice->tenant?->address }}
                                                    </span>
                                                </li>

                                            </ul>
                                        </div>
                                    </div>
                                    <div class="invoice-desc">
                                        <h3 class="title">{{ __('Invoice')}}</h3>
                                        <ul class="list-plain">
                                            <li class="invoice-id">
                                                <span>{{ __('Invoice ID')}}</span>:<span>{{ $invoice->invoice_id }}</span>
                                            </li>
                                            <li class="invoice-date">
                                                <span>{{ __('Date')}}</span>:<span>{{ $invoice->created_at->format('M d,Y') }}</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div><!-- .invoice-head -->
                                <div class="invoice-bills">
                                    <div class="table-responsive">
                                        <table class="table table-striped datatable-wrap">
                                            <thead>
                                            <tr>
                                                <th class="w-150px">{{ __('Item ID')}}</th>
                                                <th class="w-60">{{ __('Description')}}</th>
                                                <td></td>

                                                <th>{{ __('Amount')}}</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <tr>
                                                <td>1</td>
                                                <td>{{ __('Rent payment for')}} {{ $invoice->property?->name??'' }}
                                                    ,{{ $invoice->house?->name??'' }}
                                                </td>
                                                <td></td>

                                                <td>
                                                    {{ setting('currency_symbol').' '.number_format($invoice->amount,2) }}
                                                </td>
                                            </tr>

                                            @forelse($invoice->bills as $item)
                                                <tr>
                                                    <td>{{ $loop->iteration+1 }}</td>
                                                    <td>{{ $item['name'] }}</td>
                                                    <td></td>
                                                    <td>{{ setting('currency_symbol').' '.number_format($item['amount'],2) }}</td>
                                                </tr>

                                            @empty

                                            @endforelse


                                            </tbody>
                                            <tfoot>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td>
                                                    {{ __('Sub Total:')}} {{ setting('currency_symbol').' '.number_format(($invoice->amount+$invoice->bills_amount),2) }}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="3"></td>
                                                <td>
                                                    {{ __('Paid Amount:')}} {{ setting('currency_symbol').' '.number_format($invoice->paid_amount,2) }}
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="3"></td>
                                                <td>
                                                    {{ __('Total Due:')}} {{ setting('currency_symbol').' '.number_format($invoice->balance_due,2) }}
                                                </td>
                                            </tr>


                                            </tfoot>
                                        </table>

                                    </div>
                                </div><!-- .invoice-bills -->

                                <table class="table datatable-wrap mt-4">
                                    <thead class="table-light">
                                    <tr>
                                        <th colspan="4">{{ __('Transactions')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="col">{{ __('Transaction Date')}}</th>
                                        <th scope="col">{{ __('Gateway')}}</th>
                                        <th scope="col">{{ __('Transaction ID')}}</th>
                                        <th scope="col">{{ __('Amount')}}</th>
                                    </tr>

                                    @forelse($invoice->verified_payments as $transaction)
                                        <tr>
                                            <td>{{ $transaction->paid_at->format('M d,Y') }}</td>
                                            <td>{{ $transaction->payment_method }}</td>
                                            <td>{{ $transaction->reference_number }}</td>
                                            <td>{{ setting('currency_symbol').' '.number_format($transaction->amount,2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">{{ __('No transactions found')}}</td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>
                                <table class="table datatable-wrap mt-4">
                                    <thead class="table-light">
                                    <tr>
                                        <th colspan="4">{{ __('Pending Payments')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="col">{{ __('Transaction Date')}}</th>
                                        <th scope="col">{{ __('Gateway')}}</th>
                                        <th scope="col">{{ __('Transaction ID')}}</th>
                                        <th scope="col">{{ __('Amount')}}</th>
                                    </tr>

                                    @forelse($invoice->pending_payments as $pending_payment)
                                        <tr>
                                            <td>{{ $pending_payment->paid_at->format('M d,Y') }}</td>
                                            <td>{{ $pending_payment->payment_method }}</td>
                                            <td>{{ $pending_payment->reference_number }}</td>
                                            <td>{{ setting('currency_symbol').' '.number_format($pending_payment->amount,2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">{{ __('No pending payments found')}}</td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>

                                <table class="table datatable-wrap mt-4">
                                    <thead class="table-light">
                                    <tr>
                                        <th colspan="4">{{ __('Cancelled Payments')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <th scope="col">{{ __('Transaction Date')}}</th>
                                        <th scope="col">{{ __('Gateway')}}</th>
                                        <th scope="col">{{ __('Transaction ID')}}</th>
                                        <th scope="col">{{ __('Amount')}}</th>
                                    </tr>

                                    @forelse($invoice->cancelled_payments as $cancelled_payment)
                                        <tr>
                                            <td>{{ $cancelled_payment->paid_at->format('M d,Y') }}</td>
                                            <td>{{ $cancelled_payment->payment_method }}</td>
                                            <td>{{ $cancelled_payment->reference_number }}</td>
                                            <td>{{ setting('currency_symbol').' '.number_format($cancelled_payment->amount,2) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4">{{ __('No cancelled payments found')}}</td>
                                        </tr>
                                    @endforelse

                                    </tbody>
                                </table>

                            </div><!-- .invoice-wrap -->
                        </div><!-- .invoice -->
                    </div><!-- .nk-block -->
                </div>
            </div>
        </div>
    </div>

@endsection
