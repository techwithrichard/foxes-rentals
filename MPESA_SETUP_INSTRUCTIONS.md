# M-Pesa API Setup Instructions

## Current Issue
The "Merchant does not exist" error is caused by invalid M-Pesa API credentials.

## Solution Steps

### 1. Register for M-Pesa API Access
1. Go to [Safaricom Developer Portal](https://developer.safaricom.co.ke/)
2. Create an account and register your application
3. Get your sandbox credentials:
   - Consumer Key
   - Consumer Secret
   - Passkey

### 2. Update Environment Configuration
Update your `.env` file with the correct credentials:

```env
# Replace these with your actual sandbox credentials
MPESA_CONSUMER_KEY=your_actual_consumer_key_here
MPESA_CONSUMER_SECRET=your_actual_consumer_secret_here
MPESA_PASSKEY=your_actual_passkey_here

# These should remain the same for sandbox
MPESA_SHORTCODE=174379
MPESA_PAYBILL=174379
MPESA_ENV=sandbox
```

### 3. Test Configuration
After updating credentials, run:
```bash
php artisan config:clear
php artisan mpesa:test-config
```

### 4. Common Sandbox Credentials (for testing only)
If you need test credentials immediately, you can use these (but they may not work):
- Consumer Key: `c779zA18GytQZv2wwrG79LiZLMMt3oi6DvbtiQGNlxb26Wau`
- Consumer Secret: `HmPmTuCE9WB5NZ8hH124H9WW1HC9cIJSZaSRzRoHVpE2G7Guo5ktU9hWW9uQNZf0`
- Passkey: `bfb279f9aa9bdbcf158e97dd71a467cd2e0c893059b10f78e6b72ada1ed2c919`

**Note**: These credentials may be expired or invalid. You should get your own from Safaricom.

## What We Fixed
1. ✅ Corrected business shortcode from 600986 to 174379
2. ✅ Fixed PartyB parameter in STK push request
3. ✅ Added comprehensive error logging
4. ✅ Created test command for debugging
5. ✅ Improved error handling

## Next Steps
1. Get valid M-Pesa API credentials from Safaricom
2. Update your .env file
3. Test the payment flow
4. If successful, proceed to production setup
