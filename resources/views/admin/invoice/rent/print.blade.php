<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>Invoice</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            line-height: inherit;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        /*.invoice-box table tr td:nth-child(1) {*/
        /*    text-align: center;*/
        /*}*/

        /*.invoice-box table tr td:nth-child(2) {*/
        /*    text-align: right;*/
        /*}*/

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td {
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(3) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .invoice-box.rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .invoice-box.rtl table {
            text-align: right;
        }

        .invoice-box.rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td style="text-align: left;align-content: start;">
                            <h2>{{ __('Rental Invoice')}}</h2>
                        </td>

                        <td style="text-align: right;">
                            {{ __('Invoice')}} #: {{ $invoice->invoice_id }}<br/>
                            {{ __('Created')}}: {{ $invoice->created_at->format('F d,Y') }}<br/>

                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            {{ $invoice->tenant?->name }}<br/>
                            {{ $invoice->tenant?->email }}<br/>
                            {{ $invoice->tenant?->phone }}<br/>
                            {{ $invoice->tenant?->address }}
                        </td>

                        <td style="text-align: right;">
                            {{ setting('company_name') }}<br/>
                            {{ setting('company_email') }}<br/>
                            {{ setting('company_phone') }}<br/>
                            {{ setting('company_address') }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>


        <tr class="heading">
            <td>Item</td>

            <td style="text-align: right;">{{ __('Price')}}</td>
        </tr>

        <tr class="item">
            <td>{{ __('Rent')}}</td>
            <td style="text-align: right;">{{ setting('currency_symbol').' '.number_format($invoice->amount,2) }}</td>
        </tr>

        @foreach($invoice->bills as $item)
            <tr class="item">
                <td>{{ $item['name'] }}</td>
                <td style="text-align: right;">{{ setting('currency_symbol').' '.number_format($item['amount'],2) }}</td>
            </tr>

        @endforeach

        <tr class="item last">
            <td>&nbsp;</td>
            <td style="text-align: right;">&nbsp;</td>
        </tr>

        <tr class="total">
            <td></td>
            <td style="text-align: right">
                {{ __('Total Due')}}
                : {{ setting('currency_symbol').' '.number_format(($invoice->amount+ $invoice->bills_amount),2) }}
            </td>

        </tr>
        <tr class="total">
            <td></td>
            <td style="text-align: right">
                {{ __('Paid Amount:')}} {{ setting('currency_symbol').' '.number_format($invoice->paid_amount,2) }}
            </td>
        </tr>
        <tr class="total">
            <td></td>
            <td style="text-align: right">
                {{ __('Balance Due')}}: {{ setting('currency_symbol').' '.number_format($invoice->balance_due,2) }}
            </td>

        </tr>

        <tr>
            <td colspan="2">
                <strong>{{ __('Approved Transactions')}}</strong>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <table>
                    <tr style=" background: #eee;
            border-bottom: 1px solid #ddd;">
                        <td>{{ __('Date')}}</td>
                        <td>{{ __('Payment Method')}}</td>
                        <td>{{ __('Reference Number')}}</td>
                        <td>{{ __('Amount')}}</td>
                    </tr>

                    @foreach($invoice->verified_payments as $payment)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td>{{ $payment->created_at->format('F d,Y') }}</td>
                            <td>{{ $payment->payment_method }}</td>
                            <td>{{ $payment->reference_number }}</td>
                            <td>{{ setting('currency_symbol').' '.number_format($payment->amount,2) }}</td>
                        </tr>
                    @endforeach


                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <strong>{{ __('Pending Transactions')}}</strong>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <table>
                    <tr style=" background: #eee;
            border-bottom: 1px solid #ddd;">
                        <td>{{ __('Date')}}</td>
                        <td>{{ __('Payment Method')}}</td>
                        <td>{{ __('Reference Number')}}</td>
                        <td>{{ __('Amount')}}</td>
                    </tr>

                    @foreach($invoice->pending_payments as $pending_payment)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td>{{ $pending_payment->created_at->format('F d,Y') }}</td>
                            <td>{{ $pending_payment->payment_method }}</td>
                            <td>{{ $pending_payment->reference_number }}</td>
                            <td>{{ setting('currency_symbol').' '.number_format($pending_payment->amount,2) }}</td>
                        </tr>
                    @endforeach


                </table>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <strong>{{ __('Cancelled Transactions')}}</strong>
            </td>
        </tr>

        <tr>
            <td colspan="2">
                <table>
                    <tr style=" background: #eee;
            border-bottom: 1px solid #ddd;">
                        <td>{{ __('Date')}}</td>
                        <td>{{ __('Payment Method')}}</td>
                        <td>{{ __('Reference Number')}}</td>
                        <td>{{ __('Amount')}}</td>
                    </tr>

                    @foreach($invoice->cancelled_payments as $cancelled_payment)
                        <tr style="border-bottom: 1px solid #ddd;">
                            <td>{{ $cancelled_payment->created_at->format('F d,Y') }}</td>
                            <td>{{ $cancelled_payment->payment_method }}</td>
                            <td>{{ $cancelled_payment->reference_number }}</td>
                            <td>{{ setting('currency_symbol').' '.number_format($cancelled_payment->amount,2) }}</td>
                        </tr>
                    @endforeach


                </table>
            </td>
        </tr>


    </table>
</div>
</body>
</html>
