<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SimpleAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating admin accounts...');

        // Create Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@foxesrentals.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('SuperAdmin123!'),
                'email_verified_at' => now(),
                'phone' => '+254700000000',
            ]
        );

        // Assign admin role (since super_admin might not exist)
        if (!$superAdmin->hasRole('admin')) {
            $superAdmin->assignRole('admin');
        }

        $this->command->info("✅ Super Admin created:");
        $this->command->info("   Email: superadmin@foxesrentals.com");
        $this->command->info("   Password: SuperAdmin123!");
        $this->command->info("   Role: admin");

        // Create Regular Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@foxesrentals.com'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('Admin123!'),
                'email_verified_at' => now(),
                'phone' => '+254700000001',
            ]
        );

        // Assign admin role
        if (!$admin->hasRole('admin')) {
            $admin->assignRole('admin');
        }

        $this->command->info("✅ Admin created:");
        $this->command->info("   Email: admin@foxesrentals.com");
        $this->command->info("   Password: Admin123!");
        $this->command->info("   Role: admin");

        // Create Test Manager
        $manager = User::updateOrCreate(
            ['email' => 'manager@foxesrentals.com'],
            [
                'name' => 'Property Manager',
                'password' => Hash::make('Manager123!'),
                'email_verified_at' => now(),
                'phone' => '+254700000002',
            ]
        );

        // Assign agent role (since manager might not exist)
        if (!$manager->hasRole('agent')) {
            $manager->assignRole('agent');
        }

        $this->command->info("✅ Manager created:");
        $this->command->info("   Email: manager@foxesrentals.com");
        $this->command->info("   Password: Manager123!");
        $this->command->info("   Role: agent");

        $this->command->info('');
        $this->command->info('🔐 Login Credentials Summary:');
        $this->command->info('================================');
        $this->command->info('Super Admin: superadmin@foxesrentals.com / SuperAdmin123!');
        $this->command->info('Admin:        admin@foxesrentals.com / Admin123!');
        $this->command->info('Manager:      manager@foxesrentals.com / Manager123!');
        $this->command->info('================================');
        $this->command->info('');
        $this->command->info('⚠️  IMPORTANT: Please change these passwords after first login!');
        $this->command->info('   You can do this in the user profile section.');
    }
}

