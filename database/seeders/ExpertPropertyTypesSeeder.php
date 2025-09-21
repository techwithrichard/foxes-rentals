<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PropertyType;
use Illuminate\Support\Facades\DB;

class ExpertPropertyTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Expert-designed property categorization system based on industry standards
     */
    public function run(): void
    {
        $this->command->info('Seeding expert-designed property types...');

        // Clear existing property types
        DB::table('property_types')->delete();

        $propertyTypes = [
            // 🏠 RESIDENTIAL PROPERTIES
            [
                'name' => 'Single-Family Detached Homes',
                'description' => 'Standalone residential properties on individual lots, offering maximum privacy and space.',
                'category' => 'residential',
                'icon' => 'ni ni-home',
                'color' => '#4CAF50',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Single-Family Attached Homes',
                'description' => 'Residential properties sharing one or more walls with adjacent units (townhouses, row houses).',
                'category' => 'residential',
                'icon' => 'ni ni-building',
                'color' => '#2196F3',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Condominiums',
                'description' => 'Individually owned units in multi-unit buildings with shared common areas and amenities.',
                'category' => 'residential',
                'icon' => 'ni ni-apartment',
                'color' => '#FF9800',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Multi-Family Apartments',
                'description' => 'Rental apartment buildings with multiple units under single ownership.',
                'category' => 'residential',
                'icon' => 'ni ni-house',
                'color' => '#9C27B0',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Duplexes & Triplexes',
                'description' => 'Residential buildings with 2-3 separate living units, ideal for small-scale investors.',
                'category' => 'residential',
                'icon' => 'ni ni-building-alt',
                'color' => '#E91E63',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Student Housing',
                'description' => 'Specialized residential properties designed for student accommodation near educational institutions.',
                'category' => 'residential',
                'icon' => 'ni ni-graduation',
                'color' => '#607D8B',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Senior Living Communities',
                'description' => 'Age-restricted residential properties designed for active adults and seniors.',
                'category' => 'residential',
                'icon' => 'ni ni-users',
                'color' => '#795548',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Vacation & Short-Term Rentals',
                'description' => 'Residential properties optimized for short-term stays and vacation rentals.',
                'category' => 'residential',
                'icon' => 'ni ni-calendar',
                'color' => '#00BCD4',
                'sort_order' => 8,
                'is_active' => true,
            ],

            // 🏢 OFFICE & PROFESSIONAL SPACES
            [
                'name' => 'Class A Office Buildings',
                'description' => 'Premium office buildings with modern amenities, prime locations, and high-quality finishes.',
                'category' => 'office',
                'icon' => 'ni ni-office',
                'color' => '#3F51B5',
                'sort_order' => 9,
                'is_active' => true,
            ],
            [
                'name' => 'Class B Office Buildings',
                'description' => 'Mid-tier office buildings with good amenities and competitive rental rates.',
                'category' => 'office',
                'icon' => 'ni ni-building',
                'color' => '#2196F3',
                'sort_order' => 10,
                'is_active' => true,
            ],
            [
                'name' => 'Class C Office Buildings',
                'description' => 'Older office buildings with basic amenities, often requiring renovation.',
                'category' => 'office',
                'icon' => 'ni ni-building-alt',
                'color' => '#607D8B',
                'sort_order' => 11,
                'is_active' => true,
            ],
            [
                'name' => 'Co-working Spaces',
                'description' => 'Flexible office spaces designed for freelancers, startups, and remote workers.',
                'category' => 'office',
                'icon' => 'ni ni-users',
                'color' => '#FF5722',
                'sort_order' => 12,
                'is_active' => true,
            ],
            [
                'name' => 'Medical Office Buildings',
                'description' => 'Specialized office buildings designed for healthcare providers and medical practices.',
                'category' => 'office',
                'icon' => 'ni ni-medical',
                'color' => '#4CAF50',
                'sort_order' => 13,
                'is_active' => true,
            ],
            [
                'name' => 'Flex Office/Industrial',
                'description' => 'Versatile spaces that can accommodate both office and light industrial uses.',
                'category' => 'office',
                'icon' => 'ni ni-building-alt',
                'color' => '#9C27B0',
                'sort_order' => 14,
                'is_active' => true,
            ],

            // 🛍️ RETAIL & CONSUMER SPACES
            [
                'name' => 'Shopping Centers & Malls',
                'description' => 'Large retail complexes with multiple tenants and anchor stores.',
                'category' => 'retail',
                'icon' => 'ni ni-shop',
                'color' => '#FF9800',
                'sort_order' => 15,
                'is_active' => true,
            ],
            [
                'name' => 'Strip Centers & Power Centers',
                'description' => 'Open-air retail centers with multiple stores and convenient parking.',
                'category' => 'retail',
                'icon' => 'ni ni-shop-alt',
                'color' => '#E91E63',
                'sort_order' => 16,
                'is_active' => true,
            ],
            [
                'name' => 'Standalone Retail Buildings',
                'description' => 'Individual retail buildings for single tenants or small retail operations.',
                'category' => 'retail',
                'icon' => 'ni ni-store',
                'color' => '#795548',
                'sort_order' => 17,
                'is_active' => true,
            ],
            [
                'name' => 'Restaurant & Food Service',
                'description' => 'Specialized spaces designed for restaurants, cafes, and food service businesses.',
                'category' => 'retail',
                'icon' => 'ni ni-restaurant',
                'color' => '#CDDC39',
                'sort_order' => 18,
                'is_active' => true,
            ],
            [
                'name' => 'Automotive & Service Centers',
                'description' => 'Properties designed for automotive sales, service, and related businesses.',
                'category' => 'retail',
                'icon' => 'ni ni-car',
                'color' => '#607D8B',
                'sort_order' => 19,
                'is_active' => true,
            ],

            // 🏭 INDUSTRIAL & MANUFACTURING
            [
                'name' => 'Heavy Manufacturing Facilities',
                'description' => 'Large industrial facilities for heavy manufacturing and production operations.',
                'category' => 'industrial',
                'icon' => 'ni ni-factory',
                'color' => '#795548',
                'sort_order' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'Light Manufacturing & Assembly',
                'description' => 'Industrial spaces for light manufacturing, assembly, and production activities.',
                'category' => 'industrial',
                'icon' => 'ni ni-tools',
                'color' => '#607D8B',
                'sort_order' => 21,
                'is_active' => true,
            ],
            [
                'name' => 'Distribution & Logistics Centers',
                'description' => 'Large warehouses and distribution facilities for logistics and supply chain operations.',
                'category' => 'industrial',
                'icon' => 'ni ni-warehouse',
                'color' => '#FF5722',
                'sort_order' => 22,
                'is_active' => true,
            ],
            [
                'name' => 'Cold Storage Facilities',
                'description' => 'Specialized industrial properties with temperature-controlled storage capabilities.',
                'category' => 'industrial',
                'icon' => 'ni ni-snowflake',
                'color' => '#00BCD4',
                'sort_order' => 23,
                'is_active' => true,
            ],
            [
                'name' => 'Research & Development Facilities',
                'description' => 'Industrial properties designed for R&D, testing, and innovation activities.',
                'category' => 'industrial',
                'icon' => 'ni ni-lab',
                'color' => '#4CAF50',
                'sort_order' => 24,
                'is_active' => true,
            ],

            // 🏨 HOSPITALITY & LEISURE
            [
                'name' => 'Hotels & Motels',
                'description' => 'Accommodation properties for short-term stays and business travelers.',
                'category' => 'hospitality',
                'icon' => 'ni ni-hotel',
                'color' => '#E91E63',
                'sort_order' => 25,
                'is_active' => true,
            ],
            [
                'name' => 'Resorts & Vacation Properties',
                'description' => 'Leisure-focused properties with extensive amenities and recreational facilities.',
                'category' => 'hospitality',
                'icon' => 'ni ni-palm-tree',
                'color' => '#4CAF50',
                'sort_order' => 26,
                'is_active' => true,
            ],
            [
                'name' => 'Extended Stay Hotels',
                'description' => 'Hotels designed for longer stays with residential-style amenities.',
                'category' => 'hospitality',
                'icon' => 'ni ni-home-alt',
                'color' => '#FF9800',
                'sort_order' => 27,
                'is_active' => true,
            ],
            [
                'name' => 'Conference Centers & Event Venues',
                'description' => 'Specialized properties for meetings, conferences, and special events.',
                'category' => 'hospitality',
                'icon' => 'ni ni-calendar-alt',
                'color' => '#9C27B0',
                'sort_order' => 28,
                'is_active' => true,
            ],

            // 🏥 HEALTHCARE & SPECIALIZED
            [
                'name' => 'Hospitals & Medical Centers',
                'description' => 'Large healthcare facilities providing comprehensive medical services.',
                'category' => 'healthcare',
                'icon' => 'ni ni-hospital',
                'color' => '#F44336',
                'sort_order' => 29,
                'is_active' => true,
            ],
            [
                'name' => 'Clinics & Outpatient Centers',
                'description' => 'Smaller healthcare facilities for outpatient services and specialized care.',
                'category' => 'healthcare',
                'icon' => 'ni ni-medical',
                'color' => '#4CAF50',
                'sort_order' => 30,
                'is_active' => true,
            ],
            [
                'name' => 'Senior Care & Assisted Living',
                'description' => 'Specialized healthcare properties for senior care and assisted living services.',
                'category' => 'healthcare',
                'icon' => 'ni ni-users',
                'color' => '#795548',
                'sort_order' => 31,
                'is_active' => true,
            ],
            [
                'name' => 'Laboratories & Research Facilities',
                'description' => 'Specialized properties for medical research, testing, and laboratory operations.',
                'category' => 'healthcare',
                'icon' => 'ni ni-lab',
                'color' => '#2196F3',
                'sort_order' => 32,
                'is_active' => true,
            ],

            // 🏘️ MIXED-USE & DEVELOPMENT
            [
                'name' => 'Mixed-Use Developments',
                'description' => 'Integrated developments combining residential, commercial, and retail uses.',
                'category' => 'mixed-use',
                'icon' => 'ni ni-building-alt',
                'color' => '#8BC34A',
                'sort_order' => 33,
                'is_active' => true,
            ],
            [
                'name' => 'Transit-Oriented Developments',
                'description' => 'Mixed-use developments designed around public transportation hubs.',
                'category' => 'mixed-use',
                'icon' => 'ni ni-bus',
                'color' => '#FFC107',
                'sort_order' => 34,
                'is_active' => true,
            ],
            [
                'name' => 'Urban Infill Projects',
                'description' => 'Development projects on vacant or underutilized land in urban areas.',
                'category' => 'mixed-use',
                'icon' => 'ni ni-city',
                'color' => '#607D8B',
                'sort_order' => 35,
                'is_active' => true,
            ],

            // 🌿 LAND & DEVELOPMENT OPPORTUNITIES
            [
                'name' => 'Residential Development Land',
                'description' => 'Raw land suitable for residential development and subdivision.',
                'category' => 'land',
                'icon' => 'ni ni-map',
                'color' => '#4CAF50',
                'sort_order' => 36,
                'is_active' => true,
            ],
            [
                'name' => 'Commercial Development Land',
                'description' => 'Land designated and zoned for commercial development projects.',
                'category' => 'land',
                'icon' => 'ni ni-map-alt',
                'color' => '#2196F3',
                'sort_order' => 37,
                'is_active' => true,
            ],
            [
                'name' => 'Industrial Development Land',
                'description' => 'Land suitable for industrial development and manufacturing facilities.',
                'category' => 'land',
                'icon' => 'ni ni-factory',
                'color' => '#795548',
                'sort_order' => 38,
                'is_active' => true,
            ],
            [
                'name' => 'Agricultural & Farmland',
                'description' => 'Land used for agricultural purposes, farming, and agricultural development.',
                'category' => 'land',
                'icon' => 'ni ni-tree',
                'color' => '#8BC34A',
                'sort_order' => 39,
                'is_active' => true,
            ],
            [
                'name' => 'Recreational & Conservation Land',
                'description' => 'Land designated for recreational use, conservation, or environmental protection.',
                'category' => 'land',
                'icon' => 'ni ni-park',
                'color' => '#4CAF50',
                'sort_order' => 40,
                'is_active' => true,
            ],
            [
                'name' => 'Event & Temporary Use Land',
                'description' => 'Land available for short-term leasing, events, and temporary commercial use.',
                'category' => 'land',
                'icon' => 'ni ni-calendar-alt',
                'color' => '#FF9800',
                'sort_order' => 41,
                'is_active' => true,
            ],
        ];

        foreach ($propertyTypes as $propertyType) {
            PropertyType::create($propertyType);
            $this->command->info("Created property type: {$propertyType['name']}");
        }

        $this->command->info('Expert-designed property types seeded successfully!');
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
