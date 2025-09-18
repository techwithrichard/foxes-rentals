<?php

namespace Database\Factories;

use App\Models\PropertyConsolidated;
use App\Models\PropertyType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PropertyConsolidated>
 */
class PropertyConsolidatedFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = PropertyConsolidated::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtype = $this->faker->randomElement(['rental', 'sale', 'lease']);
        
        return [
            'name' => $this->faker->words(3, true) . ' Property',
            'description' => $this->faker->paragraph(3),
            'property_type_id' => PropertyType::factory(),
            'landlord_id' => User::factory(),
            'property_subtype' => $subtype,
            'base_amount' => $this->getBaseAmountForSubtype($subtype),
            'deposit_amount' => $this->faker->randomFloat(2, 5000, 50000),
            'commission_rate' => $this->faker->randomFloat(2, 1, 10),
            'status' => $this->faker->randomElement(['active', 'inactive', 'maintenance']),
            'is_available' => $this->faker->boolean(80),
            'is_vacant' => $this->faker->boolean(70),
            'is_multi_unit' => $this->faker->boolean(20),
            'total_units' => $this->faker->numberBetween(1, 10),
            'available_units' => function (array $attributes) {
                return $this->faker->numberBetween(0, $attributes['total_units']);
            },
            'electricity_id' => $this->faker->optional()->numerify('ELEC####'),
            'water_id' => $this->faker->optional()->numerify('WATER####'),
            'furnished' => $this->faker->boolean(30),
            'pet_friendly' => $this->faker->boolean(40),
            'smoking_allowed' => $this->faker->boolean(20),
            'parking_spaces' => $this->faker->numberBetween(0, 5),
            'balcony' => $this->faker->boolean(50),
            'garden' => $this->faker->boolean(40),
            'swimming_pool' => $this->faker->boolean(10),
            'gym' => $this->faker->boolean(15),
            'security' => $this->faker->boolean(60),
            'elevator' => $this->faker->boolean(25),
            'air_conditioning' => $this->faker->boolean(35),
            'heating' => $this->faker->boolean(20),
            'internet' => $this->faker->boolean(70),
            'cable_tv' => $this->faker->boolean(50),
            'laundry' => $this->faker->boolean(40),
            'dishwasher' => $this->faker->boolean(30),
            'microwave' => $this->faker->boolean(60),
            'refrigerator' => $this->faker->boolean(80),
            'stove' => $this->faker->boolean(70),
            'oven' => $this->faker->boolean(50),
            'features' => $this->faker->randomElements([
                'parking', 'garden', 'security', 'elevator', 'balcony',
                'swimming_pool', 'gym', 'air_conditioning', 'heating',
                'internet', 'cable_tv', 'laundry', 'dishwasher'
            ], $this->faker->numberBetween(0, 5)),
            'images' => $this->faker->randomElements([
                'property1.jpg', 'property2.jpg', 'property3.jpg',
                'property4.jpg', 'property5.jpg'
            ], $this->faker->numberBetween(1, 3)),
            'floor_plan' => $this->faker->optional()->imageUrl(),
            'virtual_tour' => $this->faker->optional()->url(),
            'latitude' => $this->faker->latitude(-1.5, -1.0), // Kenya coordinates
            'longitude' => $this->faker->longitude(36.0, 37.0),
            'year_built' => $this->faker->numberBetween(1950, 2024),
            'last_renovated' => $this->faker->optional()->numberBetween(2000, 2024),
            'property_size' => $this->faker->randomFloat(2, 50, 500),
            'lot_size' => $this->faker->randomFloat(2, 100, 1000),
            'bedrooms' => $this->faker->numberBetween(1, 6),
            'bathrooms' => $this->faker->numberBetween(1, 4),
            'living_rooms' => $this->faker->numberBetween(1, 3),
            'kitchens' => $this->faker->numberBetween(1, 2),
            'dining_rooms' => $this->faker->numberBetween(0, 2),
            'storage_rooms' => $this->faker->numberBetween(0, 3),
            'garage_spaces' => $this->faker->numberBetween(0, 3),
            'outdoor_spaces' => $this->faker->numberBetween(0, 2),
            'utilities_included' => $this->faker->randomElements([
                'water', 'electricity', 'internet', 'cable_tv', 'garbage'
            ], $this->faker->numberBetween(0, 3)),
            'maintenance_responsibility' => $this->faker->randomElement([
                'tenant', 'landlord', 'shared', 'property_management'
            ]),
            'lease_terms' => [
                'minimum_lease_period' => $this->faker->numberBetween(6, 24),
                'maximum_lease_period' => $this->faker->numberBetween(24, 60),
                'renewal_terms' => $this->faker->sentence(),
            ],
            'minimum_lease_period' => $this->faker->numberBetween(6, 24),
            'maximum_lease_period' => $this->faker->numberBetween(24, 60),
            'notice_period' => $this->faker->numberBetween(30, 90),
            'late_fee_percentage' => $this->faker->randomFloat(2, 0, 10),
            'late_fee_fixed' => $this->faker->randomFloat(2, 0, 5000),
            'returned_check_fee' => $this->faker->randomFloat(2, 0, 2000),
            'early_termination_fee' => $this->faker->randomFloat(2, 0, 10000),
            'renewal_terms' => [
                'automatic_renewal' => $this->faker->boolean(),
                'renewal_notice_period' => $this->faker->numberBetween(30, 90),
            ],
            'special_conditions' => [
                'no_pets' => $this->faker->boolean(),
                'no_smoking' => $this->faker->boolean(),
                'background_check_required' => $this->faker->boolean(),
            ],
            'marketing_description' => $this->faker->paragraph(5),
            'keywords' => $this->faker->words(5, true),
            'seo_title' => $this->faker->sentence(6),
            'seo_description' => $this->faker->paragraph(2),
            'is_featured' => $this->faker->boolean(20),
            'is_published' => $this->faker->boolean(80),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
            'views_count' => $this->faker->numberBetween(0, 1000),
            'inquiries_count' => $this->faker->numberBetween(0, 50),
            'applications_count' => $this->faker->numberBetween(0, 20),
        ];
    }

    /**
     * Get base amount based on property subtype
     */
    private function getBaseAmountForSubtype(string $subtype): float
    {
        return match ($subtype) {
            'rental' => $this->faker->randomFloat(2, 15000, 150000), // Monthly rent
            'sale' => $this->faker->randomFloat(2, 2000000, 50000000), // Sale price
            'lease' => $this->faker->randomFloat(2, 20000, 200000), // Monthly lease
            default => $this->faker->randomFloat(2, 15000, 150000),
        };
    }

    /**
     * Indicate that the property is a rental.
     */
    public function rental(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_subtype' => 'rental',
            'base_amount' => $this->faker->randomFloat(2, 15000, 150000),
            'is_vacant' => true,
        ]);
    }

    /**
     * Indicate that the property is for sale.
     */
    public function sale(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_subtype' => 'sale',
            'base_amount' => $this->faker->randomFloat(2, 2000000, 50000000),
            'is_available' => true,
        ]);
    }

    /**
     * Indicate that the property is for lease.
     */
    public function lease(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_subtype' => 'lease',
            'base_amount' => $this->faker->randomFloat(2, 20000, 200000),
            'is_available' => true,
        ]);
    }

    /**
     * Indicate that the property is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
            'is_available' => true,
        ]);
    }

    /**
     * Indicate that the property is vacant.
     */
    public function vacant(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_vacant' => true,
            'is_available' => true,
        ]);
    }

    /**
     * Indicate that the property is featured.
     */
    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'is_published' => true,
        ]);
    }
}
