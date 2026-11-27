<?php

namespace Database\Seeders;

use App\Models\AdditionalFacility;
use App\Models\Room;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's master data (Admin account, Meeting Rooms, and Additional Facilities).
     */
    public function run(): void
    {
        // 1. Akun Default Admin
        User::updateOrCreate(
            ['email' => 'admin@ruangkita.id'],
            [
                'name' => 'Admin RuangKita',
                'phone' => '081100001111',
                'role' => 'admin',
                'password' => bcrypt('password'),
            ]
        );

        // 2. Data Master Ruangan Meeting
        Room::updateOrCreate(
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

        Room::updateOrCreate(
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

        Room::updateOrCreate(
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

        Room::updateOrCreate(
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

        // 3. Data Master Fasilitas Tambahan (Add-ons)
        AdditionalFacility::updateOrCreate(
            ['name' => 'Paket Coffee Break & Snack'],
            [
                'description' => 'Kopi, teh, air mineral, dan 2 macam kudapan per peserta',
                'price' => 35000,
                'pricing_type' => AdditionalFacility::PRICING_PER_PARTICIPANT,
                'is_active' => true,
            ]
        );

        AdditionalFacility::updateOrCreate(
            ['name' => 'Proyektor & Layar Portable Ekstra'],
            [
                'description' => 'Unit proyektor Full HD tambahan beserta layar tripod',
                'price' => 50000,
                'pricing_type' => AdditionalFacility::PRICING_PER_HOUR,
                'is_active' => true,
            ]
        );

        AdditionalFacility::updateOrCreate(
            ['name' => 'Kamera Video Conference 360° & Speakerphone'],
            [
                'description' => 'Set kamera omnidirectional + mikrofon speaker untuk hybrid meeting',
                'price' => 100000,
                'pricing_type' => AdditionalFacility::PRICING_PER_BOOKING,
                'is_active' => true,
            ]
        );

        AdditionalFacility::updateOrCreate(
            ['name' => 'Papan Flipchart & Kertas A1 + Spidol'],
            [
                'description' => 'Standing flipchart lengkap dengan 20 lembar kertas A1 dan spidol warna',
                'price' => 45000,
                'pricing_type' => AdditionalFacility::PRICING_PER_BOOKING,
                'is_active' => true,
            ]
        );

        AdditionalFacility::updateOrCreate(
            ['name' => 'Paket Makan Siang Bento Eksekutif'],
            [
                'description' => 'Nasi bento hangat lengkap dengan lauk utama, sayur, buah, dan minuman',
                'price' => 55000,
                'pricing_type' => AdditionalFacility::PRICING_PER_PARTICIPANT,
                'is_active' => true,
            ]
        );
    }
}

