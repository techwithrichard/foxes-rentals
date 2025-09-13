# Paybill Payment Troubleshooting Guide

## Problem: Payments via Paybill Not Showing Up

If payments via paybill are successful but not appearing in the system, here are the steps to diagnose and fix the issue.

## 🔍 Diagnostic Steps

### 1. Run Diagnostic Command
```bash
php artisan diagnose:paybill-payments
```

This will check:
- Recent C2B requests in database
- System configuration
- Database connectivity
- Specific payment searches

### 2. Check Specific Payment
```bash
php artisan diagnose:paybill-payments --phone=254712345678 --amount=2000
```

### 3. Process Pending Requests
```bash
php artisan process:pending-c2b-requests --limit=20
```

## 🚨 Common Issues and Solutions

### Issue 1: IP Whitelist Problem
**Problem**: M-PESA callbacks are being blocked due to IP validation
**Solution**: Fixed IP whitelist typo in `config/mpesa.php`
- Changed `'196. 201.213.44'` to `'196.201.213.44'`

### Issue 2: Callback URL Not Accessible
**Problem**: M-PESA cannot reach your confirmation URL
**Check**:
- Is your server accessible from the internet?
- Are the callback URLs correctly configured?
- Is there a firewall blocking incoming requests?

**URLs to check**:
- Confirmation URL: `https://yourdomain.com/api/callback/confirmation`
- Validation URL: `https://yourdomain.com/api/callback/validation`

### Issue 3: Database Connection Issues
**Problem**: C2B requests are received but not saved to database
**Check**:
- Database connection is working
- C2B requests table exists and is accessible
- No database errors in logs

### Issue 4: Automatic Reconciliation Failing
**Problem**: C2B requests are saved but not automatically reconciled
**Check**:
- Phone number format matches tenant records
- Payment amount matches invoice amounts
- Tenant exists in system

### Issue 5: Manual Reconciliation Required
**Problem**: Some payments require manual reconciliation
**Solution**: Use admin panel to manually reconcile
1. Go to Admin → MPesa Transactions → C2B Transactions
2. Find the pending transaction
3. Click "Reconcile" button
4. Select the correct tenant and invoice

## 📊 Where to Check Payments

### 1. C2B Transactions (Raw M-PESA Data)
- **Location**: Admin → MPesa Transactions → C2B Transactions
- **Shows**: All M-PESA callbacks received
- **Status**: Pending, Reconciled, or Ignored

### 2. Payments Table (Processed Payments)
- **Location**: Admin → Payments (if available)
- **Shows**: Successfully processed payments
- **Status**: Paid, Pending, or Verified

### 3. Invoice Status
- **Location**: Admin → Invoices
- **Shows**: Invoice payment status
- **Status**: Pending, Partially Paid, Paid, Over Paid

### 4. Overpayments
- **Location**: Admin → Overpayments
- **Shows**: Excess payments
- **Status**: Available for refund or credit

## 🔧 Configuration Checklist

### M-PESA Configuration
- [ ] Consumer Key and Secret are correct
- [ ] Business Short Code is correct
- [ ] Paybill number is correct
- [ ] Passkey is correct
- [ ] Environment (sandbox/live) is correct

### Callback URLs
- [ ] Confirmation URL is accessible
- [ ] Validation URL is accessible
- [ ] STK Callback URL is accessible
- [ ] URLs use HTTPS (required for live environment)

### IP Whitelist
- [ ] All Safaricom IPs are whitelisted
- [ ] No typos in IP addresses
- [ ] IP validation is working correctly

### Database
- [ ] C2B requests table exists
- [ ] Payments table exists
- [ ] Invoices table exists
- [ ] Database connection is stable

## 🚀 Quick Fixes

### Fix 1: Process All Pending C2B Requests
```bash
php artisan process:pending-c2b-requests --limit=100
```

### Fix 2: Check Recent Logs
```bash
tail -f storage/logs/laravel.log | grep -i "c2b\|mpesa\|payment"
```

### Fix 3: Test Callback URL
```bash
curl -X POST https://yourdomain.com/api/callback/confirmation \
  -H "Content-Type: application/json" \
  -d '{"test": "data"}'
```

### Fix 4: Verify Database
```bash
php artisan tinker
>>> App\Models\C2bRequest::count()
>>> App\Models\Payment::count()
```

## 📱 Testing Paybill Payments

### 1. Sandbox Testing
- Use M-PESA sandbox environment
- Test with sandbox phone numbers
- Check logs for callback reception

### 2. Live Testing
- Use real M-PESA environment
- Test with actual phone numbers
- Monitor callback URLs

### 3. Manual Testing
- Make a test payment via paybill
- Check C2B transactions immediately
- Process manually if needed

## 🔍 Monitoring and Alerts

### Set up monitoring for:
- C2B callback reception
- Payment processing success rate
- Failed reconciliation attempts
- Database connectivity

### Log monitoring:
- Laravel logs: `storage/logs/laravel.log`
- M-PESA callback logs: `storage/app/confirmation.txt`
- Error logs: Check for exceptions

## 📞 Support Contacts

If issues persist:
1. Check M-PESA developer documentation
2. Contact Safaricom support for callback issues
3. Review server logs for errors
4. Test with different phone numbers and amounts

## 🎯 Success Indicators

Payments are working correctly when:
- ✅ C2B requests appear in admin panel
- ✅ Payments are automatically reconciled
- ✅ Invoices show correct payment status
- ✅ Overpayments are properly tracked
- ✅ No manual intervention required

## 📋 Maintenance Tasks

### Daily:
- Check for pending C2B requests
- Monitor payment processing logs
- Verify callback URL accessibility

### Weekly:
- Review failed reconciliation attempts
- Check IP whitelist for updates
- Test callback URLs

### Monthly:
- Review payment processing statistics
- Update M-PESA configuration if needed
- Clean up old log files

