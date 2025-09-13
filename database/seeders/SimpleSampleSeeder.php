<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class SimpleSampleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Starting to create sample data...');
        
        try {
            // Create Admin User
            $admin = User::create([
                'name' => 'System Administrator',
                'email' => 'admin@foxesrental.com',
                'password' => Hash::make('admin123'),
                'phone' => '+254700000000',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Admin user created: ' . $admin->email);
            
            // Create Landlord
            $landlord = User::create([
                'name' => 'John Kamau',
                'email' => 'john.kamau@email.com',
                'password' => Hash::make('landlord123'),
                'phone' => '+254712345678',
                'role' => 'landlord',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Landlord created: ' . $landlord->email);
            
            // Create Tenant
            $tenant = User::create([
                'name' => 'Peter Mwangi',
                'email' => 'peter.mwangi@email.com',
                'password' => Hash::make('tenant123'),
                'phone' => '+254734567890',
                'role' => 'tenant',
                'email_verified_at' => now(),
            ]);
            
            $this->command->info('Tenant created: ' . $tenant->email);
            
            $this->command->info('Sample data created successfully!');
            $this->command->info('Login credentials:');
            $this->command->info('Admin: admin@foxesrental.com / admin123');
            $this->command->info('Landlord: john.kamau@email.com / landlord123');
            $this->command->info('Tenant: peter.mwangi@email.com / tenant123');
            
        } catch (\Exception $e) {
            $this->command->error('Error creating sample data: ' . $e->getMessage());
        }
    }
}
