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

- **Supports Partial Payments**: Tenants can make multiple payments (400, 200, 50) until invoice is complete
- **Automatic Overpayment Detection**: Only triggers when payment exceeds remaining balance
- **Smart Payment Splitting**: Automatically splits overpayments between invoice completion and overpayment
- **Complete Audit Trail**: All payment handling is logged with detailed information
- **Admin Visibility**: Overpayments visible in `/admin/overpayments`
- **Precise Tracking**: Exact overpayment amounts are tracked and recorded

### 7. Testing

Use the test command to verify overpayment detection:
```bash
php artisan test:duplicate-payment 254712345678 2000
```

This will check if a payment of Ksh 2000 from phone number 254712345678 would cause an overpayment.

### 8. Configuration

The overpayment detection works based on invoice balances, not time windows. It automatically calculates the remaining balance for each invoice and determines if the payment would exceed it.

## Example Scenarios

### Scenario 1: Multiple Partial Payments (No Overpayment)
**Invoice**: Lease ID "CsBvzmgAmM" - Ksh 1,000
- Tenant pays Ksh 400 → Invoice balance: Ksh 600 (PARTIALLY_PAID)
- Tenant pays Ksh 200 → Invoice balance: Ksh 400 (PARTIALLY_PAID)  
- Tenant pays Ksh 50 → Invoice balance: Ksh 350 (PARTIALLY_PAID)
- Tenant pays Ksh 350 → Invoice balance: Ksh 0 (PAID)

### Scenario 2: Overpayment Detection
**Invoice**: Lease ID "CsBvzmgAmM" - Ksh 1,000
- Tenant pays Ksh 400 → Invoice balance: Ksh 600 (PARTIALLY_PAID)
- Tenant pays Ksh 200 → Invoice balance: Ksh 400 (PARTIALLY_PAID)
- Tenant pays Ksh 500 → **OVERPAYMENT DETECTED!**
  - Payment split: Ksh 400 for invoice completion, Ksh 100 as overpayment
  - Invoice marked as PAID
  - Overpayment of Ksh 100 recorded for tenant
  - Two payment records created with clear references

### Scenario 3: Your Original Example
**Invoice**: Lease ID "CsBvzmgAmM" - Ksh 2,000
- First payment Ksh 2,000 → Invoice marked as PAID
- Second payment Ksh 2,000 → **OVERPAYMENT DETECTED!**
  - Payment split: Ksh 0 for invoice (already paid), Ksh 2,000 as overpayment
  - Overpayment of Ksh 2,000 recorded for tenant
  - Admin can see overpayment in `/admin/overpayments`

## Technical Details

### Payment Splitting Logic
When an overpayment is detected:
1. Calculate remaining invoice balance
2. Create payment record for remaining balance (completes invoice)
3. Create separate payment record for excess amount (overpayment)
4. Update invoice status to PAID
5. Create/update overpayment record for tenant
6. Log all actions with detailed information

### Reference Number Format
- Invoice payment: `{original_reference}-INVOICE`
- Overpayment: `{original_reference}-OVERPAYMENT`

This ensures clear tracking of which payment caused the overpayment and how it was split.

