<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * FR-14: Seed example data (at least 3 rooms and several example bookings).
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ruangkita.id'],
            [
                'name' => 'Admin RuangKita',
                'password' => bcrypt('password'),
            ]
        );

        $nusantara = Room::firstOrCreate(
            ['name' => 'Ruang Nusantara'],
            [
                'capacity' => 12,
                'facilities' => ['Smart TV 65"', 'Proyektor HD', 'Whiteboard', 'High-Speed WiFi', 'AC', 'Video Conference'],
                'price_per_hour' => 200000,
                'is_active' => true,
            ]
        );

        $garuda = Room::firstOrCreate(
            ['name' => 'Ruang Garuda'],
            [
                'capacity' => 8,
                'facilities' => ['Smart TV 55"', 'Whiteboard', 'High-Speed WiFi', 'AC'],
                'price_per_hour' => 150000,
                'is_active' => true,
            ]
        );

        $khatulistiwa = Room::firstOrCreate(
            ['name' => 'Ruang Khatulistiwa'],
            [
                'capacity' => 6,
                'facilities' => ['Monitor HDMI', 'Whiteboard', 'High-Speed WiFi', 'AC'],
                'price_per_hour' => 100000,
                'is_active' => true,
            ]
        );

        $merapi = Room::firstOrCreate(
            ['name' => 'Ruang Merapi'],
            [
                'capacity' => 20,
                'facilities' => ['Dual Proyektor', 'Sound System', 'Podium', 'Whiteboard', 'High-Speed WiFi', 'AC'],
                'price_per_hour' => 350000,
                'is_active' => true,
            ]
        );

        if (Booking::count() === 0) {
            $tomorrowWib = Carbon::now('Asia/Jakarta')->addDay()->startOfDay();
            $dayAfterWib = $tomorrowWib->copy()->addDay();

            // Example Booking 1: Tomorrow 09:00 - 11:00 WIB at Ruang Nusantara
            Booking::create([
                'room_id' => $nusantara->id,
                'booker_name' => 'Budi Santoso',
                'booker_contact' => '081234567890',
                'start_time' => $tomorrowWib->copy()->setTime(9, 0)->utc()->format('Y-m-d H:i:s'),
                'end_time' => $tomorrowWib->copy()->setTime(11, 0)->utc()->format('Y-m-d H:i:s'),
                'participants' => 10,
                'total_price' => 400000,
                'status' => Booking::STATUS_CONFIRMED,
                'penalty_amount' => 0,
            ]);

            // Example Booking 2: Tomorrow 13:00 - 15:00 WIB at Ruang Nusantara
            Booking::create([
                'room_id' => $nusantara->id,
                'booker_name' => 'Siti Aminah',
                'booker_contact' => 'siti@startup.id',
                'start_time' => $tomorrowWib->copy()->setTime(13, 0)->utc()->format('Y-m-d H:i:s'),
                'end_time' => $tomorrowWib->copy()->setTime(15, 0)->utc()->format('Y-m-d H:i:s'),
                'participants' => 8,
                'total_price' => 400000,
                'status' => Booking::STATUS_CONFIRMED,
                'penalty_amount' => 0,
            ]);

            // Example Booking 3: Tomorrow 10:00 - 12:00 WIB at Ruang Garuda
            Booking::create([
                'room_id' => $garuda->id,
                'booker_name' => 'Andi Wijaya',
                'booker_contact' => '081987654321',
                'start_time' => $tomorrowWib->copy()->setTime(10, 0)->utc()->format('Y-m-d H:i:s'),
                'end_time' => $tomorrowWib->copy()->setTime(12, 0)->utc()->format('Y-m-d H:i:s'),
                'participants' => 6,
                'total_price' => 300000,
                'status' => Booking::STATUS_CONFIRMED,
                'penalty_amount' => 0,
            ]);

            // Example Booking 4: Day after tomorrow 14:00 - 17:00 WIB at Ruang Merapi
            Booking::create([
                'room_id' => $merapi->id,
                'booker_name' => 'Dewi Lestari',
                'booker_contact' => 'dewi@techcorp.co.id',
                'start_time' => $dayAfterWib->copy()->setTime(14, 0)->utc()->format('Y-m-d H:i:s'),
                'end_time' => $dayAfterWib->copy()->setTime(17, 0)->utc()->format('Y-m-d H:i:s'),
                'participants' => 18,
                'total_price' => 1050000,
                'status' => Booking::STATUS_CONFIRMED,
                'penalty_amount' => 0,
            ]);
        }
    }
}
