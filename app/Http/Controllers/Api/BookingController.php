<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\BookingService;
use App\Support\Clock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * FR-10: List bookings with optional filters (room_id, status, booker_contact, booker_name, date).
     */
    public function index(Request $request): JsonResponse
    {
        $query = Booking::with('room')->orderByDesc('start_time');

        if ($request->filled('room_id')) {
            $query->where('room_id', (int) $request->query('room_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper((string) $request->query('status')));
        }

        if ($request->filled('booker_contact')) {
            $query->where('booker_contact', $request->query('booker_contact'));
        }

        if ($request->filled('booker_name')) {
            $query->where('booker_name', 'like', '%'.$request->query('booker_name').'%');
        }

        if ($request->filled('date')) {
            $dayWib = $this->bookingService->parseWibDate((string) $request->query('date'));
            $startUtc = $dayWib->copy()->startOfDay()->utc();
            $endUtc = $dayWib->copy()->endOfDay()->utc();

            $query->where('start_time', '>=', $startUtc->format('Y-m-d H:i:s'))
                ->where('start_time', '<=', $endUtc->format('Y-m-d H:i:s'));
        }

        $bookings = $query->get()->map(fn (Booking $booking) => $booking->toApiArray());

        return response()->json([
            'data' => $bookings,
        ], 200);
    }

    /**
     * FR-07 & FR-08: Create a new booking.
     */
    public function store(Request $request): JsonResponse
    {
        $nowUtc = Clock::now($request);
        $booking = $this->bookingService->createBooking($request->all(), $nowUtc);

        return response()->json([
            'message' => 'Booking berhasil dibuat.',
            'data' => $booking->toApiArray(),
        ], 201);
    }

    /**
     * FR-10: Get booking details by ID.
     */
    public function show(int $id): JsonResponse
    {
        $booking = Booking::with('room')->find($id);

        if (! $booking) {
            return response()->json([
                'error' => 'BOOKING_NOT_FOUND',
                'message' => 'Booking tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $booking->toApiArray(),
        ], 200);
    }

    /**
     * FR-09 & BR-05: Cancel a booking and calculate penalty.
     */
    public function cancel(Request $request, int $id): JsonResponse
    {
        $nowUtc = Clock::now($request);
        $booking = $this->bookingService->cancelBooking($id, $nowUtc);

        return response()->json([
            'message' => 'Booking berhasil dibatalkan.',
            'data' => $booking->toApiArray(),
        ], 200);
    }
}
