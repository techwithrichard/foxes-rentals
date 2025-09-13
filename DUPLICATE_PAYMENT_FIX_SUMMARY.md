# Partial Payment and Overpayment Handling

## Problem
The system needed to handle multiple partial payments for the same invoice correctly. Tenants should be able to pay multiple times (e.g., 400, 200, 50) for the same invoice until the full amount is reached. Only when the total exceeds the required amount should it be marked as an overpayment.

## Solution Implemented

### 1. Enhanced PaymentReconciliationService
- **Smart overpayment detection**: Only triggers when a payment would exceed the remaining invoice balance
- **Payment splitting**: When overpayment occurs, the payment is split between completing the invoice and creating an overpayment
- **Precise tracking**: Tracks exactly how much was overpaid and which payment caused the overpayment
- **Improved logging**: Added detailed logging for overpayment detection and handling

### 2. Updated MpesaPaymentController
- **C2B Payment Processing**: Enhanced to detect and handle overpayments automatically
- **STK Payment Processing**: Added overpayment detection for STK push payments
- **Better error handling**: Improved logging and error messages for overpayment scenarios

### 3. Key Features

#### Overpayment Detection Criteria
- Same phone number (tenant)
- Payment amount exceeds remaining invoice balance
- Only triggers when there's an actual overpayment (not for partial payments)
- Works with multiple partial payments until balance is reached

#### Smart Payment Splitting
- When overpayment occurs, payment is split into two parts:
  1. Amount needed to complete the invoice
  2. Excess amount recorded as overpayment
- Two separate payment records are created with clear references
- Invoice is marked as completed
- Overpayment record is created or updated for the tenant

#### Logging and Monitoring
- All overpayments are logged with detailed information
- Invoice completion and overpayment amounts are tracked
- Success/failure of overpayment handling is logged

### 4. Files Modified

1. **app/Services/PaymentReconciliationService.php**
   - Updated `checkForDuplicatePayment()` method to detect overpayments
   - Updated `handleDuplicatePayment()` method to split payments
   - Enhanced `processC2bCallback()` method
   - Added test method for verification

2. **app/Http/Controllers/MpesaPaymentController.php**
   - Enhanced C2B confirmation processing
   - Enhanced STK callback processing
   - Added overpayment detection for both payment types

3. **app/Console/Commands/TestDuplicatePaymentDetection.php** (New)
   - Command to test overpayment detection
   - Usage: `php artisan test:duplicate-payment {phone} {amount}`

### 5. How It Works

1. **Payment Received**: M-PESA sends payment confirmation
2. **Overpayment Check**: System checks if payment would exceed remaining invoice balance
3. **Decision Making**:
   - If overpayment detected → Split payment between invoice completion and overpayment
   - If no overpayment → Process as normal payment
4. **Payment Splitting**: Creates two payment records - one for invoice, one for overpayment
5. **Logging**: All actions are logged for audit trail

### 6. Benefits

- **Prevents Double Charging**: Tenants won't be charged twice for the same invoice
- **Automatic Processing**: No manual intervention required for duplicate payments
- **Audit Trail**: Complete logging of all duplicate payment handling
- **Overpayment Tracking**: Duplicate payments are properly tracked as overpayments
- **Admin Visibility**: Overpayments are visible in the admin panel under `/admin/overpayments`

### 7. Testing

Use the test command to verify duplicate detection:
```bash
php artisan test:duplicate-payment 254712345678 2000
```

This will check if there are any recent payments of Ksh 2000 from phone number 254712345678.

### 8. Configuration

The duplicate detection time window is currently set to 30 minutes. This can be adjusted in the `checkForDuplicatePayment()` method by changing:
```php
->where('created_at', '>=', now()->subMinutes(30))
```

## Example Scenario

**Before Fix:**
- Tenant pays Ksh 2,000 for invoice #12345
- Tenant accidentally pays Ksh 2,000 again 10 minutes later
- Both payments are recorded as regular payments
- Invoice shows as overpaid, but both payments are counted

**After Fix:**
- Tenant pays Ksh 2,000 for invoice #12345
- Tenant accidentally pays Ksh 2,000 again 10 minutes later
- First payment: Recorded as regular payment, invoice marked as paid
- Second payment: Automatically detected as duplicate, recorded as overpayment
- Admin can see the overpayment in `/admin/overpayments` and handle accordingly
