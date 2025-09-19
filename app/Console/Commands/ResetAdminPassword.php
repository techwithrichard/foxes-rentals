<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ResetAdminPassword extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'admin:reset-password {email} {password}';

    /**
     * The console command description.
     */
    protected $description = 'Reset password for an admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');

        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        // Update password
        $user->update([
            'password' => Hash::make($password),
            'password_changed_at' => now(),
        ]);

        $this->info("✅ Password reset successfully for {$email}");
        $this->info("New password: {$password}");
        $this->warn("⚠️  Please change this password after logging in!");

        return 0;
    }
}

