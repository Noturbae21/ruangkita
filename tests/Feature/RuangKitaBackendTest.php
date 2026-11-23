<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Room;
use App\Support\Clock;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuangKitaBackendTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Set deterministic clock: 2026-11-25 07:00:00 WIB (00:00:00 UTC)
        Clock::setTestNow('2026-11-25T07:00:00+07:00');
    }

    protected function tearDown(): void
    {
        Clock::reset();
        parent::tearDown();
    }

    /**
     * T-01: /health returns HTTP 200 and database connection status.
     */
    public function test_t01_health_endpoint_returns_200_and_database_status(): void
    {
        $response = $this->getJson('/health');

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'ok',
                'database' => 'connected',
            ]);
    }

    /**
     * T-02: Create room, list rooms, update room, and deactivate vs delete room (FR-01 to FR-04).
     */
    public function test_t02_crud_rooms_and_deactivate_when_has_upcoming_booking(): void
    {
        $createResponse = $this->postJson('/api/rooms', [
            'name' => 'Ruang Nusantara',
            'capacity' => 10,
            'facilities' => ['WiFi', 'Proyektor', 'Whiteboard'],
            'price_per_hour' => 150000,
            'is_active' => true,
        ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('data.name', 'Ruang Nusantara')
            ->assertJsonPath('data.capacity', 10)
            ->assertJsonPath('data.price_per_hour', 150000);

        $roomId = $createResponse->json('data.id');

        // List rooms
        $this->getJson('/api/rooms')
            ->assertStatus(200)
            ->assertJsonCount(1, 'data');

        // Update room
        $this->putJson("/api/rooms/{$roomId}", [
            'price_per_hour' => 200000,
        ])->assertStatus(200)
            ->assertJsonPath('data.price_per_hour', 200000);

        // Create an upcoming booking on this room
        $this->postJson('/api/bookings', [
            'room_id' => $roomId,
            'booker_name' => 'Budi',
            'booker_contact' => '08123456789',
            'date' => '2026-11-26',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 5,
        ])->assertStatus(201);

        // FR-04: Deleting a room with upcoming bookings must only deactivate it
        $this->deleteJson("/api/rooms/{$roomId}")
            ->assertStatus(200)
            ->assertJsonPath('action', 'deactivated')
            ->assertJsonPath('data.is_active', false);

        // BR-07: Inactive room cannot be booked
        $this->postJson('/api/bookings', [
            'room_id' => $roomId,
            'booker_name' => 'Siti',
            'booker_contact' => '0819999999',
            'date' => '2026-11-27',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 5,
        ])->assertStatus(422)
            ->assertJsonPath('error', 'BR_07_ROOM_INACTIVE');
    }

    /**
     * T-03: Valid booking succeeds with price calculated on server (FR-07, FR-08).
     */
    public function test_t03_valid_booking_calculates_price_on_server(): void
    {
        $room = Room::factory()->create([
            'capacity' => 12,
            'price_per_hour' => 200000,
        ]);

        // 2.5 hours booking (09:00 - 11:30 WIB) -> 2.5 * 200,000 = 500,000
        $response = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Andi Wijaya',
            'booker_contact' => 'andi@example.com',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '11:30',
            'participants' => 8,
            'total_price' => 1000, // Client-sent price must be ignored by server
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.total_price', 500000)
            ->assertJsonPath('data.status', Booking::STATUS_CONFIRMED)
            ->assertJsonPath('data.penalty_amount', 0);
    }

    /**
     * T-04: Partially overlapping booking on the same room is rejected with 409 (BR-01).
     */
    public function test_t04_partial_overlapping_booking_is_rejected_with_409(): void
    {
        $room = Room::factory()->create(['capacity' => 10]);

        // Existing booking: 09:00 - 11:00 WIB
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan 1',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 5,
        ])->assertStatus(201);

        // Overlapping booking: 10:00 - 12:00 WIB
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan 2',
            'booker_contact' => '0822222222',
            'date' => '2026-11-25',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'participants' => 5,
        ])->assertStatus(409)
            ->assertJsonPath('error', 'BR_01_BOOKING_CONFLICT');
    }

    /**
     * T-05: Back-to-back (adjacent) bookings on the same room are accepted (BR-01).
     */
    public function test_t05_adjacent_booking_is_accepted(): void
    {
        $room = Room::factory()->create(['capacity' => 10]);

        // First booking: 08:00 - 10:00 WIB
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan 1',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '08:00',
            'end_time' => '10:00',
            'participants' => 4,
        ])->assertStatus(201);

        // Second booking immediately starting at 10:00 - 12:00 WIB
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan 2',
            'booker_contact' => '0822222222',
            'date' => '2026-11-25',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'participants' => 4,
        ])->assertStatus(201);
    }

    /**
     * T-06: Bookings at the same time in different rooms are accepted (BR-01).
     */
    public function test_t06_same_time_booking_in_different_room_is_accepted(): void
    {
        $roomA = Room::factory()->create(['name' => 'Ruang A']);
        $roomB = Room::factory()->create(['name' => 'Ruang B']);

        $this->postJson('/api/bookings', [
            'room_id' => $roomA->id,
            'booker_name' => 'Pemesan A',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 4,
        ])->assertStatus(201);

        $this->postJson('/api/bookings', [
            'room_id' => $roomB->id,
            'booker_name' => 'Pemesan B',
            'booker_contact' => '0822222222',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 4,
        ])->assertStatus(201);
    }

    /**
     * T-07: Participants exceeding capacity or <= 0 rejected (422); exact capacity accepted (BR-02).
     */
    public function test_t07_capacity_validation(): void
    {
        $room = Room::factory()->create(['capacity' => 8]);

        // 0 participants -> rejected (422)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'participants' => 0,
        ])->assertStatus(422)
            ->assertJsonPath('error', 'BR_02_CAPACITY_EXCEEDED');

        // 9 participants (> 8) -> rejected (422)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'participants' => 9,
        ])->assertStatus(422)
            ->assertJsonPath('error', 'BR_02_CAPACITY_EXCEEDED');

        // Exact capacity (8) -> accepted (201)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '10:00',
            'participants' => 8,
        ])->assertStatus(201);
    }

    /**
     * T-08: Duration 59 minutes and 8h 30m rejected (422); 1 hour and 8 hours accepted (BR-03).
     */
    public function test_t08_duration_validation_boundaries(): void
    {
        $room = Room::factory()->create(['capacity' => 10]);

        // 59 minutes -> rejected (422)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '09:59',
            'participants' => 4,
        ])->assertStatus(422)
            ->assertJsonPath('error', 'BR_03_INVALID_DURATION');

        // 8 hours 30 minutes (08:00 - 16:30) -> rejected (422)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '08:00',
            'end_time' => '16:30',
            'participants' => 4,
        ])->assertStatus(422)
            ->assertJsonPath('error', 'BR_03_INVALID_DURATION');

        // Exactly 1 hour (08:00 - 09:00) -> accepted (201)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '08:00',
            'end_time' => '09:00',
            'participants' => 4,
        ])->assertStatus(201);

        // Exactly 8 hours (09:00 - 17:00) -> accepted (201)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '17:00',
            'participants' => 4,
        ])->assertStatus(201);
    }

    /**
     * T-09: Outside operational hours (08:00 - 21:00 WIB) rejected (BR-04).
     */
    public function test_t09_operational_hours_validation(): void
    {
        $room = Room::factory()->create(['capacity' => 10]);

        // 07:30 - 09:00 -> rejected (422)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '07:30',
            'end_time' => '09:00',
            'participants' => 4,
        ])->assertStatus(422)
            ->assertJsonPath('error', 'BR_04_OPERATIONAL_HOURS');

        // 20:00 - 21:30 -> rejected (422)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '20:00',
            'end_time' => '21:30',
            'participants' => 4,
        ])->assertStatus(422)
            ->assertJsonPath('error', 'BR_04_OPERATIONAL_HOURS');

        // 20:00 - 21:00 -> accepted (201)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '20:00',
            'end_time' => '21:00',
            'participants' => 4,
        ])->assertStatus(201);
    }

    /**
     * T-10: Cancellation less than 2 hours before start_time incurs 50% penalty (BR-05).
     */
    public function test_t10_cancellation_less_than_2_hours_before_start_incurs_50_percent_penalty(): void
    {
        $room = Room::factory()->create([
            'capacity' => 10,
            'price_per_hour' => 200000,
        ]);

        // Booking at 10:00 - 12:00 WIB (total_price = 400,000)
        $bookingResp = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Budi',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'participants' => 5,
        ])->assertStatus(201);

        $bookingId = $bookingResp->json('data.id');

        // Advance clock to 08:30 WIB (1 hour 30 minutes before 10:00 WIB)
        Clock::setTestNow('2026-11-25T08:30:00+07:00');

        $this->postJson("/api/bookings/{$bookingId}/cancel")
            ->assertStatus(200)
            ->assertJsonPath('data.status', Booking::STATUS_CANCELLED)
            ->assertJsonPath('data.penalty_amount', 200000);
    }

    /**
     * T-11: Cancellation 2 hours or more (including exactly 2 hours) before start_time has 0 penalty (BR-05).
     */
    public function test_t11_cancellation_2_hours_or_more_before_start_has_zero_penalty(): void
    {
        $room = Room::factory()->create([
            'capacity' => 10,
            'price_per_hour' => 200000,
        ]);

        // Booking 1: 12:00 - 14:00 WIB
        $b1 = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Budi 1',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '12:00',
            'end_time' => '14:00',
            'participants' => 5,
        ])->json('data.id');

        // Booking 2: 15:00 - 17:00 WIB
        $b2 = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Budi 2',
            'booker_contact' => '0822222222',
            'date' => '2026-11-25',
            'start_time' => '15:00',
            'end_time' => '17:00',
            'participants' => 5,
        ])->json('data.id');

        // Cancel Booking 1 at EXACTLY 2 hours before start (10:00:00 WIB for 12:00:00 WIB start) -> penalty 0
        Clock::setTestNow('2026-11-25T10:00:00+07:00');
        $this->postJson("/api/bookings/{$b1}/cancel")
            ->assertStatus(200)
            ->assertJsonPath('data.status', Booking::STATUS_CANCELLED)
            ->assertJsonPath('data.penalty_amount', 0);

        // Cancel Booking 2 at 5 hours before start (10:00:00 WIB for 15:00:00 WIB start) -> penalty 0
        $this->postJson("/api/bookings/{$b2}/cancel")
            ->assertStatus(200)
            ->assertJsonPath('data.status', Booking::STATUS_CANCELLED)
            ->assertJsonPath('data.penalty_amount', 0);
    }

    /**
     * T-12: A cancelled slot becomes available to be booked again (BR-01).
     */
    public function test_t12_cancelled_slot_can_be_rebooked(): void
    {
        $room = Room::factory()->create(['capacity' => 10]);

        $bookingId = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan Lama',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 5,
        ])->assertStatus(201)->json('data.id');

        // Cancel the booking
        $this->postJson("/api/bookings/{$bookingId}/cancel")->assertStatus(200);

        // Rebook the exact same slot
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan Baru',
            'booker_contact' => '0822222222',
            'date' => '2026-11-25',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 5,
        ])->assertStatus(201);
    }

    /**
     * T-13: Two booking requests for the exact same slot result in only one succeeding and the second getting 409.
     */
    public function test_t13_duplicate_booking_on_same_slot_only_one_succeeds(): void
    {
        $room = Room::factory()->create(['capacity' => 10]);

        $payload = [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '13:00',
            'end_time' => '15:00',
            'participants' => 6,
        ];

        $first = $this->postJson('/api/bookings', $payload);
        $second = $this->postJson('/api/bookings', $payload);

        $first->assertStatus(201);
        $second->assertStatus(409);

        $this->assertEquals(1, Booking::where('room_id', $room->id)->where('status', Booking::STATUS_CONFIRMED)->count());
    }

    /**
     * T-14: Booking in the past and cancelling an already-started or already-cancelled booking are rejected (BR-06).
     */
    public function test_t14_past_booking_and_cancelling_started_or_cancelled_booking_are_rejected(): void
    {
        $room = Room::factory()->create(['capacity' => 10]);

        // Set clock to 2026-11-25 10:00 WIB
        Clock::setTestNow('2026-11-25T10:00:00+07:00');

        // Booking in the past (08:00 - 09:00 WIB on 2026-11-25) -> rejected (422)
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '08:00',
            'end_time' => '09:00',
            'participants' => 4,
        ])->assertStatus(422)
            ->assertJsonPath('error', 'BR_06_PAST_BOOKING');

        // Create valid future booking (13:00 - 15:00 WIB)
        $bookingId = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Pemesan',
            'booker_contact' => '0811111111',
            'date' => '2026-11-25',
            'start_time' => '13:00',
            'end_time' => '15:00',
            'participants' => 4,
        ])->assertStatus(201)->json('data.id');

        // Move clock to 13:00 WIB (booking has already started) -> cannot cancel (422)
        Clock::setTestNow('2026-11-25T13:00:00+07:00');
        $this->postJson("/api/bookings/{$bookingId}/cancel")
            ->assertStatus(422)
            ->assertJsonPath('error', 'BR_06_ALREADY_STARTED');

        // Move clock back to 11:00 WIB, cancel once (200), then try to cancel again -> rejected (422)
        Clock::setTestNow('2026-11-25T11:00:00+07:00');
        $this->postJson("/api/bookings/{$bookingId}/cancel")->assertStatus(200);
        $this->postJson("/api/bookings/{$bookingId}/cancel")
            ->assertStatus(422)
            ->assertJsonPath('error', 'BR_06_ALREADY_CANCELLED');
    }

    /**
     * FR-05 & FR-11: Check room availability per date and calendar endpoint.
     */
    public function test_availability_and_calendar_endpoints(): void
    {
        $room = Room::factory()->create([
            'name' => 'Ruang Garuda',
            'capacity' => 8,
            'price_per_hour' => 150000,
        ]);

        // Create booking 10:00 - 12:00 WIB on 2026-11-25
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Andi',
            'booker_contact' => '08123456',
            'date' => '2026-11-25',
            'start_time' => '10:00',
            'end_time' => '12:00',
            'participants' => 6,
        ])->assertStatus(201);

        // Check availability on 2026-11-25: booked 10:00-12:00, free 08:00-10:00 and 12:00-21:00
        $availResp = $this->getJson("/api/rooms/{$room->id}/availability?date=2026-11-25");
        $availResp->assertStatus(200)
            ->assertJsonCount(1, 'data.booked_slots')
            ->assertJsonCount(2, 'data.available_slots')
            ->assertJsonPath('data.available_slots.0.start_time', '08:00')
            ->assertJsonPath('data.available_slots.0.end_time', '10:00')
            ->assertJsonPath('data.available_slots.1.start_time', '12:00')
            ->assertJsonPath('data.available_slots.1.end_time', '21:00');

        // Check daily and weekly calendar
        $this->getJson("/api/calendar?date=2026-11-25&room_id={$room->id}&view=day")
            ->assertStatus(200)
            ->assertJsonCount(1, 'data.bookings')
            ->assertJsonCount(1, 'data.days');

        $this->getJson('/api/calendar?date=2026-11-25&view=week')
            ->assertStatus(200)
            ->assertJsonCount(7, 'data.days');
    }

    /**
     * Separate User (Booker) and Admin Portal authentication flow.
     */
    public function test_admin_portal_requires_login_and_allows_authenticated_admin(): void
    {
        // Guest visiting /admin is redirected to /admin/login
        $this->get('/admin')->assertRedirect('/admin/login');

        // Login with valid admin credentials
        $this->post('/admin/login', [
            'email' => 'admin@ruangkita.id',
            'password' => 'password',
        ])->assertRedirect('/admin');

        // Authenticated admin can access /admin dashboard
        $this->get('/admin')->assertStatus(200);

        // Logout redirects back to /admin/login
        $this->post('/admin/logout')->assertRedirect('/admin/login');
        $this->get('/admin')->assertRedirect('/admin/login');
    }

    /**
     * T-15: CRUD Additional Facilities & Booking with flexible pricing (PER_BOOKING, PER_HOUR, PER_PARTICIPANT).
     */
    public function test_t15_additional_facilities_crud_and_booking_price_calculation(): void
    {
        $room = Room::factory()->create([
            'name' => 'Ruang Nusantara',
            'capacity' => 10,
            'price_per_hour' => 200000,
        ]);

        // 1. Create 3 additional facilities with different pricing schemes
        $flatResp = $this->postJson('/api/additional-facilities', [
            'name' => 'Kamera 360 Conference',
            'description' => 'Kamera hybrid meeting',
            'price' => 100000,
            'pricing_type' => 'PER_BOOKING',
            'is_active' => true,
        ])->assertStatus(201);
        $flatId = $flatResp->json('data.id');

        $hourlyResp = $this->postJson('/api/additional-facilities', [
            'name' => 'Proyektor Ekstra',
            'price' => 50000,
            'pricing_type' => 'PER_HOUR',
            'is_active' => true,
        ])->assertStatus(201);
        $hourlyId = $hourlyResp->json('data.id');

        $perPersonResp = $this->postJson('/api/additional-facilities', [
            'name' => 'Paket Coffee Break',
            'price' => 30000,
            'pricing_type' => 'PER_PARTICIPANT',
            'is_active' => true,
        ])->assertStatus(201);
        $perPersonId = $perPersonResp->json('data.id');

        // 2. Book room for 2 hours (09:00 - 11:00) with 6 participants and all 3 add-ons
        // Room price = 2 * 200,000 = 400,000
        // Flat add-on (qty 1) = 100,000
        // Hourly add-on (qty 2, 2 hours) = 50,000 * 2 * 2 = 200,000
        // Per participant add-on (qty 1, 6 participants) = 30,000 * 6 * 1 = 180,000
        // Add-ons total = 480,000 -> Grand Total = 880,000
        $bookingResp = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Rina Permata',
            'booker_contact' => '08123456789',
            'date' => '2026-11-26',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 6,
            'additional_facilities' => [
                ['id' => $flatId, 'quantity' => 1],
                ['id' => $hourlyId, 'quantity' => 2],
                ['id' => $perPersonId, 'quantity' => 1],
            ],
        ]);

        $bookingResp->assertStatus(201)
            ->assertJsonPath('data.room_price', 400000)
            ->assertJsonPath('data.additional_facilities_price', 480000)
            ->assertJsonPath('data.total_price', 880000)
            ->assertJsonCount(3, 'data.additional_facilities');

        // 3. Attempting to delete an additional facility used in an upcoming booking deactivates it instead
        $this->deleteJson("/api/additional-facilities/{$flatId}")
            ->assertStatus(200)
            ->assertJsonPath('action', 'deactivated')
            ->assertJsonPath('data.is_active', false);

        // 4. Inactive additional facility cannot be selected for new booking
        $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Doni',
            'booker_contact' => '08198765432',
            'date' => '2026-11-27',
            'start_time' => '09:00',
            'end_time' => '11:00',
            'participants' => 4,
            'additional_facilities' => [
                ['id' => $flatId, 'quantity' => 1],
            ],
        ])->assertStatus(422)
            ->assertJsonPath('error', 'ADDITIONAL_FACILITY_INACTIVE');
    }

    /**
     * T-16: QRIS-Only Payment Gateway generation, payload inspection, and settlement verification.
     */
    public function test_t16_qris_payment_gateway_generation_and_settlement(): void
    {
        $room = Room::factory()->create([
            'name' => 'Ruang Garuda',
            'capacity' => 8,
            'price_per_hour' => 150000,
        ]);

        // 1. Create booking -> automatically generates QRIS payload & UNPAID payment status
        $createResp = $this->postJson('/api/bookings', [
            'room_id' => $room->id,
            'booker_name' => 'Farhan Akbar',
            'booker_contact' => '08129876543',
            'date' => '2026-11-26',
            'start_time' => '13:00',
            'end_time' => '15:00',
            'participants' => 6,
        ])->assertStatus(201)
            ->assertJsonPath('data.payment_method', 'QRIS')
            ->assertJsonPath('data.payment_status', 'UNPAID')
            ->assertJsonPath('data.total_price', 300000);

        $bookingId = $createResp->json('data.id');
        $bookingCode = $createResp->json('data.booking_code');
        $this->assertNotEmpty($bookingCode);
        $this->assertStringStartsWith('RK-20261126-', $bookingCode);

        // 2. Inspect QRIS Payment Gateway endpoint
        $qrisResp = $this->getJson("/api/bookings/{$bookingId}/qris")
            ->assertStatus(200)
            ->assertJsonPath('data.booking_id', $bookingId)
            ->assertJsonPath('data.booking_code', $bookingCode)
            ->assertJsonPath('data.payment_method', 'QRIS')
            ->assertJsonPath('data.payment_status', 'UNPAID')
            ->assertJsonPath('data.total_price', 300000);

        $this->assertStringContainsString('ID.CO.QRIS.WWW', $qrisResp->json('data.qris_payload'));
        $this->assertStringContainsString('RUANGKITA', $qrisResp->json('data.qris_payload'));

        // 3. Settle/pay via QRIS endpoint
        $this->postJson("/api/bookings/{$bookingId}/pay")
            ->assertStatus(200)
            ->assertJsonPath('data.payment_status', 'PAID');

        // 4. Paying an already PAID booking is rejected with 422
        $this->postJson("/api/bookings/{$bookingId}/pay")
            ->assertStatus(422)
            ->assertJsonPath('error', 'PAYMENT_ALREADY_PAID');
    }

    /**
     * T-17: Room dimensions (length_m, width_m, area_sqm) and layout_type are returned and configurable.
     */
    public function test_t17_room_dimensions_and_layout_blueprint_attributes(): void
    {
        $resp = $this->postJson('/api/rooms', [
            'name' => 'Ruang Krakatau',
            'capacity' => 16,
            'length_m' => 9.5,
            'width_m' => 6.0,
            'layout_type' => 'U_SHAPE',
            'facilities' => ['Dual TV', 'WiFi', 'AC'],
            'price_per_hour' => 300000,
            'is_active' => true,
        ])->assertStatus(201)
            ->assertJsonPath('data.length_m', 9.5)
            ->assertJsonPath('data.width_m', 6)
            ->assertJsonPath('data.area_sqm', 57)
            ->assertJsonPath('data.dimensions_label', '9.5m × 6m')
            ->assertJsonPath('data.layout_type', 'U_SHAPE');

        $roomId = $resp->json('data.id');

        $this->putJson("/api/rooms/{$roomId}", [
            'length_m' => 10.0,
            'width_m' => 7.0,
            'layout_type' => 'CLASSROOM',
        ])->assertStatus(200)
            ->assertJsonPath('data.area_sqm', 70)
            ->assertJsonPath('data.dimensions_label', '10m × 7m')
            ->assertJsonPath('data.layout_type', 'CLASSROOM');
    }
}
