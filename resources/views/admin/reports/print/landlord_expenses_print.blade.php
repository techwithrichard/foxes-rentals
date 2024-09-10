<!DOCTYPE html>
<html>
<head>
    <title>Expenses Report</title>
    <style>
        /* Add some style to make the report more appealing */
        @page {
            size: A4;
            /*margin: 2cm;*/
        }

        body {
            /*padding: 1cm;*/
        }

        h1 {
            text-align: center;
        }

        .expense-table {
            border-collapse: collapse;
            width: 90%;
            margin: 0 auto;
        }

        .expense-table td, .expense-table th {
            border: 1px solid #ddd;
            padding: 8px;
        }

        .expense-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        .expense-table tr:hover {
            background-color: #ddd;
        }

        .expense-table th {
            padding-top: 12px;
            padding-bottom: 12px;
            text-align: left;
            background-color: #4caf50;
            color: white;
        }

        .category-header {
            background-color: #4caf50;
            color: white;
            font-weight: bold;
        }

        .total {
            font-weight: bold;
            font-size: larger;
        }
    </style>
</head>
<body>
<h1>{{ __('Landlord Expenses Report')}}</h1>
<hr>
<h3> {{ __('Incurred Expenses between')}} {{$start_date}} {{ __('and')}} {{$end_date}}</h3>
<h3> {{ __('Landlord')}}: {{$landlord_name}}</h3>

<table class="expense-table">

    @foreach($expenses->groupBy('category.name') as $categoryName=>$categoryExpenses)
        <tr class="category-header">
            <th colspan="3"> {{ $categoryName }}</th>
        </tr>




        @foreach($categoryExpenses as $expense)
            <tr>
                <td>{{ $expense->description }}</td>
                <td>{{ $expense->incurred_on?->format('M d, Y') }}</td>
                <td>{{ setting('currency_symbol').' '. number_format($expense->amount,2) }} </td>
            </tr>



        @endforeach

    @endforeach

</table>
<p class="total">{{ __('Total Expenses')}}: {{ setting('currency_symbol').' '. number_format($expenses->sum('amount'),2) }} </p>
</body>
</html>
