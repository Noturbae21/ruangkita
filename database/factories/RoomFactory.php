<?php

namespace Database\Factories;

use App\Models\Room;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Room>
 */
class RoomFactory extends Factory
{
    protected $model = Room::class;

    public function definition(): array
    {
        return [
            'name' => 'Ruang '.fake()->unique()->city(),
            'capacity' => fake()->randomElement([6, 8, 10, 12, 20]),
            'facilities' => ['WiFi', 'Proyektor', 'Whiteboard', 'AC'],
            'price_per_hour' => fake()->randomElement([100000, 150000, 200000, 250000]),
            'is_active' => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }
}
