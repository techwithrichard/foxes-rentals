<!DOCTYPE html>
<html>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<head>
    <style>

        /*
  Common invoice styles. These styles will work in a browser or using the HTML
  to PDF anvil endpoint.
*/

        body {
            font-size: 16px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table tr td {
            padding: 0;
        }

        table tr td:last-child {
            text-align: right;
        }

        .bold {
            font-weight: bold;
        }

        .right {
            text-align: right;
        }

        .large {
            font-size: 1.75em;
        }

        .total {
            font-weight: bold;
            color: #fb7578;
        }

        .logo-container {
            margin: 20px 0 70px 0;
        }

        .invoice-info-container {
            font-size: 0.875em;
        }

        .invoice-info-container td {
            padding: 4px 0;
        }

        .client-name {
            font-size: 1.5em;
            vertical-align: top;
        }

        .line-items-container {
            margin: 70px 0;
            font-size: 0.875em;
        }

        .line-items-container th {
            text-align: left;
            color: #999;
            border-bottom: 2px solid #ddd;
            padding: 10px 0 15px 0;
            font-size: 0.75em;
            text-transform: uppercase;
        }

        .line-items-container th:last-child {
            text-align: right;
        }

        .line-items-container td {
            padding: 15px 0;
        }

        .line-items-container tbody tr:first-child td {
            padding-top: 25px;
        }

        .line-items-container.has-bottom-border tbody tr:last-child td {
            padding-bottom: 25px;
            border-bottom: 2px solid #ddd;
        }

        .line-items-container.has-bottom-border {
            margin-bottom: 0;
        }

        .line-items-container th.heading-quantity {
            width: 50px;
        }

        .line-items-container th.heading-price {
            text-align: right;
            width: 100px;
        }

        .line-items-container th.heading-subtotal {
            width: 100px;
        }

        .payment-info {
            width: 38%;
            font-size: 0.75em;
            line-height: 1.5;
        }


        .page-container {
            display: none;
        }

        /*
  The styles here for use when generating a PDF invoice with the HTML code.
  * Set up a repeating page counter
  * Place the .footer-info in the last page's footer
*/

        .footer {
            margin-top: 30px;
        }

        .footer-info {
            float: none;
            position: running(footer);
            margin-top: -25px;
        }

        .page-container {
            display: block;
            position: running(pageContainer);
            margin-top: -25px;
            font-size: 12px;
            text-align: right;
            color: #999;
        }

        .page-container .page::after {
            content: counter(page);
        }

        .page-container .pages::after {
            content: counter(pages);
        }


        @page {
            @bottom-right {
                content: element(pageContainer);
            }
            @bottom-left {
                content: element(footer);
            }
        }

    </style>
</head>
<body>


<div class="logo-container">
    <img src="{{ asset('assets/images/logo.png') }}" height="60">
</div>

<h3 style="text-align: center;">{{ __('Invoice')}}</h3>

<table class="invoice-info-container">
    <tr>
        <td class="client-name">
            {{ __('Landlord Details:')}}
        </td>
        <td>
        </td>
    </tr>

    <tr>
        <td>
            {{ $invoice->landlord->name }}<br>
            {{ $invoice->landlord->email }}<br>
            {{ $invoice->landlord->phone }}<br>
            {{ $invoice->landlord->address }}

        </td>
        <td>
            {{ setting('company_name') }}<br>
            {{ setting('company_email') }}<br>
            {{ setting('company_email') }}<br>
            {{ setting('company_address') }}

        </td>
    </tr>

    <tr>
        <td>
            {{ __('Invoice Date:')}} <strong>{{ $invoice->invoice_date?->format('M d,Y') }}</strong><br>
            {{ __('Due Date:')}} <strong>{{ $invoice->due_date?->format('M d,Y') }}</strong><br>
            {{ __('Invoice Number:')}} <strong>{{ $invoice->invoice_id }}</strong>

        </td>
        <td>
            {{ __('Property:')}} <strong>{{ $invoice->property?->name }}</strong><br>
            {{ __('House:')}} <strong>{{ $invoice->house?->name }}</strong>
        </td>
    </tr>

</table>


<table class="line-items-container">
    <thead>
    <tr>
        <th class="heading-quantity">#</th>
        <th class="heading-description">{{ __('Description')}}</th>
        <th class="heading-quantity">{{ __('Qty')}}</th>
        <th class="heading-subtotal">{{ __('Subtotal')}}</th>
    </tr>
    </thead>
    <tbody>

    @foreach($invoice->items as $item)
        <tr>
            <td>
                {{ $loop->iteration }}
            </td>
            <td>
                {{ $item->description }}
            </td>
            <td>
                {{ $item->quantity }}
            </td>
            <td>
                {{ number_format($item->cost,2) }}
            </td>
        </tr>

    @endforeach


    </tbody>
</table>


<table class="line-items-container has-bottom-border">
    <thead>
    <tr>
        <th></th>
        <th></th>
        <th>{{ __('Total Due')}}</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td class="payment-info">

        </td>
        <td class="large"></td>
        <td class="large total">{{setting('currency_symbol').' '.number_format($invoice->items->sum('cost'),2) }}</td>
    </tr>
    </tbody>
</table>


</body>
</html>
