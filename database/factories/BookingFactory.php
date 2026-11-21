<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Room;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startWib = Carbon::now('Asia/Jakarta')->addDays(2)->setTime(9, 0, 0);
        $endWib = $startWib->copy()->addHours(2);

        return [
            'room_id' => Room::factory(),
            'booker_name' => fake()->name(),
            'booker_contact' => fake()->phoneNumber(),
            'start_time' => $startWib->copy()->utc()->format('Y-m-d H:i:s'),
            'end_time' => $endWib->copy()->utc()->format('Y-m-d H:i:s'),
            'participants' => 4,
            'total_price' => 300000,
            'status' => Booking::STATUS_CONFIRMED,
            'cancelled_at' => null,
            'penalty_amount' => 0,
        ];
    }
}
