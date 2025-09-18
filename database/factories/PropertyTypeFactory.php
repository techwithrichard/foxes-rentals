<?php

namespace Database\Factories;

use App\Models\PropertyType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class PropertyTypeFactory extends Factory
{
    protected $model = PropertyType::class;

    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'name' => $this->faker->randomElement(['Apartment', 'House', 'Commercial', 'Land', 'Office']),
            'description' => $this->faker->sentence,
            'is_active' => true,
        ];
    }
}
