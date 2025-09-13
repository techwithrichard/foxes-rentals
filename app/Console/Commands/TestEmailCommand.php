<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email=kipkoech25.richard@student.cuk.ac.ke}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email configuration by sending a test email';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        try {
            Mail::raw('This is a test email from Foxes Rental Management System. If you receive this, your email configuration is working correctly!', function ($message) use ($email) {
                $message->to($email)
                        ->subject('Test Email - Foxes Rental System');
            });
            
            $this->info("✅ Test email sent successfully to: {$email}");
            $this->info("📧 Check your inbox to confirm the email was received.");
            
        } catch (\Exception $e) {
            $this->error("❌ Failed to send email: " . $e->getMessage());
            $this->error("💡 Make sure you're using a Gmail App Password, not your regular password.");
            $this->error("🔗 Generate app password: https://myaccount.google.com/apppasswords");
        }
    }
}
