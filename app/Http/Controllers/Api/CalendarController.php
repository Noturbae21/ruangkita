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

class CalendarController extends Controller
{
    public function __construct(
        protected BookingService $bookingService
    ) {}

    /**
     * FR-11: Usage calendar endpoint per day or per week (all rooms or single room).
     * GET /api/calendar?date=YYYY-MM-DD&room_id=...&view=day|week
     */
    public function index(Request $request): JsonResponse
    {
        $dateParam = $request->query('date') ?? Clock::nowWib($request)->format('Y-m-d');
        $startDayWib = $this->bookingService->parseWibDate((string) $dateParam);

        $view = strtolower((string) ($request->query('view') ?? $request->query('period') ?? 'day'));
        if (! in_array($view, ['day', 'week'], true)) {
            throw new DomainException("Parameter 'view' harus bernilai 'day' atau 'week'.", 400, 'INVALID_INPUT');
        }

        $daysCount = $view === 'week' ? 7 : 1;
        $endDayWib = $startDayWib->copy()->addDays($daysCount - 1)->endOfDay();

        $roomsQuery = Room::query()->orderBy('id');
        if ($request->filled('room_id')) {
            $roomId = (int) $request->query('room_id');
            $room = Room::find($roomId);
            if (! $room) {
                return response()->json([
                    'error' => 'ROOM_NOT_FOUND',
                    'message' => 'Ruang tidak ditemukan.',
                ], 404);
            }
            $roomsQuery->where('id', $roomId);
        }

        $rooms = $roomsQuery->get();

        $bookingsQuery = Booking::with('room')
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('start_time', '<', $endDayWib->copy()->utc()->format('Y-m-d H:i:s'))
            ->where('end_time', '>', $startDayWib->copy()->utc()->format('Y-m-d H:i:s'))
            ->orderBy('start_time');

        if ($request->filled('room_id')) {
            $bookingsQuery->where('room_id', (int) $request->query('room_id'));
        }

        $bookings = $bookingsQuery->get();

        $days = [];
        for ($i = 0; $i < $daysCount; $i++) {
            $currentDate = $startDayWib->copy()->addDays($i)->format('Y-m-d');
            $dayBookings = $bookings
                ->filter(fn (Booking $b) => $b->toApiArray()['date'] === $currentDate)
                ->map(fn (Booking $b) => $b->toApiArray())
                ->values();

            $days[] = [
                'date' => $currentDate,
                'rooms' => $rooms->map(function (Room $room) use ($dayBookings) {
                    return [
                        'room_id' => $room->id,
                        'room_name' => $room->name,
                        'capacity' => $room->capacity,
                        'is_active' => $room->is_active,
                        'bookings' => $dayBookings->where('room_id', $room->id)->values(),
                    ];
                })->values(),
            ];
        }

        return response()->json([
            'data' => [
                'view' => $view,
                'timezone' => Booking::TIMEZONE_WIB,
                'start_date' => $startDayWib->format('Y-m-d'),
                'end_date' => $endDayWib->format('Y-m-d'),
                'operational_hours' => [
                    'start' => '08:00',
                    'end' => '21:00',
                ],
                'bookings' => $bookings->map(fn (Booking $b) => $b->toApiArray())->values(),
                'days' => $days,
            ],
        ], 200);
    }
}
