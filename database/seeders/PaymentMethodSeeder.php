<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $paymentMethods = [
            'CASH',
            'BANK TRANSFER',
            'MPESA STK',
            'MPESA PAYBILL',
            'MPESA C2B',
            'PAYPAL',
            'CHEQUE',
            'CARD PAYMENT',
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::firstOrCreate(['name' => $method]);
        }

        $this->command->info('Payment methods seeded successfully!');
    }
}

