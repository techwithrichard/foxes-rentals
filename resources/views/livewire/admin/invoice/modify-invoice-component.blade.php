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

                                        </tbody>
                                        <tfoot>
                                        <tr>
                                            <td colspan="3"></td>
                                            <td>
                                                Total
                                                Rent {{ setting('currency_symbol').' '.number_format(($invoice->amount),2) }}
                                            </td>
                                        </tr>

                                        <tr>
                                            <td colspan="3"></td>
                                            <td>
                                                Total
                                                Bills {{ setting('currency_symbol').' '.number_format(($invoice->bills_amount),2) }}
                                            </td>
                                        </tr>

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
                                    <th colspan="4">{{ __('Invoice Extra Bills')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                <tr>
                                    <th scope="col">#</th>
                                    <th scope="col">Bill Description</th>
                                    <th scope="col">Amount</th>
                                    <th scope="col" class="text-end">Remove</th>
                                </tr>

                                @forelse($current_bills as $index=>$invoice_bill)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $invoice_bill['name'] }}</td>
                                        <td>{{ $invoice_bill['amount'] }}</td>
                                        <td class="text-end">
                                            <button
                                                onclick="return confirm('Delete invoice bill ?')|| event.stopImmediatePropagation();"
                                                class="btn btn-icon btn-sm btn-danger"
                                                wire:click="removeInvoiceBill('{{ $index }}')">
                                                <em class="icon ni ni-trash"></em>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4">
                                            No bills has been added to this invoice yet.
                                        </td>
                                    </tr>
                                @endforelse

                                </tbody>
                            </table>

                            <div class="card card-bordered">
                                <div class="card-inner">
                                    <div class="card-head">
                                        <h6 class="card-title">Add Invoice Bills</h6>
                                    </div>
                                    <hr/>
                                    <div class="row g-4">
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="full-name-1">
                                                    {{ __('Bill Description')}}
                                                </label>
                                                <div class="form-control-wrap">
                                                    <input type="text" wire:model.defer="bill_description"
                                                           class="form-control @error('bill_description') is-invalid @enderror"
                                                           id="full-name-1">

                                                    @error('bill_description')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="form-group">
                                                <label class="form-label" for="email-address-1">
                                                    {{ __('Amount')}}
                                                </label>
                                                <div class="form-control-wrap">
                                                    <input type="number" wire:model.defer="bill_amount"
                                                           class="form-control @error('bill_amount') is-invalid @enderror"
                                                           id="email-address-1">

                                                    @error('bill_amount')
                                                    <span class="invalid-feedback" role="alert">
                                                        <strong>{{ $message }}</strong>
                                                    </span>
                                                    @enderror
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="form-group">
                                                <x-button type="button" wire:click="addInvoiceBill"
                                                          class="btn btn-primary">
                                                    {{ __('Add Bill')}}
                                                </x-button>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div><!-- .invoice-wrap -->
                    </div><!-- .invoice -->
                </div><!-- .nk-block -->
            </div>
        </div>
    </div>
</div>
