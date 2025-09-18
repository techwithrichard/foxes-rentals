<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class EmergencyAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating emergency admin accounts...');

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

        // Assign super admin role
        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        $this->command->info("✅ Super Admin created:");
        $this->command->info("   Email: superadmin@foxesrentals.com");
        $this->command->info("   Password: SuperAdmin123!");
        $this->command->info("   Role: super_admin");

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

        // Assign manager role
        if (!$manager->hasRole('manager')) {
            $manager->assignRole('manager');
        }

        $this->command->info("✅ Manager created:");
        $this->command->info("   Email: manager@foxesrentals.com");
        $this->command->info("   Password: Manager123!");
        $this->command->info("   Role: manager");

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

        Log::info('Emergency admin accounts created', [
            'super_admin_email' => 'superadmin@foxesrentals.com',
            'admin_email' => 'admin@foxesrentals.com',
            'manager_email' => 'manager@foxesrentals.com',
        ]);
    }
}
