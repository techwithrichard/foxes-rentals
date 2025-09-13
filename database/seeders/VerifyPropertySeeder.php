<?php

namespace Database\Seeders;

use App\Models\Property;
use Illuminate\Database\Seeder;

class VerifyPropertySeeder extends Seeder
{
    public function run()
    {
        $properties = Property::all();
        echo "Properties available for house creation: " . $properties->count() . "\n";
        
        foreach ($properties as $property) {
            echo "- " . $property->name . " (Multi-unit: " . ($property->is_multi_unit ? 'Yes' : 'No') . ")\n";
        }
    }
}
