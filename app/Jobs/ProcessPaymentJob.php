<?php

namespace App\Jobs;

use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentProcessedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 120;
    public $tries = 3;

    protected Payment $payment;
    protected User $user;

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment, User $user)
    {
        $this->payment = $payment;
        $this->user = $user;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Processing payment job started", [
                'payment_id' => $this->payment->id,
                'user_id' => $this->user->id,
                'amount' => $this->payment->amount
            ]);

            // Process payment logic
            $this->processPayment();

            // Send notifications
            $this->sendNotifications();

            // Update statistics
            $this->updateStatistics();

            // Clear related caches
            $this->clearCaches();

            Log::info("Payment job completed successfully", [
                'payment_id' => $this->payment->id
            ]);

        } catch (\Exception $e) {
            Log::error("Payment job failed", [
                'payment_id' => $this->payment->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Process the payment
     */
    private function processPayment(): void
    {
        // Update payment status
        $this->payment->update([
            'status' => 'completed',
            'verified_at' => now(),
            'verified_by' => $this->user->id
        ]);

        // Update related invoice if exists
        if ($this->payment->invoice) {
            $this->payment->invoice->update([
                'status' => 'paid',
                'paid_at' => now()
            ]);
        }

        // Update property vacancy status if needed
        if ($this->payment->property) {
            $this->payment->property->update([
                'is_vacant' => false
            ]);
        }
    }

    /**
     * Send notifications
     */
    private function sendNotifications(): void
    {
        // Notify tenant
        if ($this->payment->tenant) {
            $this->payment->tenant->notify(
                new PaymentProcessedNotification($this->payment)
            );
        }

        // Notify landlord
        if ($this->payment->property && $this->payment->property->landlord) {
            $this->payment->property->landlord->notify(
                new PaymentProcessedNotification($this->payment)
            );
        }
    }

    /**
     * Update statistics
     */
    private function updateStatistics(): void
    {
        // Update property statistics
        if ($this->payment->property) {
            $property = $this->payment->property;
            $property->increment('total_revenue', $this->payment->amount);
        }

        // Update user statistics
        $this->user->increment('total_payments_processed');
    }

    /**
     * Clear related caches
     */
    private function clearCaches(): void
    {
        $cacheKeys = [
            'property_stats',
            'payment_stats',
            'dashboard_data',
            'user_stats'
        ];

        foreach ($cacheKeys as $key) {
            Cache::forget($key);
        }

        // Clear property-specific cache
        if ($this->payment->property) {
            Cache::forget("property_{$this->payment->property->id}_stats");
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Payment job failed permanently", [
            'payment_id' => $this->payment->id,
            'error' => $exception->getMessage()
        ]);

        // Update payment status to failed
        $this->payment->update([
            'status' => 'failed',
            'notes' => 'Payment processing failed: ' . $exception->getMessage()
        ]);
    }
}
