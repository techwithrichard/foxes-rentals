<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;
use Illuminate\Support\Facades\DB;

class ComprehensivePropertyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Seeding comprehensive property types...');

        // Clear existing property types
        DB::table('property_types')->delete();

        $propertyTypes = [
            // 🏠 Residential Properties
            [
                'name' => 'Single-Family Homes',
                'description' => 'Standalone residential properties designed for single families, offering privacy and space.',
                'category' => 'residential',
                'icon' => 'ni ni-home',
                'color' => '#4CAF50',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Condominiums & Townhouses',
                'description' => 'Multi-unit residential properties with shared amenities and individual ownership.',
                'category' => 'residential',
                'icon' => 'ni ni-building',
                'color' => '#2196F3',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Apartments & Rental Homes',
                'description' => 'Multi-unit residential buildings with individual rental units for tenants.',
                'category' => 'residential',
                'icon' => 'ni ni-apartment',
                'color' => '#FF9800',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Duplexes & Multi-Unit Residences',
                'description' => 'Residential properties with two or more separate living units under one roof.',
                'category' => 'residential',
                'icon' => 'ni ni-house',
                'color' => '#9C27B0',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Vacation Rentals',
                'description' => 'Short-term rental properties for tourists and travelers.',
                'category' => 'residential',
                'icon' => 'ni ni-calendar',
                'color' => '#E91E63',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Rooms & Shared Housing',
                'description' => 'Individual rooms or shared living spaces within larger residential properties.',
                'category' => 'residential',
                'icon' => 'ni ni-room',
                'color' => '#607D8B',
                'sort_order' => 6,
                'is_active' => true,
            ],

            // 🏢 Commercial Properties
            [
                'name' => 'Office Buildings & Suites',
                'description' => 'Commercial properties designed for business operations and professional services.',
                'category' => 'commercial',
                'icon' => 'ni ni-office',
                'color' => '#3F51B5',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Retail Storefronts & Units',
                'description' => 'Commercial spaces designed for retail businesses and customer-facing operations.',
                'category' => 'commercial',
                'icon' => 'ni ni-shop',
                'color' => '#FF5722',
                'sort_order' => 8,
                'is_active' => true,
            ],
            [
                'name' => 'Warehouses & Industrial Facilities',
                'description' => 'Large commercial spaces for storage, manufacturing, and industrial operations.',
                'category' => 'industrial',
                'icon' => 'ni ni-warehouse',
                'color' => '#795548',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Co-working & Flex Spaces',
                'description' => 'Flexible commercial spaces designed for modern work environments and collaboration.',
                'category' => 'commercial',
                'icon' => 'ni ni-users',
                'color' => '#00BCD4',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant & Hospitality Spaces',
                'description' => 'Commercial properties designed for food service and hospitality businesses.',
                'category' => 'commercial',
                'icon' => 'ni ni-restaurant',
                'color' => '#CDDC39',
                'sort_order' => 11,
                'is_active' => true,
            ],

            // 🏘️ Mixed-Use Properties
            [
                'name' => 'Developments with Residential & Commercial Use',
                'description' => 'Properties that combine residential and commercial spaces in integrated developments.',
                'category' => 'mixed-use',
                'icon' => 'ni ni-building-alt',
                'color' => '#8BC34A',
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Ideal for Investors, Business Owners & Tenants',
                'description' => 'Mixed-use properties offering diverse investment opportunities and flexible usage.',
                'category' => 'mixed-use',
                'icon' => 'ni ni-investment',
                'color' => '#FFC107',
                'sort_order' => 13,
                'is_active' => true,
            ],

            // 🌿 Land & Development Opportunities
            [
                'name' => 'Residential Land',
                'description' => 'Undeveloped land suitable for residential construction and development.',
                'category' => 'land',
                'icon' => 'ni ni-map',
                'color' => '#4CAF50',
                'sort_order' => 14,
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Land',
                'description' => 'Undeveloped land designated for commercial development and business use.',
                'category' => 'land',
                'icon' => 'ni ni-map-alt',
                'color' => '#2196F3',
                'sort_order' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Agricultural & Farmland',
                'description' => 'Land used for agricultural purposes, farming, and agricultural development.',
                'category' => 'land',
                'icon' => 'ni ni-tree',
                'color' => '#8BC34A',
                'sort_order' => 16,
                'is_active' => true,
            ],
            [
                'name' => 'Leasing Options for Temporary Use or Events',
                'description' => 'Land available for short-term leasing, events, and temporary commercial use.',
                'category' => 'land',
                'icon' => 'ni ni-calendar-alt',
                'color' => '#FF9800',
                'sort_order' => 17,
                'is_active' => true,
            ],
        ];

        foreach ($propertyTypes as $propertyType) {
            PropertyType::create($propertyType);
            $this->command->info("Created property type: {$propertyType['name']}");
        }

        $this->command->info('Comprehensive property types seeded successfully!');
        $this->command->info('Total property types created: ' . count($propertyTypes));
        
        // Display summary by category
        $categories = PropertyType::selectRaw('category, COUNT(*) as count')
            ->groupBy('category')
            ->get();
            
        $this->command->info("\nProperty types by category:");
        foreach ($categories as $category) {
            $this->command->info("- {$category->category}: {$category->count} types");
        }
    }
}
