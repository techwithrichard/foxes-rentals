<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class UpdateNgrokUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:ngrok-urls';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update M-PESA callback URLs with new ngrok URL';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("🔧 Updating M-PESA callback URLs...");

        try {
            // Get the current ngrok URL
            $response = Http::get('http://localhost:4040/api/tunnels');
            $data = $response->json();
            
            if (isset($data['tunnels'][0]['public_url'])) {
                $ngrokUrl = $data['tunnels'][0]['public_url'];
                $this->info("✅ Found ngrok URL: {$ngrokUrl}");
                
                // Update the .env file
                $envFile = base_path('.env');
                $envContent = file_get_contents($envFile);
                
                // Update the URLs
                $newUrls = [
                    'MPESA_CONFIRMATION_URL' => $ngrokUrl . '/api/callback/confirmation',
                    'MPESA_VALIDATION_URL' => $ngrokUrl . '/api/callback/validation',
                    'MPESA_STK_CALLBACK_URL' => $ngrokUrl . '/api/callback/stk_callback',
                ];
                
                foreach ($newUrls as $key => $url) {
                    $pattern = "/^{$key}=.*$/m";
                    $replacement = "{$key}={$url}";
                    
                    if (preg_match($pattern, $envContent)) {
                        $envContent = preg_replace($pattern, $replacement, $envContent);
                        $this->info("✅ Updated {$key}: {$url}");
                    } else {
                        $envContent .= "\n{$replacement}";
                        $this->info("✅ Added {$key}: {$url}");
                    }
                }
                
                // Write the updated content back to .env
                file_put_contents($envFile, $envContent);
                
                // Clear config cache
                $this->call('config:clear');
                
                $this->info("");
                $this->info("🎉 M-PESA URLs updated successfully!");
                $this->info("New URLs:");
                foreach ($newUrls as $key => $url) {
                    $this->info("  {$key}: {$url}");
                }
                
                $this->info("");
                $this->info("📱 Now you can test STK Push again!");
                $this->info("php artisan test:stk-direct --phone=254720691181 --amount=100");
                
            } else {
                $this->error("❌ Could not find ngrok URL. Make sure ngrok is running.");
                return 1;
            }
            
        } catch (\Exception $e) {
            $this->error("❌ Error updating URLs: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}

