<?php

namespace App\Console\Commands;

use App\Models\C2bRequest;
use App\Services\PaymentReconciliationService;
use Illuminate\Console\Command;

class ProcessPendingC2bRequests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'process:pending-c2b-requests {--limit=10} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending C2B requests that may not have been automatically reconciled';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $limit = $this->option('limit');
        $force = $this->option('force');
        
        $this->info("🔄 Processing Pending C2B Requests...");
        $this->newLine();

        // Get pending C2B requests
        $pendingRequests = C2bRequest::where('reconciliation_status', 'pending')
            ->latest()
            ->limit($limit)
            ->get();

        if ($pendingRequests->isEmpty()) {
            $this->info("✅ No pending C2B requests found!");
            return;
        }

        $this->info("📋 Found {$pendingRequests->count()} pending C2B requests:");
        
        $reconciliationService = new PaymentReconciliationService();
        $processed = 0;
        $failed = 0;

        foreach ($pendingRequests as $request) {
            $this->info("  🔄 Processing: {$request->TransID} - {$request->MSISDN} - Ksh {$request->TransAmount}");
            
            try {
                // Attempt to process the C2B request
                $result = $reconciliationService->processC2bCallback([
                    'MSISDN' => $request->MSISDN,
                    'TransAmount' => $request->TransAmount,
                    'TransID' => $request->TransID,
                ]);

                if ($result['success']) {
                    $this->info("    ✅ Successfully processed: {$result['message']}");
                    $processed++;
                    
                    // Update reconciliation status
                    $request->reconciliation_status = 'reconciled';
                    $request->save();
                } else {
                    $this->warn("    ⚠️ Processing failed: {$result['message']}");
                    $failed++;
                }
                
            } catch (\Exception $e) {
                $this->error("    ❌ Error processing {$request->TransID}: " . $e->getMessage());
                $failed++;
            }
            
            $this->newLine();
        }

        $this->info("📊 Processing Summary:");
        $this->info("  ✅ Successfully processed: {$processed}");
        $this->info("  ❌ Failed to process: {$failed}");
        
        if ($failed > 0) {
            $this->warn("⚠️ Some requests failed to process. They may require manual reconciliation.");
            $this->info("💡 Use the admin panel to manually reconcile failed requests:");
            $this->info("   Admin → MPesa Transactions → C2B Transactions");
        }
    }
}

