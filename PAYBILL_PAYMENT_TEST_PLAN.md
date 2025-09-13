# Paybill Payment Test Plan - Awesome Tenant Invoice

## 📋 **Current Invoice Status**

### **Invoice Details**
- **UUID**: `9fd89980-1e1b-4d94-8aa8-dbc26f05f93c`
- **Account Number**: `CsBvzmgAmM`
- **Tenant**: Awesome Tenant (254720691181)
- **Property**: Morphic Towers

### **Financial Breakdown**
- **Base Amount**: Ksh 2.00
- **Water Bill**: Ksh 500.00
- **Garbage Collection**: Ksh 300.00
- **Total Bills**: Ksh 800.00
- **Total Amount**: Ksh 802.00
- **Paid Amount**: Ksh 4.00
- **Balance Due**: Ksh 798.00

## 🎯 **Test Scenarios**

### **Scenario 1: Partial Payment (Ksh 500)**
**Expected Result**: 
- New Balance: Ksh 298.00
- Status: PARTIALLY_PAID
- No Overpayment

**Test Steps**:
1. Make paybill payment: Ksh 500
2. Account: CsBvzmgAmM
3. Verify balance becomes Ksh 298.00
4. Verify status becomes PARTIALLY_PAID

### **Scenario 2: Another Partial Payment (Ksh 200)**
**Expected Result**:
- New Balance: Ksh 98.00
- Status: PARTIALLY_PAID
- No Overpayment

**Test Steps**:
1. Make paybill payment: Ksh 200
2. Account: CsBvzmgAmM
3. Verify balance becomes Ksh 98.00
4. Verify status remains PARTIALLY_PAID

### **Scenario 3: Final Payment (Ksh 100)**
**Expected Result**:
- New Balance: Ksh -2.00
- Status: OVER_PAID
- Overpayment: Ksh 2.00

**Test Steps**:
1. Make paybill payment: Ksh 100
2. Account: CsBvzmgAmM
3. Verify balance becomes Ksh -2.00
4. Verify status becomes OVER_PAID
5. Verify overpayment record created

### **Scenario 4: Large Overpayment (Ksh 1000)**
**Expected Result**:
- New Balance: Ksh -1002.00
- Status: OVER_PAID
- Overpayment: Ksh 1002.00

**Test Steps**:
1. Make paybill payment: Ksh 1000
2. Account: CsBvzmgAmM
3. Verify balance becomes Ksh -1002.00
4. Verify status becomes OVER_PAID
5. Verify overpayment record created

## 📱 **Paybill Payment Instructions**

### **For Each Test Payment**:
1. Go to M-PESA menu
2. Select "Lipa na M-PESA"
3. Select "Paybill"
4. Enter business number: **174379**
5. Enter account number: **CsBvzmgAmM**
6. Enter amount: **[Test Amount]**
7. Enter PIN and press OK
8. Wait for confirmation SMS

## 🔍 **Monitoring Commands**

### **Check Invoice Status**
```bash
php artisan check:invoice-details 9fd89980-1e1b-4d94-8aa8-dbc26f05f93c
```

### **Check C2B Callbacks**
```bash
php artisan diagnose:paybill-payments --phone=254720691181
```

### **Check Specific Transaction**
```bash
php artisan diagnose:paybill-payments --trans-id=TRANSACTION_ID
```

### **Test Account Lookup**
```bash
php artisan test:account-lookup CsBvzmgAmM
```

## ✅ **Success Criteria**

### **Balance Tracking**
- ✅ Balance updates correctly after each payment
- ✅ Multiple payments are properly accumulated
- ✅ Bills are included in total amount calculation
- ✅ Overpayment only occurs when payment exceeds total billing

### **Status Management**
- ✅ Status changes from PENDING → PARTIALLY_PAID → OVER_PAID
- ✅ Status reflects current payment state
- ✅ No incorrect status changes

### **Overpayment Handling**
- ✅ Overpayment only recorded when payment exceeds total billing
- ✅ Overpayment amount calculated correctly
- ✅ Overpayment records created properly
- ✅ Balance shows negative amount for overpayments

### **Reconciliation**
- ✅ C2B callbacks received and processed
- ✅ Payments automatically reconciled to correct invoice
- ✅ Phone number verification works
- ✅ Account number lookup works correctly

## 🚨 **Important Notes**

1. **Balance Calculation**: `balance_due = (amount + bills_amount) - paid_amount`
2. **Overpayment Detection**: Only when `payment_amount > balance_due`
3. **Status Updates**: Automatic based on current balance
4. **Multiple Payments**: Each payment reduces the balance
5. **Bills Integration**: Bills are part of total amount calculation

## 🎯 **Test Execution Order**

1. **Start**: Check current invoice status
2. **Payment 1**: Ksh 500 (partial payment)
3. **Verify**: Balance = Ksh 298.00, Status = PARTIALLY_PAID
4. **Payment 2**: Ksh 200 (another partial payment)
5. **Verify**: Balance = Ksh 98.00, Status = PARTIALLY_PAID
6. **Payment 3**: Ksh 100 (final payment with overpayment)
7. **Verify**: Balance = Ksh -2.00, Status = OVER_PAID
8. **Payment 4**: Ksh 1000 (large overpayment)
9. **Verify**: Balance = Ksh -1002.00, Status = OVER_PAID

## 📊 **Expected Final State**

After all test payments:
- **Total Payments Made**: Ksh 1,800.00
- **Total Amount Due**: Ksh 802.00
- **Final Balance**: Ksh -998.00 (overpaid)
- **Status**: OVER_PAID
- **Overpayment Records**: Multiple overpayment records created

---

**Ready to test! Use the paybill details above to make test payments and verify the system correctly tracks balances and handles overpayments.**

