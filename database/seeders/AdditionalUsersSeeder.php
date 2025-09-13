<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdditionalUsersSeeder extends Seeder
{
    public function run()
    {
        // Create Landlord User
        $landlord = User::create([
            'name' => 'John Landlord',
            'email' => 'landlord@test.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $landlord->assignRole('landlord');

        // Create Tenant User
        $tenant = User::create([
            'name' => 'Jane Tenant',
            'email' => 'tenant@test.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $tenant->assignRole('tenant');

        // Create Staff User
        $staff = User::create([
            'name' => 'Mike Staff',
            'email' => 'staff@test.com',
            'password' => bcrypt('password123'),
            'email_verified_at' => now(),
        ]);
        $staff->assignRole('agent');
    }
}
