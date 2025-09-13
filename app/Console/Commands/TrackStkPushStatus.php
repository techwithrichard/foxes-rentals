<?php

namespace App\Console\Commands;

use App\Models\StkRequest;
use App\Models\C2bRequest;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Console\Command;

class TrackStkPushStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'track:stk-status {--phone=254720691181} {--watch} {--checkout-id=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Track ALL STK Push statuses including cancelled, insufficient funds, wrong PIN, etc.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $phone = $this->option('phone');
        $watch = $this->option('watch');
        $checkoutId = $this->option('checkout-id');
        
        $this->info("📱 TRACKING ALL STK PUSH STATUSES");
        $this->info("Phone: {$phone}");
        
        if ($checkoutId) {
            $this->info("Checkout ID: {$checkoutId}");
            $this->trackSpecificRequest($checkoutId);
        } elseif ($watch) {
            $this->info("👀 Watch mode enabled - monitoring for status changes...");
            $this->watchStkStatus($phone);
        } else {
            $this->displayCurrentStatus($phone);
        }

        return 0;
    }

    protected function displayCurrentStatus($phone)
    {
        $this->info("");
        $this->info("📊 CURRENT STK PUSH STATUSES:");

        // Get all STK requests for this phone
        $stkRequests = StkRequest::where('phone', $phone)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($stkRequests->isEmpty()) {
            $this->warn("⚠️  No STK requests found for phone: {$phone}");
        } else {
            $this->info("📱 STK Push Requests:");
            foreach ($stkRequests as $request) {
                $statusIcon = $this->getStatusIcon($request->status);
                $this->info("  {$statusIcon} Amount: Ksh " . number_format($request->amount, 2));
                $this->info("     Status: {$request->status}");
                $this->info("     Checkout ID: {$request->CheckoutRequestID}");
                $this->info("     Created: {$request->created_at->format('Y-m-d H:i:s')}");
                $this->info("     Reference: {$request->reference}");
                $this->info("");
            }
        }

        // Get recent C2B requests (successful payments)
        $c2bRequests = C2bRequest::where('MSISDN', $phone)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        if ($c2bRequests->isEmpty()) {
            $this->warn("⚠️  No C2B requests found for phone: {$phone}");
        } else {
            $this->info("💰 C2B Requests (Successful Payments):");
            foreach ($c2bRequests as $request) {
                $this->info("  ✅ Amount: Ksh " . number_format($request->TransAmount, 2));
                $this->info("     Transaction ID: {$request->TransID}");
                $this->info("     Created: {$request->created_at->format('Y-m-d H:i:s')}");
                $this->info("");
            }
        }

        // Get recent payments
        $tenant = User::where('phone', $phone)->first();
        if ($tenant) {
            $payments = Payment::where('tenant_id', $tenant->id)
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            if ($payments->isEmpty()) {
                $this->warn("⚠️  No payments found for tenant");
            } else {
                $this->info("💳 Payment Records:");
                foreach ($payments as $payment) {
                    $status = is_object($payment->status) ? $payment->status->value : $payment->status;
                    $statusIcon = $status === 'paid' ? '✅' : ($status === 'pending' ? '⏳' : '❌');
                    $this->info("  {$statusIcon} Amount: Ksh " . number_format($payment->amount, 2));
                    $this->info("     Method: {$payment->payment_method}");
                    $this->info("     Status: {$status}");
                    $this->info("     Created: {$payment->created_at->format('Y-m-d H:i:s')}");
                    $this->info("");
                }
            }
        }

        $this->info("🔍 Available Status Types:");
        $this->info("  📱 Request Sent - STK push initiated");
        $this->info("  ⏳ Pending - Waiting for user action");
        $this->info("  ✅ Completed - Payment successful");
        $this->info("  ❌ Cancelled - User cancelled");
        $this->info("  💸 Insufficient Funds - Not enough balance");
        $this->info("  🔒 Wrong PIN - Incorrect PIN entered");
        $this->info("  ⚠️  Timeout - Request expired");
        $this->info("  🚫 Failed - Other failure reasons");
    }

    protected function trackSpecificRequest($checkoutId)
    {
        $this->info("🔍 Tracking specific STK request: {$checkoutId}");
        
        $stkRequest = StkRequest::where('CheckoutRequestID', $checkoutId)->first();
        
        if (!$stkRequest) {
            $this->error("❌ STK request not found with Checkout ID: {$checkoutId}");
            return;
        }

        $this->info("📱 STK Request Details:");
        $this->info("  Phone: {$stkRequest->phone}");
        $this->info("  Amount: Ksh " . number_format($stkRequest->amount, 2));
        $this->info("  Status: {$stkRequest->status}");
        $this->info("  Reference: {$stkRequest->reference}");
        $this->info("  Created: {$stkRequest->created_at->format('Y-m-d H:i:s')}");
        $this->info("  Updated: {$stkRequest->updated_at->format('Y-m-d H:i:s')}");

        // Check for related C2B request
        $c2bRequest = C2bRequest::where('MSISDN', $stkRequest->phone)
            ->where('TransAmount', $stkRequest->amount)
            ->where('created_at', '>=', $stkRequest->created_at)
            ->first();

        if ($c2bRequest) {
            $this->info("✅ Related C2B Request Found:");
            $this->info("  Transaction ID: {$c2bRequest->TransID}");
            $this->info("  Amount: Ksh " . number_format($c2bRequest->TransAmount, 2));
            $this->info("  Created: {$c2bRequest->created_at->format('Y-m-d H:i:s')}");
        } else {
            $this->warn("⚠️  No related C2B request found - payment may not have completed");
        }
    }

    protected function watchStkStatus($phone)
    {
        $this->info("Starting STK status monitoring... Press Ctrl+C to stop");
        
        $lastStkCount = StkRequest::where('phone', $phone)->count();
        $lastC2bCount = C2bRequest::where('MSISDN', $phone)->count();
        $lastPaymentCount = 0;
        
        $tenant = User::where('phone', $phone)->first();
        if ($tenant) {
            $lastPaymentCount = Payment::where('tenant_id', $tenant->id)->count();
        }

        while (true) {
            sleep(3); // Check every 3 seconds

            $currentStkCount = StkRequest::where('phone', $phone)->count();
            $currentC2bCount = C2bRequest::where('MSISDN', $phone)->count();
            $currentPaymentCount = 0;
            
            if ($tenant) {
                $currentPaymentCount = Payment::where('tenant_id', $tenant->id)->count();
            }

            // Check for new STK requests
            if ($currentStkCount > $lastStkCount) {
                $this->info("🆕 New STK request detected!");
                $newRequests = StkRequest::where('phone', $phone)
                    ->orderBy('created_at', 'desc')
                    ->limit($currentStkCount - $lastStkCount)
                    ->get();
                
                foreach ($newRequests as $request) {
                    $statusIcon = $this->getStatusIcon($request->status);
                    $this->info("  {$statusIcon} New STK Request:");
                    $this->info("    Amount: Ksh " . number_format($request->amount, 2));
                    $this->info("    Status: {$request->status}");
                    $this->info("    Checkout ID: {$request->CheckoutRequestID}");
                    $this->info("    Time: {$request->created_at->format('H:i:s')}");
                }
                $lastStkCount = $currentStkCount;
            }

            // Check for STK status updates
            $recentStkRequests = StkRequest::where('phone', $phone)
                ->where('updated_at', '>', now()->subSeconds(10))
                ->get();

            foreach ($recentStkRequests as $request) {
                $this->info("🔄 STK Status Update:");
                $this->info("  Checkout ID: {$request->CheckoutRequestID}");
                $this->info("  New Status: {$request->status}");
                $this->info("  Updated: {$request->updated_at->format('H:i:s')}");
                
                // Interpret status
                $this->interpretStatus($request->status);
            }

            // Check for new C2B requests (successful payments)
            if ($currentC2bCount > $lastC2bCount) {
                $this->info("💰 New C2B request detected (Payment successful)!");
                $newC2bRequests = C2bRequest::where('MSISDN', $phone)
                    ->orderBy('created_at', 'desc')
                    ->limit($currentC2bCount - $lastC2bCount)
                    ->get();
                
                foreach ($newC2bRequests as $request) {
                    $this->info("  ✅ Payment Successful:");
                    $this->info("    Amount: Ksh " . number_format($request->TransAmount, 2));
                    $this->info("    Transaction ID: {$request->TransID}");
                    $this->info("    Time: {$request->created_at->format('H:i:s')}");
                }
                $lastC2bCount = $currentC2bCount;
            }

            // Check for new payments
            if ($tenant && $currentPaymentCount > $lastPaymentCount) {
                $this->info("💳 New payment record created!");
                $newPayments = Payment::where('tenant_id', $tenant->id)
                    ->orderBy('created_at', 'desc')
                    ->limit($currentPaymentCount - $lastPaymentCount)
                    ->get();
                
                foreach ($newPayments as $payment) {
                    $status = is_object($payment->status) ? $payment->status->value : $payment->status;
                    $statusIcon = $status === 'paid' ? '✅' : ($status === 'pending' ? '⏳' : '❌');
                    $this->info("  {$statusIcon} Payment Record:");
                    $this->info("    Amount: Ksh " . number_format($payment->amount, 2));
                    $this->info("    Method: {$payment->payment_method}");
                    $this->info("    Status: {$status}");
                    $this->info("    Time: {$payment->created_at->format('H:i:s')}");
                }
                $lastPaymentCount = $currentPaymentCount;
            }

            // Display timestamp every 30 seconds
            if (time() % 30 === 0) {
                $this->info("👀 Monitoring STK statuses... " . now()->format('H:i:s'));
            }
        }
    }

    protected function getStatusIcon($status)
    {
        return match(strtolower($status)) {
            'request sent' => '📱',
            'pending' => '⏳',
            'completed' => '✅',
            'cancelled' => '❌',
            'insufficient funds' => '💸',
            'wrong pin' => '🔒',
            'timeout' => '⚠️',
            'failed' => '🚫',
            default => '❓'
        };
    }

    protected function interpretStatus($status)
    {
        $status = strtolower($status);
        
        switch ($status) {
            case 'request sent':
                $this->info("  📱 STK push has been sent to your phone");
                break;
            case 'pending':
                $this->info("  ⏳ Waiting for you to respond on your phone");
                break;
            case 'completed':
                $this->info("  ✅ Payment completed successfully!");
                break;
            case 'cancelled':
                $this->info("  ❌ You cancelled the payment on your phone");
                break;
            case 'insufficient funds':
                $this->info("  💸 Not enough balance in your M-PESA account");
                break;
            case 'wrong pin':
                $this->info("  🔒 You entered the wrong M-PESA PIN");
                break;
            case 'timeout':
                $this->info("  ⚠️  Payment request expired (took too long)");
                break;
            case 'failed':
                $this->info("  🚫 Payment failed for other reasons");
                break;
            default:
                $this->info("  ❓ Unknown status: {$status}");
        }
    }
}

