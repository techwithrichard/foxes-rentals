<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyTypes = [
            // Residential Properties
            ['name' => 'Villa', 'description' => 'Luxury standalone house with garden', 'category' => 'residential', 'icon' => 'ni-home', 'color' => '#3B82F6', 'sort_order' => 1],
            ['name' => 'Apartment', 'description' => 'Multi-unit residential building', 'category' => 'residential', 'icon' => 'ni-building', 'color' => '#10B981', 'sort_order' => 2],
            ['name' => 'House', 'description' => 'Single-family residential house', 'category' => 'residential', 'icon' => 'ni-home-alt', 'color' => '#F59E0B', 'sort_order' => 3],
            ['name' => 'Studio', 'description' => 'Single room with kitchen and bathroom', 'category' => 'residential', 'icon' => 'ni-home-smile', 'color' => '#EF4444', 'sort_order' => 4],
            ['name' => 'Townhouse', 'description' => 'Multi-story attached house', 'category' => 'residential', 'icon' => 'ni-home-2', 'color' => '#8B5CF6', 'sort_order' => 5],
            ['name' => 'Penthouse', 'description' => 'Luxury apartment on top floor', 'category' => 'residential', 'icon' => 'ni-home-star', 'color' => '#EC4899', 'sort_order' => 6],
            ['name' => 'Duplex', 'description' => 'Two-story house with separate entrances', 'category' => 'residential', 'icon' => 'ni-home-alt-2', 'color' => '#06B6D4', 'sort_order' => 7],
            ['name' => 'Bungalow', 'description' => 'Single-story house', 'category' => 'residential', 'icon' => 'ni-home-3', 'color' => '#84CC16', 'sort_order' => 8],
            
            // Commercial Properties
            ['name' => 'Office Space', 'description' => 'Commercial office building or space', 'category' => 'commercial', 'icon' => 'ni-building-alt', 'color' => '#6366F1', 'sort_order' => 9],
            ['name' => 'Retail Space', 'description' => 'Commercial space for retail business', 'category' => 'commercial', 'icon' => 'ni-shop', 'color' => '#F97316', 'sort_order' => 10],
            ['name' => 'Warehouse', 'description' => 'Large storage and distribution facility', 'category' => 'commercial', 'icon' => 'ni-building-2', 'color' => '#64748B', 'sort_order' => 11],
            ['name' => 'Restaurant', 'description' => 'Commercial space for food service', 'category' => 'commercial', 'icon' => 'ni-restaurant', 'color' => '#DC2626', 'sort_order' => 12],
            ['name' => 'Hotel', 'description' => 'Commercial accommodation facility', 'category' => 'commercial', 'icon' => 'ni-hotel', 'color' => '#059669', 'sort_order' => 13],
            ['name' => 'Shopping Mall', 'description' => 'Large commercial shopping complex', 'category' => 'commercial', 'icon' => 'ni-mall', 'color' => '#7C3AED', 'sort_order' => 14],
            
            // Industrial Properties
            ['name' => 'Factory', 'description' => 'Industrial manufacturing facility', 'category' => 'industrial', 'icon' => 'ni-factory', 'color' => '#374151', 'sort_order' => 15],
            ['name' => 'Industrial Land', 'description' => 'Land zoned for industrial use', 'category' => 'industrial', 'icon' => 'ni-land', 'color' => '#6B7280', 'sort_order' => 16],
            
            // Land Properties
            ['name' => 'Residential Land', 'description' => 'Land zoned for residential development', 'category' => 'land', 'icon' => 'ni-land-alt', 'color' => '#22C55E', 'sort_order' => 17],
            ['name' => 'Commercial Land', 'description' => 'Land zoned for commercial development', 'category' => 'land', 'icon' => 'ni-land-2', 'color' => '#3B82F6', 'sort_order' => 18],
            ['name' => 'Agricultural Land', 'description' => 'Land suitable for farming or agriculture', 'category' => 'land', 'icon' => 'ni-land-3', 'color' => '#16A34A', 'sort_order' => 19],
            ['name' => 'Vacant Land', 'description' => 'Undeveloped land for various uses', 'category' => 'land', 'icon' => 'ni-land-4', 'color' => '#84CC16', 'sort_order' => 20],
        ];

        foreach ($propertyTypes as $type) {
            PropertyType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
