<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\DomainException;
use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use App\Services\BookingService;
use App\Support\Clock;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoomController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * FR-02 & FR-06: List all rooms, with optional filters for active status,
     * minimum capacity, and availability within a specific time range.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Room::query()->orderBy('id');

        if ($request->has('is_active')) {
            $isActive = filter_var($request->query('is_active'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            if ($isActive !== null) {
                $query->where('is_active', $isActive);
            }
        }

        $minCapacity = $request->query('min_capacity') ?? $request->query('capacity');
        if ($minCapacity !== null && $minCapacity !== '') {
            if (! is_numeric($minCapacity) || (int) $minCapacity < 1) {
                return response()->json([
                    'error' => 'INVALID_INPUT',
                    'message' => 'Parameter min_capacity harus berupa angka positif.',
                ], 400);
            }
            $query->where('capacity', '>=', (int) $minCapacity);
        }

        // FR-06: Search available rooms in a given time range
        if ($request->filled('start_time') && $request->filled('end_time')) {
            [$startUtc, $endUtc] = $this->bookingService->parseBookingTimes([
                'date' => $request->query('date'),
                'start_time' => $request->query('start_time'),
                'end_time' => $request->query('end_time'),
            ]);

            $query->where('is_active', true)
                ->whereDoesntHave('bookings', function ($bookingQuery) use ($startUtc, $endUtc) {
                    $bookingQuery->where('status', Booking::STATUS_CONFIRMED)
                        ->where('start_time', '<', $endUtc->format('Y-m-d H:i:s'))
                        ->where('end_time', '>', $startUtc->format('Y-m-d H:i:s'));
                });
        }

        $rooms = $query->get();

        return response()->json([
            'data' => $rooms,
        ], 200);
    }

    /**
     * FR-01: Create a new room (admin).
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'capacity' => ['required', 'integer', 'min:1'],
            'facilities' => ['nullable'],
            'price_per_hour' => ['required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'INVALID_INPUT',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 400);
        }

        $room = Room::create([
            'name' => trim((string) $request->input('name')),
            'capacity' => (int) $request->input('capacity'),
            'facilities' => $request->input('facilities', []),
            'price_per_hour' => (int) round((float) $request->input('price_per_hour')),
            'is_active' => $request->has('is_active') ? (bool) $request->boolean('is_active') : true,
        ]);

        return response()->json([
            'message' => 'Ruang berhasil dibuat.',
            'data' => $room,
        ], 201);
    }

    /**
     * FR-02: Get room details by ID.
     */
    public function show(int $id): JsonResponse
    {
        $room = Room::find($id);

        if (! $room) {
            return response()->json([
                'error' => 'ROOM_NOT_FOUND',
                'message' => 'Ruang tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'data' => $room,
        ], 200);
    }

    /**
     * FR-03: Update a room (admin).
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $room = Room::find($id);

        if (! $room) {
            return response()->json([
                'error' => 'ROOM_NOT_FOUND',
                'message' => 'Ruang tidak ditemukan.',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'capacity' => ['sometimes', 'required', 'integer', 'min:1'],
            'facilities' => ['nullable'],
            'price_per_hour' => ['sometimes', 'required', 'numeric', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error' => 'INVALID_INPUT',
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 400);
        }

        if ($request->has('name')) {
            $room->name = trim((string) $request->input('name'));
        }
        if ($request->has('capacity')) {
            $room->capacity = (int) $request->input('capacity');
        }
        if ($request->has('facilities')) {
            $room->facilities = $request->input('facilities', []);
        }
        if ($request->has('price_per_hour')) {
            $room->price_per_hour = (int) round((float) $request->input('price_per_hour'));
        }
        if ($request->has('is_active')) {
            $room->is_active = (bool) $request->boolean('is_active');
        }

        $room->save();

        return response()->json([
            'message' => 'Ruang berhasil diperbarui.',
            'data' => $room,
        ], 200);
    }

    /**
     * FR-04: Delete or deactivate a room.
     * A room with upcoming CONFIRMED bookings cannot be permanently deleted (only deactivated).
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $room = Room::find($id);

        if (! $room) {
            return response()->json([
                'error' => 'ROOM_NOT_FOUND',
                'message' => 'Ruang tidak ditemukan.',
            ], 404);
        }

        $nowUtc = Clock::now($request);

        $hasUpcomingBookings = Booking::where('room_id', $room->id)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('end_time', '>', $nowUtc->format('Y-m-d H:i:s'))
            ->exists();

        if ($hasUpcomingBookings) {
            $room->update(['is_active' => false]);

            return response()->json([
                'message' => 'Ruang masih memiliki booking mendatang sehingga hanya dinonaktifkan.',
                'action' => 'deactivated',
                'data' => $room->fresh(),
            ], 200);
        }

        $room->delete();

        return response()->json([
            'message' => 'Ruang berhasil dihapus.',
            'action' => 'deleted',
        ], 200);
    }

    /**
     * FR-05: Check room availability on a specific date.
     * GET /api/rooms/:id/availability?date=YYYY-MM-DD
     */
    public function availability(Request $request, int $id): JsonResponse
    {
        $room = Room::find($id);

        if (! $room) {
            return response()->json([
                'error' => 'ROOM_NOT_FOUND',
                'message' => 'Ruang tidak ditemukan.',
            ], 404);
        }

        $date = $request->query('date');
        if (! is_string($date) || trim($date) === '') {
            throw new DomainException("Query parameter 'date' (YYYY-MM-DD) wajib diisi.", 400, 'INVALID_INPUT');
        }

        $availability = $this->bookingService->getRoomAvailability($room, $date);

        return response()->json([
            'data' => $availability,
        ], 200);
    }
}
