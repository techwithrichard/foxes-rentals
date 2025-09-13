<?php

namespace App\Console\Commands;

use App\Services\MPesaHelper;
use Illuminate\Console\Command;

class RegisterMpesaUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'register:mpesa-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Register M-PESA callback URLs with Safaricom';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("📡 Registering M-PESA callback URLs...");
        
        try {
            $response = MPesaHelper::registerURLS();
            
            $this->info("Response:");
            $this->info(json_encode($response, JSON_PRETTY_PRINT));
            
            if (isset($response['ResponseCode']) && $response['ResponseCode'] == '0') {
                $this->info("✅ URLs registered successfully!");
            } else {
                $this->warn("⚠️  URL registration response: " . ($response['ResponseDescription'] ?? 'Unknown'));
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error registering URLs: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

