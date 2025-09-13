<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class FixPropertySeeder extends Seeder
{
    public function run()
    {
        // Update the existing property to be multi-unit so houses can be added to it
        $property = Property::first();
        if ($property) {
            $property->is_multi_unit = 1;
            $property->save();
            echo "Updated property '{$property->name}' to multi-unit\n";
        } else {
            echo "No properties found to update\n";
        }
    }
}
