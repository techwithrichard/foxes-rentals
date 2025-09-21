<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\HouseType;
use App\Models\PropertyType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        //clear

        DB::table('house_types')->delete();
        DB::table('property_types')->delete();

        // Use expert-designed property types seeder
        $this->call(ExpertPropertyTypesSeeder::class);

        HouseType::create(['name' => 'Detached']);
        HouseType::create(['name' => 'Semi-Detached']);
        HouseType::create(['name' => 'Terraced']);


        //run RolesSeeder
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
    }
}
