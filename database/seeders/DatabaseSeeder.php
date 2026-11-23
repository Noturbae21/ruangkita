<?php

namespace Database\Seeders;

use App\Models\AdditionalFacility;
use App\Models\Booking;
use App\Models\BookingAdditionalFacility;
use App\Models\Room;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * FR-14: Seed example data (at least 3 rooms, additional facilities, and several example bookings).
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

        $nusantara = Room::updateOrCreate(
            ['name' => 'Ruang Nusantara'],
            [
                'capacity' => 12,
                'length_m' => 8.0,
                'width_m' => 5.0,
                'layout_type' => Room::LAYOUT_BOARDROOM,
                'facilities' => ['Smart TV 65"', 'Proyektor HD', 'Whiteboard', 'High-Speed WiFi', 'AC', 'Video Conference'],
                'price_per_hour' => 200000,
                'is_active' => true,
            ]
        );

        $garuda = Room::updateOrCreate(
            ['name' => 'Ruang Garuda'],
            [
                'capacity' => 8,
                'length_m' => 6.0,
                'width_m' => 4.0,
                'layout_type' => Room::LAYOUT_BOARDROOM,
                'facilities' => ['Smart TV 55"', 'Whiteboard', 'High-Speed WiFi', 'AC'],
                'price_per_hour' => 150000,
                'is_active' => true,
            ]
        );

        $khatulistiwa = Room::updateOrCreate(
            ['name' => 'Ruang Khatulistiwa'],
            [
                'capacity' => 6,
                'length_m' => 4.5,
                'width_m' => 3.5,
                'layout_type' => Room::LAYOUT_POD,
                'facilities' => ['Monitor HDMI', 'Whiteboard', 'High-Speed WiFi', 'AC'],
                'price_per_hour' => 100000,
                'is_active' => true,
            ]
        );

        $merapi = Room::updateOrCreate(
            ['name' => 'Ruang Merapi'],
            [
                'capacity' => 20,
                'length_m' => 10.0,
                'width_m' => 6.5,
                'layout_type' => Room::LAYOUT_U_SHAPE,
                'facilities' => ['Dual Proyektor', 'Sound System', 'Podium', 'Whiteboard', 'High-Speed WiFi', 'AC'],
                'price_per_hour' => 350000,
                'is_active' => true,
            ]
        );

        // Seed Master Additional Facilities (Add-ons)
        $coffeeBreak = AdditionalFacility::firstOrCreate(
            ['name' => 'Paket Coffee Break & Snack'],
            [
                'description' => 'Kopi, teh, air mineral, dan 2 macam kudapan per peserta',
                'price' => 35000,
                'pricing_type' => AdditionalFacility::PRICING_PER_PARTICIPANT,
                'is_active' => true,
            ]
        );

        $extraProjector = AdditionalFacility::firstOrCreate(
            ['name' => 'Proyektor & Layar Portable Ekstra'],
            [
                'description' => 'Unit proyektor Full HD tambahan beserta layar tripod',
                'price' => 50000,
                'pricing_type' => AdditionalFacility::PRICING_PER_HOUR,
                'is_active' => true,
            ]
        );

        $webcamConference = AdditionalFacility::firstOrCreate(
            ['name' => 'Kamera Video Conference 360° & Speakerphone'],
            [
                'description' => 'Set kamera omnidirectional + mikrofon speaker untuk hybrid meeting',
                'price' => 100000,
                'pricing_type' => AdditionalFacility::PRICING_PER_BOOKING,
                'is_active' => true,
            ]
        );

        $flipchart = AdditionalFacility::firstOrCreate(
            ['name' => 'Papan Flipchart & Kertas A1 + Spidol'],
            [
                'description' => 'Standing flipchart lengkap dengan 20 lembar kertas A1 dan spidol warna',
                'price' => 45000,
                'pricing_type' => AdditionalFacility::PRICING_PER_BOOKING,
                'is_active' => true,
            ]
        );

        $cateringLunch = AdditionalFacility::firstOrCreate(
            ['name' => 'Paket Makan Siang Bento Eksekutif'],
            [
                'description' => 'Nasi bento hangat lengkap dengan lauk utama, sayur, buah, dan minuman',
                'price' => 55000,
                'pricing_type' => AdditionalFacility::PRICING_PER_PARTICIPANT,
                'is_active' => true,
            ]
        );

        if (Booking::count() === 0) {
            $tomorrowWib = Carbon::now('Asia/Jakarta')->addDay()->startOfDay();
            $dayAfterWib = $tomorrowWib->copy()->addDay();

            // Example Booking 1: Tomorrow 09:00 - 11:00 WIB at Ruang Nusantara + Flipchart (PAID via QRIS)
            $b1Code = 'RK-' . $tomorrowWib->format('Ymd') . '-B101';
            $b1 = Booking::create([
                'booking_code' => $b1Code,
                'room_id' => $nusantara->id,
                'booker_name' => 'Budi Santoso',
                'booker_contact' => '081234567890',
                'start_time' => $tomorrowWib->copy()->setTime(9, 0)->utc()->format('Y-m-d H:i:s'),
                'end_time' => $tomorrowWib->copy()->setTime(11, 0)->utc()->format('Y-m-d H:i:s'),
                'participants' => 10,
                'room_price' => 400000,
                'additional_facilities_price' => 45000,
                'total_price' => 445000,
                'payment_method' => Booking::PAYMENT_METHOD_QRIS,
                'payment_status' => Booking::PAYMENT_STATUS_PAID,
                'payment_reference' => 'QRIS-RK-B101A9F2',
                'qris_payload' => \App\Services\BookingService::buildQrisPayload($b1Code, 445000),
                'paid_at' => Carbon::now('UTC')->format('Y-m-d H:i:s'),
                'status' => Booking::STATUS_CONFIRMED,
                'penalty_amount' => 0,
            ]);

            BookingAdditionalFacility::create([
                'booking_id' => $b1->id,
                'additional_facility_id' => $flipchart->id,
                'facility_name' => $flipchart->name,
                'pricing_type' => $flipchart->pricing_type,
                'unit_price' => $flipchart->price,
                'quantity' => 1,
                'subtotal' => 45000,
            ]);

            // Example Booking 2: Tomorrow 13:00 - 15:00 WIB at Ruang Nusantara (UNPAID QRIS)
            $b2Code = 'RK-' . $tomorrowWib->format('Ymd') . '-S202';
            Booking::create([
                'booking_code' => $b2Code,
                'room_id' => $nusantara->id,
                'booker_name' => 'Siti Aminah',
                'booker_contact' => 'siti@startup.id',
                'start_time' => $tomorrowWib->copy()->setTime(13, 0)->utc()->format('Y-m-d H:i:s'),
                'end_time' => $tomorrowWib->copy()->setTime(15, 0)->utc()->format('Y-m-d H:i:s'),
                'participants' => 8,
                'room_price' => 400000,
                'additional_facilities_price' => 0,
                'total_price' => 400000,
                'payment_method' => Booking::PAYMENT_METHOD_QRIS,
                'payment_status' => Booking::PAYMENT_STATUS_UNPAID,
                'payment_reference' => 'QRIS-RK-S202C4D1',
                'qris_payload' => \App\Services\BookingService::buildQrisPayload($b2Code, 400000),
                'payment_expired_at' => Carbon::now('UTC')->addMinutes(30)->format('Y-m-d H:i:s'),
                'status' => Booking::STATUS_CONFIRMED,
                'penalty_amount' => 0,
            ]);

            // Example Booking 3: Tomorrow 10:00 - 12:00 WIB at Ruang Garuda + Webcam Conference (PAID via QRIS)
            $b3Code = 'RK-' . $tomorrowWib->format('Ymd') . '-A303';
            $b3 = Booking::create([
                'booking_code' => $b3Code,
                'room_id' => $garuda->id,
                'booker_name' => 'Andi Wijaya',
                'booker_contact' => '081987654321',
                'start_time' => $tomorrowWib->copy()->setTime(10, 0)->utc()->format('Y-m-d H:i:s'),
                'end_time' => $tomorrowWib->copy()->setTime(12, 0)->utc()->format('Y-m-d H:i:s'),
                'participants' => 6,
                'room_price' => 300000,
                'additional_facilities_price' => 100000,
                'total_price' => 400000,
                'payment_method' => Booking::PAYMENT_METHOD_QRIS,
                'payment_status' => Booking::PAYMENT_STATUS_PAID,
                'payment_reference' => 'QRIS-RK-A303E7B8',
                'qris_payload' => \App\Services\BookingService::buildQrisPayload($b3Code, 400000),
                'paid_at' => Carbon::now('UTC')->format('Y-m-d H:i:s'),
                'status' => Booking::STATUS_CONFIRMED,
                'penalty_amount' => 0,
            ]);

            BookingAdditionalFacility::create([
                'booking_id' => $b3->id,
                'additional_facility_id' => $webcamConference->id,
                'facility_name' => $webcamConference->name,
                'pricing_type' => $webcamConference->pricing_type,
                'unit_price' => $webcamConference->price,
                'quantity' => 1,
                'subtotal' => 100000,
            ]);

            // Example Booking 4: Day after tomorrow 14:00 - 17:00 WIB at Ruang Merapi + Coffee Break (PAID via QRIS)
            $b4Code = 'RK-' . $dayAfterWib->format('Ymd') . '-D404';
            $b4 = Booking::create([
                'booking_code' => $b4Code,
                'room_id' => $merapi->id,
                'booker_name' => 'Dewi Lestari',
                'booker_contact' => 'dewi@techcorp.co.id',
                'start_time' => $dayAfterWib->copy()->setTime(14, 0)->utc()->format('Y-m-d H:i:s'),
                'end_time' => $dayAfterWib->copy()->setTime(17, 0)->utc()->format('Y-m-d H:i:s'),
                'participants' => 18,
                'room_price' => 1050000,
                'additional_facilities_price' => 630000,
                'total_price' => 1680000,
                'payment_method' => Booking::PAYMENT_METHOD_QRIS,
                'payment_status' => Booking::PAYMENT_STATUS_PAID,
                'payment_reference' => 'QRIS-RK-D404F1C9',
                'qris_payload' => \App\Services\BookingService::buildQrisPayload($b4Code, 1680000),
                'paid_at' => Carbon::now('UTC')->format('Y-m-d H:i:s'),
                'status' => Booking::STATUS_CONFIRMED,
                'penalty_amount' => 0,
            ]);

            BookingAdditionalFacility::create([
                'booking_id' => $b4->id,
                'additional_facility_id' => $coffeeBreak->id,
                'facility_name' => $coffeeBreak->name,
                'pricing_type' => $coffeeBreak->pricing_type,
                'unit_price' => $coffeeBreak->price,
                'quantity' => 1,
                'subtotal' => 630000,
            ]);
        }
    }
}
