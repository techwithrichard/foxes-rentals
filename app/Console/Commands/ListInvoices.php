<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Console\Command;

class ListInvoices extends Command
{
    protected $signature = 'invoices:list';
    protected $description = 'List all invoices with tenant information';

    public function handle()
    {
        $this->info('Available Invoices:');
        $this->line('');
        
        $invoices = Invoice::with('tenant')->get(['id', 'invoice_id', 'tenant_id', 'amount', 'paid_amount', 'status']);
        
        $this->table(['ID', 'Invoice ID', 'Tenant', 'Amount', 'Paid', 'Balance', 'Status'], 
            $invoices->map(function($invoice) {
                return [
                    $invoice->id,
                    $invoice->invoice_id,
                    $invoice->tenant->name ?? 'N/A',
                    'KES ' . number_format($invoice->amount),
                    'KES ' . number_format($invoice->paid_amount),
                    'KES ' . number_format($invoice->balance_due),
                    $invoice->status->value ?? $invoice->status
                ];
            })->toArray()
        );
        
        return 0;
    }
}
