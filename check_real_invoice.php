<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo 'Real Invoice Data from Database:' . PHP_EOL;
$invoice = App\Models\Invoice::find('9fd89980-1e1b-4d94-8aa8-dbc26f05f93c');

if ($invoice) {
    echo 'Base Amount: Ksh ' . number_format($invoice->amount, 2) . PHP_EOL;
    echo 'Bills Amount: Ksh ' . number_format($invoice->bills_amount, 2) . PHP_EOL;
    echo 'Total: Ksh ' . number_format($invoice->amount + $invoice->bills_amount, 2) . PHP_EOL;
    echo 'Paid: Ksh ' . number_format($invoice->paid_amount, 2) . PHP_EOL;
    echo 'Balance: Ksh ' . number_format($invoice->balance_due, 2) . PHP_EOL;
    echo 'Status: ' . $invoice->status->value . PHP_EOL;
    
    if ($invoice->bills && is_array($invoice->bills)) {
        echo PHP_EOL . 'Bills Breakdown:' . PHP_EOL;
        foreach ($invoice->bills as $bill) {
            echo '  - ' . $bill['name'] . ': Ksh ' . number_format($bill['amount'], 2) . PHP_EOL;
        }
    }
} else {
    echo 'Invoice not found!' . PHP_EOL;
}

