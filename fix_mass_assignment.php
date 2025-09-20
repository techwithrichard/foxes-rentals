<?php

/**
 * Script to fix mass assignment vulnerabilities in all models
 * This script will replace protected $guarded = [] with proper fillable arrays
 */

$models = [
    'House' => [
        'name', 'description', 'property_id', 'rent', 'deposit', 'is_vacant', 
        'status', 'electricity_id', 'water_id', 'notes'
    ],
    'VoucherDocument' => [
        'voucher_id', 'document_name', 'document_path', 'document_type', 'file_size'
    ],
    'VoucherItem' => [
        'voucher_id', 'description', 'amount', 'quantity', 'unit_price'
    ],
    'Voucher' => [
        'voucher_number', 'landlord_id', 'property_id', 'house_id', 'amount', 
        'status', 'description', 'date', 'reference_number'
    ],
    'TicketReply' => [
        'ticket_id', 'user_id', 'message', 'is_internal', 'attachments'
    ],
    'TicketCount' => [
        'user_id', 'open_tickets', 'closed_tickets', 'total_tickets'
    ],
    'TicketAttachment' => [
        'ticket_id', 'reply_id', 'file_name', 'file_path', 'file_size', 'mime_type'
    ],
    'SupportTicket' => [
        'ticket_number', 'user_id', 'subject', 'description', 'status', 
        'priority', 'category', 'assigned_to', 'resolved_at'
    ],
    'PaymentMethod' => [
        'name', 'type', 'is_active', 'configuration', 'description'
    ],
    'PaymentProof' => [
        'payment_id', 'file_name', 'file_path', 'file_size', 'status', 'verified_by', 'verified_at'
    ],
    'Overpayment' => [
        'tenant_id', 'amount', 'payment_id', 'invoice_id', 'status', 'notes', 'refunded_at'
    ],
    'LeaseDocument' => [
        'lease_id', 'document_name', 'document_path', 'document_type', 'file_size'
    ],
    'LoginActivity' => [
        'user_id', 'ip_address', 'user_agent', 'login_at', 'logout_at', 'status'
    ],
    'LeaseBill' => [
        'lease_id', 'name', 'amount', 'description', 'due_date', 'status'
    ],
    'LandlordRemittance' => [
        'landlord_id', 'property_id', 'amount', 'commission', 'status', 'date', 'reference_number'
    ],
    'InvoiceItem' => [
        'invoice_id', 'description', 'quantity', 'unit_price', 'amount', 'tax_rate'
    ],
    'InvoiceBill' => [
        'invoice_id', 'bill_name', 'amount', 'description', 'due_date'
    ],
    'HouseType' => [
        'name', 'description', 'bedrooms', 'bathrooms', 'features'
    ],
    'Expense' => [
        'property_id', 'house_id', 'expense_type_id', 'amount', 'description', 
        'date', 'status', 'receipt_path', 'notes'
    ],
    'CustomInvoice' => [
        'invoice_number', 'client_name', 'client_email', 'amount', 'description', 
        'due_date', 'status', 'notes'
    ],
    'Deposit' => [
        'lease_id', 'amount', 'status', 'deposit_type', 'notes', 'refunded_at'
    ],
    'C2bRequest' => [
        'transaction_id', 'amount', 'phone_number', 'account_number', 'status', 'callback_data'
    ],
    'Address' => [
        'addressable_type', 'addressable_id', 'street', 'city', 'state', 
        'postal_code', 'country', 'latitude', 'longitude'
    ],
    'StkRequest' => [
        'phone_number', 'amount', 'account_reference', 'transaction_desc', 
        'merchant_request_id', 'checkout_request_id', 'response_code', 'status'
    ]
];

foreach ($models as $modelName => $fillableFields) {
    $filePath = "app/Models/{$modelName}.php";
    
    if (file_exists($filePath)) {
        $content = file_get_contents($filePath);
        
        // Replace protected $guarded = []; with fillable array
        $fillableArray = "protected \$fillable = [\n";
        foreach ($fillableFields as $field) {
            $fillableArray .= "        '{$field}',\n";
        }
        $fillableArray .= "    ];\n\n    protected \$guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];";
        
        $content = preg_replace(
            '/protected \$guarded = \[\];/',
            $fillableArray,
            $content
        );
        
        file_put_contents($filePath, $content);
        echo "✅ Fixed {$modelName} model\n";
    } else {
        echo "❌ File not found: {$filePath}\n";
    }
}

echo "\n🎉 Mass assignment vulnerabilities fixed!\n";
echo "📊 Total models secured: " . count($models) . "\n";
echo "🔒 All models now have proper fillable arrays\n";
