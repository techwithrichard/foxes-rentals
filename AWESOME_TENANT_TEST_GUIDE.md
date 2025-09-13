# Awesome Tenant Paybill C2B Test Guide

## 🧪 **Test Scenario**

Testing paybill C2B payment with Awesome Tenant to verify:
1. C2B callback reception
2. Automatic reconciliation
3. Overpayment handling

## 📋 **Tenant Details**

- **Name**: Awesome Tenant
- **Phone**: 254720691181
- **Email**: kipkoech25.richard@student.cuk.ac.ke
- **Current Invoice**: 9fd89980-1e1b-4d94-8aa8-dbc26f05f93c
- **Invoice Amount**: Ksh 2.00
- **Current Balance**: Ksh -2.00 (OVERPAID)

## 💳 **Payment Details**

- **Paybill Number**: 174379
- **Account Number**: 9fd89980-1e1b-4d94-8aa8-dbc26f05f93c
- **Test Amount**: Ksh 1.00 (to test overpayment scenario)

## 📱 **Step-by-Step Test Instructions**

### **1. Make Test Payment**
1. Use phone: **254720691181**
2. Go to M-PESA menu
3. Select "Lipa na M-PESA"
4. Select "Paybill"
5. Enter business number: **174379**
6. Enter account number: **9fd89980-1e1b-4d94-8aa8-dbc26f05f93c**
7. Enter amount: **Ksh 1**
8. Enter PIN and press OK
9. Wait for confirmation SMS

### **2. Monitor System**
After making payment, run these commands to check:

```bash
# Check for C2B callback
php artisan diagnose:paybill-payments --phone=254720691181

# Check specific transaction (replace TRANS_ID with actual transaction ID)
php artisan diagnose:paybill-payments --trans-id=TRANS_ID

# Check invoice status
php artisan tinker --execute="echo 'Invoice Balance: ' . App\Models\Invoice::find('9fd89980-1e1b-4d94-8aa8-dbc26f05f93c')->balance_due;"
```

### **3. Expected Results**

**If C2B Callback Works:**
- ✅ C2B request recorded in database
- ✅ Payment automatically reconciled
- ✅ Invoice balance updated (should become -3.00)
- ✅ Overpayment record created

**If C2B Callback Fails:**
- ❌ No C2B request in database
- ❌ Payment not recorded
- ❌ Manual reconciliation needed

## 🔍 **Troubleshooting**

### **If No C2B Callback Received:**

1. **Check ngrok status:**
```bash
curl -s https://34c0e7a8b454.ngrok-free.app/api/callback/confirmation
```

2. **Register callback URLs:**
```bash
php artisan tinker --execute="echo json_encode(App\Services\MPesaHelper::registerURLS());"
```

3. **Check logs:**
```bash
type storage\logs\laravel.log | findstr /i "c2b mpesa callback confirmation"
```

### **If Payment Not Reconciled:**

1. **Check account number format:**
   - Should be: `9fd89980-1e1b-4d94-8aa8-dbc26f05f93c`
   - Not: `CsBvzmgAmM` (lease reference)

2. **Verify invoice exists:**
```bash
php artisan tinker --execute="echo App\Models\Invoice::find('9fd89980-1e1b-4d94-8aa8-dbc26f05f93c') ? 'Invoice exists' : 'Invoice not found';"
```

## 📊 **Test Results Tracking**

### **Before Payment:**
- Invoice Balance: Ksh -2.00
- C2B Requests: 0
- Payments: 2

### **After Payment (Expected):**
- Invoice Balance: Ksh -3.00
- C2B Requests: 1
- Payments: 3
- Overpayment: Ksh 1.00

## 🎯 **Success Criteria**

The test is successful if:
1. ✅ C2B callback received
2. ✅ Payment automatically reconciled
3. ✅ Invoice balance updated correctly
4. ✅ Overpayment properly recorded
5. ✅ No manual intervention required

## 📞 **Support Information**

- **Tenant Phone**: 254720691181
- **Test Invoice**: 9fd89980-1e1b-4d94-8aa8-dbc26f05f93c
- **Paybill**: 174379
- **Environment**: Sandbox

## 🚀 **Next Steps After Test**

1. **If Successful**: System is working correctly
2. **If Failed**: Check callback URL registration
3. **Document Results**: Record any issues found
4. **Test Other Scenarios**: Try with different amounts/tenants

---

**Ready to test! Use the payment details above to make a test payment and monitor the system response.**

