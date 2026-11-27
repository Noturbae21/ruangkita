<?php

namespace App\Services;

use App\Exceptions\DomainException;
use App\Models\AdditionalFacility;
use App\Models\Booking;
use App\Models\BookingAdditionalFacility;
use App\Models\Room;
use App\Support\Clock;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Throwable;

class BookingService
{
    public const OPERATIONAL_START_HOUR = 8;
    public const OPERATIONAL_END_HOUR = 21;
    public const MIN_DURATION_MINUTES = 60;
    public const MAX_DURATION_MINUTES = 480;
    public const FREE_CANCEL_SECONDS = 7200; // 2 hours
    public const PENALTY_RATE = 0.5;

    /**
     * Create a new booking enforcing FR-07, FR-08, and BR-01 through BR-07,
     * including optional paid additional facilities (add-ons).
     */
    public function createBooking(array $payload, ?Carbon $nowUtc = null): Booking
    {
        $nowUtc = ($nowUtc ?? Clock::now())->copy()->utc();

        $this->validateRequiredFields($payload);

        $roomId = (int) $payload['room_id'];
        $participants = filter_var($payload['participants'], FILTER_VALIDATE_INT);
        if ($participants === false) {
            throw new DomainException('Jumlah peserta harus berupa bilangan bulat.', 400, 'INVALID_INPUT');
        }

        $requestedAddons = $this->normalizeRequestedAdditionalFacilities($payload['additional_facilities'] ?? []);

        [$startUtc, $endUtc, $startWib, $endWib] = $this->parseBookingTimes($payload);

        $room = Room::find($roomId);
        if (! $room) {
            throw new DomainException('Ruang tidak ditemukan.', 404, 'ROOM_NOT_FOUND');
        }

        $this->validateBusinessRules($room, $startUtc, $endUtc, $startWib, $endWib, $participants, $nowUtc);

        return DB::transaction(function () use ($roomId, $payload, $startUtc, $endUtc, $startWib, $endWib, $participants, $nowUtc, $requestedAddons) {
            $lockedRoom = Room::where('id', $roomId)->lockForUpdate()->first();
            if (! $lockedRoom) {
                throw new DomainException('Ruang tidak ditemukan.', 404, 'ROOM_NOT_FOUND');
            }

            $this->validateBusinessRules($lockedRoom, $startUtc, $endUtc, $startWib, $endWib, $participants, $nowUtc);

            // BR-01: Two CONFIRMED bookings in the same room conflict if new_start < old_end AND new_end > old_start
            $hasConflict = Booking::where('room_id', $lockedRoom->id)
                ->where('status', Booking::STATUS_CONFIRMED)
                ->where('start_time', '<', $endUtc->format('Y-m-d H:i:s'))
                ->where('end_time', '>', $startUtc->format('Y-m-d H:i:s'))
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw new DomainException(
                    'Jadwal booking bentrok dengan booking lain pada ruang yang sama.',
                    409,
                    'BR_01_BOOKING_CONFLICT'
                );
            }

            // FR-08: Server calculates room price + additional facilities price
            $durationMinutes = $startUtc->diffInMinutes($endUtc);
            $roomPrice = (int) round(($durationMinutes / 60) * $lockedRoom->price_per_hour);

            $resolvedAddonItems = $this->resolveAdditionalFacilities($requestedAddons, $durationMinutes, $participants);
            $addonsPrice = array_sum(array_column($resolvedAddonItems, 'subtotal'));
            $totalPrice = $roomPrice + $addonsPrice;

            $bookingCode = $this->generateUniqueBookingCode($startWib);
            $paymentReference = 'QRIS-RK-' . strtoupper(substr(md5($bookingCode . microtime(true)), 0, 10));
            $qrisPayload = self::buildQrisPayload($bookingCode, $totalPrice);
            $paymentExpiredUtc = $nowUtc->copy()->addMinutes(15);

            $bookingData = [
                'booking_code' => $bookingCode,
                'room_id' => $lockedRoom->id,
                'booker_name' => trim((string) $payload['booker_name']),
                'booker_contact' => trim((string) $payload['booker_contact']),
                'start_time' => $startUtc->format('Y-m-d H:i:s'),
                'end_time' => $endUtc->format('Y-m-d H:i:s'),
                'participants' => $participants,
                'room_price' => $roomPrice,
                'additional_facilities_price' => $addonsPrice,
                'total_price' => $totalPrice,
                'payment_method' => Booking::PAYMENT_METHOD_QRIS,
                'payment_status' => Booking::PAYMENT_STATUS_UNPAID,
                'payment_reference' => $paymentReference,
                'qris_payload' => $qrisPayload,
                'payment_expired_at' => $paymentExpiredUtc->format('Y-m-d H:i:s'),
                'paid_at' => null,
                'status' => Booking::STATUS_CONFIRMED,
                'cancelled_at' => null,
                'penalty_amount' => 0,
            ];

            if (! empty($payload['user_id']) && \Illuminate\Support\Facades\Schema::hasColumn('bookings', 'user_id')) {
                $bookingData['user_id'] = (int) $payload['user_id'];
            }

            $booking = Booking::create($bookingData);

            $createdItems = collect();
            foreach ($resolvedAddonItems as $itemData) {
                $createdItems->push(BookingAdditionalFacility::create(array_merge(
                    ['booking_id' => $booking->id],
                    $itemData
                )));
            }

            $booking->setRelation('room', $lockedRoom);
            $booking->setRelation('additionalFacilities', $createdItems);

            return $booking;
        });
    }

    /**
     * Pay a booking via QRIS Payment Gateway.
     */
    public function payBookingWithQris(int $bookingId, ?Carbon $nowUtc = null): Booking
    {
        $nowUtc = ($nowUtc ?? Clock::now())->copy()->utc();

        return DB::transaction(function () use ($bookingId, $nowUtc) {
            $booking = Booking::with(['room', 'additionalFacilities'])->where('id', $bookingId)->lockForUpdate()->first();

            if (! $booking) {
                throw new DomainException('Booking tidak ditemukan.', 404, 'BOOKING_NOT_FOUND');
            }

            if ($booking->status === Booking::STATUS_CANCELLED) {
                throw new DomainException(
                    'Booking yang sudah dibatalkan tidak dapat dilakukan pembayaran QRIS.',
                    422,
                    'PAYMENT_BOOKING_CANCELLED'
                );
            }

            if ($booking->payment_status === Booking::PAYMENT_STATUS_PAID) {
                throw new DomainException(
                    'Booking ini sudah lunas dibayar melalui QRIS.',
                    422,
                    'PAYMENT_ALREADY_PAID'
                );
            }

            $bookingCode = $booking->booking_code ?: sprintf('RK-%s-%04d', $nowUtc->format('Ymd'), $booking->id);
            $paymentReference = $booking->payment_reference ?: ('QRIS-RK-' . strtoupper(substr(md5($bookingCode . $booking->id), 0, 10)));
            $qrisPayload = $booking->qris_payload ?: self::buildQrisPayload($bookingCode, (int) $booking->total_price);

            $booking->update([
                'booking_code' => $bookingCode,
                'payment_method' => Booking::PAYMENT_METHOD_QRIS,
                'payment_status' => Booking::PAYMENT_STATUS_PAID,
                'payment_reference' => $paymentReference,
                'qris_payload' => $qrisPayload,
                'paid_at' => $nowUtc->format('Y-m-d H:i:s'),
            ]);

            return $booking->fresh(['room', 'additionalFacilities']);
        });
    }

    /**
     * Generate a unique booking reference code (e.g. RK-20261125-A8F2).
     */
    protected function generateUniqueBookingCode(Carbon $startWib): string
    {
        $datePart = $startWib->format('Ymd');
        do {
            $randomPart = strtoupper(substr(bin2hex(random_bytes(3)), 0, 4));
            $code = "RK-{$datePart}-{$randomPart}";
        } while (Booking::where('booking_code', $code)->exists());

        return $code;
    }

    /**
     * Build a standard EMVCo / QRIS-formatted payload string with CRC16 checksum.
     */
    public static function buildQrisPayload(string $bookingCode, int $amount): string
    {
        $amountStr = (string) max(0, $amount);
        $amountTag = '54' . str_pad((string) strlen($amountStr), 2, '0', STR_PAD_LEFT) . $amountStr;
        $refTag = '05' . str_pad((string) strlen($bookingCode), 2, '0', STR_PAD_LEFT) . $bookingCode;
        $additionalData = '62' . str_pad((string) strlen($refTag), 2, '0', STR_PAD_LEFT) . $refTag;

        $basePayload = '00020101021226580014ID.CO.QRIS.WWW01189360091430000123450209RUANGKITA0303UMI520473995303360'
            . $amountTag
            . '5802ID5919RUANGKITA COWORKING6013JAKARTA PUSAT'
            . $additionalData
            . '6304';

        $crc = strtoupper(str_pad(dechex(self::crc16Ccitt($basePayload)), 4, '0', STR_PAD_LEFT));

        return $basePayload . $crc;
    }

    protected static function crc16Ccitt(string $data): int
    {
        $crc = 0xFFFF;
        $len = strlen($data);
        for ($i = 0; $i < $len; $i++) {
            $crc ^= ord($data[$i]) << 8;
            for ($j = 0; $j < 8; $j++) {
                if (($crc & 0x8000) !== 0) {
                    $crc = (($crc << 1) ^ 0x1021) & 0xFFFF;
                } else {
                    $crc = ($crc << 1) & 0xFFFF;
                }
            }
        }

        return $crc & 0xFFFF;
    }

    /**
     * Normalize requested additional facilities payload into [facility_id => quantity].
     *
     * @return array<int, int>
     */
    protected function normalizeRequestedAdditionalFacilities(mixed $rawAddons): array
    {
        if ($rawAddons === null || $rawAddons === '' || $rawAddons === []) {
            return [];
        }

        if (! is_array($rawAddons)) {
            throw new DomainException("Field 'additional_facilities' harus berupa array.", 400, 'INVALID_INPUT');
        }

        $normalized = [];

        foreach ($rawAddons as $entry) {
            if (is_numeric($entry)) {
                $facilityId = (int) $entry;
                $quantity = 1;
            } elseif (is_array($entry)) {
                $rawId = $entry['id'] ?? $entry['additional_facility_id'] ?? null;
                if ($rawId === null || ! is_numeric($rawId)) {
                    throw new DomainException('ID fasilitas tambahan tidak valid.', 400, 'INVALID_INPUT');
                }
                $facilityId = (int) $rawId;
                $rawQty = $entry['quantity'] ?? 1;
                $quantity = filter_var($rawQty, FILTER_VALIDATE_INT);
                if ($quantity === false || $quantity < 1) {
                    throw new DomainException('Jumlah (quantity) fasilitas tambahan minimal 1.', 400, 'INVALID_INPUT');
                }
            } else {
                throw new DomainException('Format item fasilitas tambahan tidak valid.', 400, 'INVALID_INPUT');
            }

            if ($facilityId <= 0) {
                throw new DomainException('ID fasilitas tambahan tidak valid.', 400, 'INVALID_INPUT');
            }

            $normalized[$facilityId] = ($normalized[$facilityId] ?? 0) + $quantity;
        }

        return $normalized;
    }

    /**
     * Validate and compute subtotals for requested additional facilities.
     *
     * @param  array<int, int>  $requestedAddons
     * @return array<int, array<string, mixed>>
     */
    protected function resolveAdditionalFacilities(array $requestedAddons, int $durationMinutes, int $participants): array
    {
        if (empty($requestedAddons)) {
            return [];
        }

        $facilityIds = array_keys($requestedAddons);
        $facilities = AdditionalFacility::whereIn('id', $facilityIds)->get()->keyBy('id');

        $resolved = [];
        foreach ($requestedAddons as $facilityId => $quantity) {
            /** @var AdditionalFacility|null $facility */
            $facility = $facilities->get($facilityId);

            if (! $facility) {
                throw new DomainException("Fasilitas tambahan #{$facilityId} tidak ditemukan.", 404, 'ADDITIONAL_FACILITY_NOT_FOUND');
            }

            if (! $facility->is_active) {
                throw new DomainException(
                    "Fasilitas tambahan '{$facility->name}' sedang tidak aktif dan tidak dapat dipilih.",
                    422,
                    'ADDITIONAL_FACILITY_INACTIVE'
                );
            }

            $subtotal = $facility->calculateSubtotal($quantity, $durationMinutes, $participants);

            $resolved[] = [
                'additional_facility_id' => $facility->id,
                'facility_name' => $facility->name,
                'pricing_type' => $facility->pricing_type,
                'unit_price' => $facility->price,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
            ];
        }

        return $resolved;
    }

    /**
     * Reschedule an existing CONFIRMED booking to a new date/time with the same duration.
     */
    public function rescheduleBooking(int $bookingId, array $payload, ?Carbon $nowUtc = null): Booking
    {
        $nowUtc = ($nowUtc ?? Clock::now())->copy()->utc();

        foreach (['start_time', 'end_time'] as $field) {
            if (! array_key_exists($field, $payload) || $payload[$field] === null || trim((string) $payload[$field]) === '') {
                throw new DomainException("Field '{$field}' wajib diisi untuk ubah jadwal.", 400, 'INVALID_INPUT');
            }
        }

        [$newStartUtc, $newEndUtc, $newStartWib, $newEndWib] = $this->parseBookingTimes($payload);

        return DB::transaction(function () use ($bookingId, $newStartUtc, $newEndUtc, $newStartWib, $newEndWib, $nowUtc) {
            $booking = Booking::with(['room', 'additionalFacilities'])->where('id', $bookingId)->lockForUpdate()->first();

            if (! $booking) {
                throw new DomainException('Booking tidak ditemukan.', 404, 'BOOKING_NOT_FOUND');
            }

            if ($booking->status === Booking::STATUS_CANCELLED) {
                throw new DomainException(
                    'Booking yang sudah dibatalkan tidak dapat diubah jadwalnya.',
                    422,
                    'RESCHEDULE_ALREADY_CANCELLED'
                );
            }

            $oldStartUtc = Carbon::parse($booking->getRawOriginal('start_time') ?? $booking->start_time, 'UTC')->utc();
            $oldEndUtc = Carbon::parse($booking->getRawOriginal('end_time') ?? $booking->end_time, 'UTC')->utc();

            if ($nowUtc->greaterThanOrEqualTo($oldStartUtc)) {
                throw new DomainException(
                    'Booking yang sudah dimulai atau selesai tidak dapat diubah jadwalnya.',
                    422,
                    'RESCHEDULE_ALREADY_STARTED'
                );
            }

            $lockedRoom = Room::where('id', $booking->room_id)->lockForUpdate()->first();
            if (! $lockedRoom) {
                throw new DomainException('Ruang tidak ditemukan.', 404, 'ROOM_NOT_FOUND');
            }

            $this->validateBusinessRules($lockedRoom, $newStartUtc, $newEndUtc, $newStartWib, $newEndWib, (int) $booking->participants, $nowUtc);

            $oldDurationMinutes = $oldStartUtc->diffInMinutes($oldEndUtc);
            $newDurationMinutes = $newStartUtc->diffInMinutes($newEndUtc);

            if ($newDurationMinutes !== $oldDurationMinutes) {
                $oldHours = $oldDurationMinutes / 60;
                throw new DomainException(
                    "Durasi jadwal baru harus sama dengan durasi pesanan awal ({$oldHours} jam / {$oldDurationMinutes} menit) agar total tagihan tetap sesuai.",
                    422,
                    'RESCHEDULE_DURATION_MISMATCH'
                );
            }

            // BR-01: Check conflict against other CONFIRMED bookings in the same room (excluding this booking itself)
            $hasConflict = Booking::where('room_id', $lockedRoom->id)
                ->where('id', '!=', $booking->id)
                ->where('status', Booking::STATUS_CONFIRMED)
                ->where('start_time', '<', $newEndUtc->format('Y-m-d H:i:s'))
                ->where('end_time', '>', $newStartUtc->format('Y-m-d H:i:s'))
                ->lockForUpdate()
                ->exists();

            if ($hasConflict) {
                throw new DomainException(
                    'Jadwal baru bentrok dengan booking lain pada ruang yang sama. Silakan pilih jam lain yang kosong.',
                    409,
                    'BR_01_BOOKING_CONFLICT'
                );
            }

            $booking->update([
                'start_time' => $newStartUtc->format('Y-m-d H:i:s'),
                'end_time' => $newEndUtc->format('Y-m-d H:i:s'),
            ]);

            return $booking->fresh(['room', 'additionalFacilities']);
        });
    }

    /**
     * Cancel an existing booking enforcing FR-09, BR-05, and BR-06.
     */
    public function cancelBooking(int $bookingId, ?Carbon $nowUtc = null): Booking
    {
        $nowUtc = ($nowUtc ?? Clock::now())->copy()->utc();

        return DB::transaction(function () use ($bookingId, $nowUtc) {
            $booking = Booking::with(['room', 'additionalFacilities'])->where('id', $bookingId)->lockForUpdate()->first();

            if (! $booking) {
                throw new DomainException('Booking tidak ditemukan.', 404, 'BOOKING_NOT_FOUND');
            }

            // BR-06: Already CANCELLED booking cannot be cancelled again
            if ($booking->status === Booking::STATUS_CANCELLED) {
                throw new DomainException(
                    'Booking sudah dibatalkan sebelumnya dan tidak dapat dibatalkan lagi.',
                    422,
                    'BR_06_ALREADY_CANCELLED'
                );
            }

            $startUtc = Carbon::parse($booking->getRawOriginal('start_time') ?? $booking->start_time, 'UTC')->utc();

            // BR-06: Booking that has already started or finished cannot be cancelled
            if ($nowUtc->greaterThanOrEqualTo($startUtc)) {
                throw new DomainException(
                    'Booking yang sudah dimulai atau selesai tidak dapat dibatalkan.',
                    422,
                    'BR_06_ALREADY_STARTED'
                );
            }

            // BR-05: Cancellation < 2 hours before start_time incurs 50% penalty.
            // Exactly 2 hours (7200 seconds) or more incurs 0 penalty.
            $secondsUntilStart = $nowUtc->diffInSeconds($startUtc, false);
            $penaltyAmount = $secondsUntilStart < self::FREE_CANCEL_SECONDS
                ? (int) round($booking->total_price * self::PENALTY_RATE)
                : 0;

            $booking->update([
                'status' => Booking::STATUS_CANCELLED,
                'cancelled_at' => $nowUtc->format('Y-m-d H:i:s'),
                'penalty_amount' => $penaltyAmount,
            ]);

            return $booking->fresh(['room', 'additionalFacilities']);
        });
    }

    /**
     * Get availability slots for a room on a specific WIB date (FR-05).
     */
    public function getRoomAvailability(Room $room, string $dateStr): array
    {
        $dayWib = $this->parseWibDate($dateStr);

        $openWib = $dayWib->copy()->setTime(self::OPERATIONAL_START_HOUR, 0, 0);
        $closeWib = $dayWib->copy()->setTime(self::OPERATIONAL_END_HOUR, 0, 0);

        $openUtc = $openWib->copy()->utc();
        $closeUtc = $closeWib->copy()->utc();

        $bookings = Booking::where('room_id', $room->id)
            ->where('status', Booking::STATUS_CONFIRMED)
            ->where('start_time', '<', $closeUtc->format('Y-m-d H:i:s'))
            ->where('end_time', '>', $openUtc->format('Y-m-d H:i:s'))
            ->orderBy('start_time')
            ->get();

        $bookedSlots = [];
        foreach ($bookings as $booking) {
            $bStartWib = Carbon::parse($booking->getRawOriginal('start_time'), 'UTC')->setTimezone(Booking::TIMEZONE_WIB);
            $bEndWib = Carbon::parse($booking->getRawOriginal('end_time'), 'UTC')->setTimezone(Booking::TIMEZONE_WIB);

            $bookedSlots[] = [
                'booking_id' => $booking->id,
                'booker_name' => $booking->booker_name,
                'participants' => $booking->participants,
                'start_time' => $bStartWib->format('H:i'),
                'end_time' => $bEndWib->format('H:i'),
                'start_datetime' => $bStartWib->toIso8601String(),
                'end_datetime' => $bEndWib->toIso8601String(),
            ];
        }

        // Compute continuous free intervals between 08:00 and 21:00 WIB
        $availableSlots = [];
        $cursor = $openWib->copy();

        foreach ($bookings as $booking) {
            $bStartWib = Carbon::parse($booking->getRawOriginal('start_time'), 'UTC')->setTimezone(Booking::TIMEZONE_WIB);
            $bEndWib = Carbon::parse($booking->getRawOriginal('end_time'), 'UTC')->setTimezone(Booking::TIMEZONE_WIB);

            if ($bStartWib->greaterThan($cursor)) {
                $availableSlots[] = [
                    'start_time' => $cursor->format('H:i'),
                    'end_time' => $bStartWib->format('H:i'),
                    'start_datetime' => $cursor->toIso8601String(),
                    'end_datetime' => $bStartWib->toIso8601String(),
                    'duration_minutes' => $cursor->diffInMinutes($bStartWib),
                ];
            }

            if ($bEndWib->greaterThan($cursor)) {
                $cursor = $bEndWib->copy();
            }
        }

        if ($cursor->lessThan($closeWib)) {
            $availableSlots[] = [
                'start_time' => $cursor->format('H:i'),
                'end_time' => $closeWib->format('H:i'),
                'start_datetime' => $cursor->toIso8601String(),
                'end_datetime' => $closeWib->toIso8601String(),
                'duration_minutes' => $cursor->diffInMinutes($closeWib),
            ];
        }

        // Also provide 30-minute discrete slots for easy UI / calendar rendering
        $halfHourSlots = [];
        $slotCursor = $openWib->copy();
        while ($slotCursor->lessThan($closeWib)) {
            $slotEnd = $slotCursor->copy()->addMinutes(30);
            $isBooked = false;

            foreach ($bookings as $booking) {
                $bStartWib = Carbon::parse($booking->getRawOriginal('start_time'), 'UTC')->setTimezone(Booking::TIMEZONE_WIB);
                $bEndWib = Carbon::parse($booking->getRawOriginal('end_time'), 'UTC')->setTimezone(Booking::TIMEZONE_WIB);

                if ($slotCursor->lessThan($bEndWib) && $slotEnd->greaterThan($bStartWib)) {
                    $isBooked = true;
                    break;
                }
            }

            $halfHourSlots[] = [
                'start_time' => $slotCursor->format('H:i'),
                'end_time' => $slotEnd->format('H:i'),
                'status' => $isBooked ? 'BOOKED' : 'AVAILABLE',
                'is_available' => ! $isBooked,
            ];

            $slotCursor = $slotEnd;
        }

        return [
            'room' => $room,
            'date' => $dayWib->format('Y-m-d'),
            'timezone' => Booking::TIMEZONE_WIB,
            'operational_hours' => [
                'start' => '08:00',
                'end' => '21:00',
            ],
            'is_room_active' => (bool) $room->is_active,
            'booked_slots' => $bookedSlots,
            'available_slots' => $room->is_active ? $availableSlots : [],
            'slots' => $halfHourSlots,
        ];
    }

    /**
     * Validate required booking request fields (400 Bad Request if missing/empty).
     */
    protected function validateRequiredFields(array $payload): void
    {
        $required = ['room_id', 'booker_name', 'booker_contact', 'start_time', 'end_time', 'participants'];

        foreach ($required as $field) {
            if (! array_key_exists($field, $payload) || $payload[$field] === null || trim((string) $payload[$field]) === '') {
                throw new DomainException("Field '{$field}' wajib diisi.", 400, 'INVALID_INPUT');
            }
        }
    }

    /**
     * Parse booking start_time and end_time into UTC and WIB Carbon instances.
     *
     * @return array{0: Carbon, 1: Carbon, 2: Carbon, 3: Carbon}
     */
    public function parseBookingTimes(array $payload): array
    {
        $rawStart = trim((string) $payload['start_time']);
        $rawEnd = trim((string) $payload['end_time']);
        $rawDate = isset($payload['date']) ? trim((string) $payload['date']) : null;

        try {
            // If start_time is time-only like "09:00" or "09:00:00", combine with date
            if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $rawStart)) {
                if (! $rawDate) {
                    throw new DomainException("Field 'date' (YYYY-MM-DD) wajib diisi jika start_time hanya berupa jam.", 400, 'INVALID_INPUT');
                }
                $dayWib = $this->parseWibDate($rawDate);
                $startWib = Carbon::parse($dayWib->format('Y-m-d').' '.$rawStart, Booking::TIMEZONE_WIB);
            } else {
                $startUtcTemp = Clock::parseToUtc($rawStart);
                $startWib = $startUtcTemp->copy()->setTimezone(Booking::TIMEZONE_WIB);
            }

            if (preg_match('/^\d{1,2}:\d{2}(:\d{2})?$/', $rawEnd)) {
                $baseDate = $rawDate ? $this->parseWibDate($rawDate)->format('Y-m-d') : $startWib->format('Y-m-d');
                $endWib = Carbon::parse($baseDate.' '.$rawEnd, Booking::TIMEZONE_WIB);
            } else {
                $endUtcTemp = Clock::parseToUtc($rawEnd);
                $endWib = $endUtcTemp->copy()->setTimezone(Booking::TIMEZONE_WIB);
            }
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable) {
            throw new DomainException('Format tanggal atau waktu tidak valid.', 400, 'INVALID_INPUT');
        }

        return [
            $startWib->copy()->utc(),
            $endWib->copy()->utc(),
            $startWib,
            $endWib,
        ];
    }

    /**
     * Parse and validate a YYYY-MM-DD date string in WIB.
     */
    public function parseWibDate(string $dateStr): Carbon
    {
        $trimmed = trim($dateStr);
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $trimmed)) {
            throw new DomainException('Format tanggal harus YYYY-MM-DD.', 400, 'INVALID_DATE_FORMAT');
        }

        try {
            $parsed = Carbon::createFromFormat('Y-m-d', $trimmed, Booking::TIMEZONE_WIB);
            if (! $parsed || $parsed->format('Y-m-d') !== $trimmed) {
                throw new DomainException('Tanggal tidak valid.', 400, 'INVALID_DATE');
            }

            return $parsed->startOfDay();
        } catch (DomainException $e) {
            throw $e;
        } catch (Throwable) {
            throw new DomainException('Tanggal tidak valid.', 400, 'INVALID_DATE');
        }
    }

    /**
     * Validate BR-02, BR-03, BR-04, BR-06, BR-07 (all throw 422 on violation).
     */
    protected function validateBusinessRules(
        Room $room,
        Carbon $startUtc,
        Carbon $endUtc,
        Carbon $startWib,
        Carbon $endWib,
        int $participants,
        Carbon $nowUtc
    ): void {
        // BR-07: Inactive room cannot be booked
        if (! $room->is_active) {
            throw new DomainException(
                'Ruang sedang tidak aktif dan tidak dapat dibooking.',
                422,
                'BR_07_ROOM_INACTIVE'
            );
        }

        // BR-06: End time must be after start time
        if ($endUtc->lessThanOrEqualTo($startUtc)) {
            throw new DomainException(
                'Jam selesai harus setelah jam mulai.',
                422,
                'BR_06_INVALID_TIME_ORDER'
            );
        }

        // BR-04: Must not cross midnight in WIB and must fall within 08:00–21:00 WIB
        if ($startWib->format('Y-m-d') !== $endWib->format('Y-m-d')) {
            throw new DomainException(
                'Booking tidak boleh melintasi tengah malam (harus dalam hari yang sama pada jam operasional 08.00–21.00 WIB).',
                422,
                'BR_04_CROSS_MIDNIGHT'
            );
        }

        $openWib = $startWib->copy()->setTime(self::OPERATIONAL_START_HOUR, 0, 0);
        $closeWib = $startWib->copy()->setTime(self::OPERATIONAL_END_HOUR, 0, 0);

        if (
            $startWib->lessThan($openWib) ||
            $startWib->greaterThanOrEqualTo($closeWib) ||
            $endWib->greaterThan($closeWib) ||
            $endWib->lessThanOrEqualTo($openWib)
        ) {
            throw new DomainException(
                'Jam booking harus berada dalam jam operasional 08.00–21.00 WIB.',
                422,
                'BR_04_OPERATIONAL_HOURS'
            );
        }

        // BR-03: Duration must be between 1 hour (60 minutes) and 8 hours (480 minutes)
        $durationMinutes = $startUtc->diffInMinutes($endUtc);
        if ($durationMinutes < self::MIN_DURATION_MINUTES || $durationMinutes > self::MAX_DURATION_MINUTES) {
            throw new DomainException(
                'Durasi booking minimal 1 jam (60 menit) dan maksimal 8 jam (480 menit).',
                422,
                'BR_03_INVALID_DURATION'
            );
        }

        // BR-02: Participants must be > 0 and <= room capacity
        if ($participants <= 0 || $participants > $room->capacity) {
            throw new DomainException(
                "Jumlah peserta harus lebih dari 0 dan tidak melebihi kapasitas ruang ({$room->capacity} orang).",
                422,
                'BR_02_CAPACITY_EXCEEDED'
            );
        }

        // BR-06: Booking in the past is rejected
        if ($startUtc->lessThan($nowUtc)) {
            throw new DomainException(
                'Booking di masa lalu tidak diperbolehkan.',
                422,
                'BR_06_PAST_BOOKING'
            );
        }
    }
}
