<?php

namespace Database\Factories;

use App\Models\AdditionalFacility;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AdditionalFacility>
 */
class AdditionalFacilityFactory extends Factory
{
    protected $model = AdditionalFacility::class;

    public function definition(): array
    {
        return [
            'name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'price' => fake()->randomElement([25000, 35000, 50000, 75000, 100000]),
            'pricing_type' => AdditionalFacility::PRICING_PER_BOOKING,
            'is_active' => true,
        ];
    }
}
