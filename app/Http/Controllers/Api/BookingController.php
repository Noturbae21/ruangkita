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
        $query = Booking::with(['room', 'additionalFacilities'])->orderByDesc('start_time');

        if ($request->filled('room_id')) {
            $query->where('room_id', (int) $request->query('room_id'));
        }

        if ($request->filled('user_id')) {
            $userId = (int) $request->query('user_id');
            $customerContact = $request->query('customer_contact');
            $hasUserIdCol = \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'user_id');
            $query->where(function ($q) use ($userId, $customerContact, $hasUserIdCol) {
                if ($hasUserIdCol) {
                    $q->where('user_id', $userId);
                    if ($customerContact) {
                        $q->orWhere('booker_contact', $customerContact);
                    }
                } elseif ($customerContact) {
                    $q->where('booker_contact', $customerContact);
                }
            });
        }

        if ($request->filled('status')) {
            $query->where('status', strtoupper((string) $request->query('status')));
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', strtoupper((string) $request->query('payment_status')));
        }

        if ($request->filled('booking_code')) {
            $query->where('booking_code', 'like', '%'.trim((string) $request->query('booking_code')).'%');
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
        $payload = $request->all();
        if (empty($payload['user_id']) && $request->hasSession() && $request->session()->has('customer_id')) {
            $payload['user_id'] = (int) $request->session()->get('customer_id');
        }
        $booking = $this->bookingService->createBooking($payload, $nowUtc);

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
        $booking = Booking::with(['room', 'additionalFacilities'])->find($id);

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
     * Get QRIS payment details & payload for a booking.
     */
    public function qris(int $id): JsonResponse
    {
        $booking = Booking::with(['room', 'additionalFacilities'])->find($id);

        if (! $booking) {
            return response()->json([
                'error' => 'BOOKING_NOT_FOUND',
                'message' => 'Booking tidak ditemukan.',
            ], 404);
        }

        $apiData = $booking->toApiArray();
        if (empty($apiData['qris_payload'])) {
            $apiData['qris_payload'] = BookingService::buildQrisPayload($apiData['booking_code'], (int) $booking->total_price);
        }

        return response()->json([
            'data' => [
                'booking_id' => $booking->id,
                'booking_code' => $apiData['booking_code'],
                'merchant_name' => 'DAVIS, SOFTWARE',
                'nmid' => 'ID1026496073473',
                'payment_method' => Booking::PAYMENT_METHOD_QRIS,
                'payment_status' => $apiData['payment_status'],
                'payment_reference' => $apiData['payment_reference'] ?: ('QRIS-RK-' . $booking->id),
                'total_price' => $booking->total_price,
                'room_price' => $apiData['room_price'],
                'additional_facilities_price' => $apiData['additional_facilities_price'],
                'qris_payload' => $apiData['qris_payload'],
                'payment_expired_at' => $apiData['payment_expired_at'],
                'paid_at' => $apiData['paid_at'],
                'booking' => $apiData,
            ],
        ], 200);
    }

    /**
     * Confirm/simulate QRIS payment settlement for a booking.
     */
    public function pay(Request $request, int $id): JsonResponse
    {
        $nowUtc = Clock::now($request);
        $booking = $this->bookingService->payBookingWithQris($id, $nowUtc);

        return response()->json([
            'message' => 'Pembayaran QRIS berhasil diverifikasi dan lunas.',
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
