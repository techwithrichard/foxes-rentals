<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //truncate table
//        \DB::table('roles')->truncate();




        //create roles tenant,admin and agent

        Role::create(['name' => 'tenant']);
        Role::create(['name' => 'landlord']);
        Role::create(['name' => 'agent']);
        Role::create(['name' => 'admin']);

        //create default admin user

        $user = User::create([
            'name' => 'Administrator',
            'email' => 'admin@admin.com',
            'password' => bcrypt('demo123#'),
            'email_verified_at' => now(),
        ]);

        $user->assignRole('admin');


    }
}
