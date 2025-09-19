<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Services\RoleBasedAccessControlService;
use App\Services\PermissionManagementService;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class EnhancedAuthSeeder extends Seeder
{
    protected RoleBasedAccessControlService $rbacService;
    protected PermissionManagementService $permissionService;

    public function __construct(
        RoleBasedAccessControlService $rbacService,
        PermissionManagementService $permissionService
    ) {
        $this->rbacService = $rbacService;
        $this->permissionService = $permissionService;
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Initializing enhanced authentication system...');

        // Initialize permissions
        $this->command->info('Creating system permissions...');
        $this->rbacService->initializePermissions();

        // Initialize roles
        $this->command->info('Creating system roles...');
        $this->rbacService->initializeRoles();

        // Create super admin user if it doesn't exist
        $this->command->info('Creating super admin user...');
        $this->createSuperAdmin();

        // Create sample users for testing
        $this->command->info('Creating sample users...');
        $this->createSampleUsers();

        $this->command->info('Enhanced authentication system initialized successfully!');
    }

    /**
     * Create super admin user
     */
    protected function createSuperAdmin(): void
    {
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@foxesrentals.com'],
            [
                'name' => 'Super Administrator',
                'password' => bcrypt('SuperAdmin123!'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        if (!$superAdmin->hasRole('super_admin')) {
            $superAdmin->assignRole('super_admin');
        }

        $this->command->info("Super admin created: {$superAdmin->email}");
    }

    /**
     * Create sample users for testing
     */
    protected function createSampleUsers(): void
    {
        $sampleUsers = [
            [
                'name' => 'John Admin',
                'email' => 'admin@foxesrentals.com',
                'password' => bcrypt('Admin123!'),
                'role' => 'admin',
            ],
            [
                'name' => 'Sarah Manager',
                'email' => 'manager@foxesrentals.com',
                'password' => bcrypt('Manager123!'),
                'role' => 'manager',
            ],
            [
                'name' => 'Mike Agent',
                'email' => 'agent@foxesrentals.com',
                'password' => bcrypt('Agent123!'),
                'role' => 'agent',
            ],
            [
                'name' => 'David Landlord',
                'email' => 'landlord@foxesrentals.com',
                'password' => bcrypt('Landlord123!'),
                'role' => 'landlord',
            ],
            [
                'name' => 'Jane Tenant',
                'email' => 'tenant@foxesrentals.com',
                'password' => bcrypt('Tenant123!'),
                'role' => 'tenant',
            ],
        ];

        foreach ($sampleUsers as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $userData['password'],
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]
            );

            if (!$user->hasRole($userData['role'])) {
                $user->assignRole($userData['role']);
            }

            $this->command->info("Sample user created: {$user->email} ({$userData['role']})");
        }
    }
}

