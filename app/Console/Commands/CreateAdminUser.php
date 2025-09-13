<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user if it does not exist';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting admin user creation...');
        
        // Check if admin user already exists
        $adminUser = User::where('email', 'admin@admin.com')->first();
        
        if ($adminUser) {
            $this->info('Admin user already exists: ' . $adminUser->name);
            return;
        }

        $this->info('Admin user not found, creating...');

        // Create roles if they don't exist
        $roles = ['tenant', 'landlord', 'agent', 'admin'];
        foreach ($roles as $roleName) {
            if (!Role::where('name', $roleName)->exists()) {
                Role::create(['name' => $roleName]);
                $this->info("Created role: {$roleName}");
            } else {
                $this->info("Role {$roleName} already exists");
            }
        }

        // Create admin user
        try {
            $user = User::create([
                'name' => 'Administrator',
                'email' => 'admin@admin.com',
                'password' => bcrypt('demo123#'),
                'email_verified_at' => now(),
            ]);

            $user->assignRole('admin');

            $this->info('Admin user created successfully!');
            $this->info('Email: admin@admin.com');
            $this->info('Password: demo123#');
        } catch (\Exception $e) {
            $this->error('Error creating admin user: ' . $e->getMessage());
        }
    }
}
