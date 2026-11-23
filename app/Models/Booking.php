<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const TIMEZONE_WIB = 'Asia/Jakarta';

    public const PAYMENT_METHOD_QRIS = 'QRIS';
    public const PAYMENT_STATUS_UNPAID = 'UNPAID';
    public const PAYMENT_STATUS_PAID = 'PAID';
    public const PAYMENT_STATUS_REFUNDED = 'REFUNDED';

    protected $fillable = [
        'booking_code',
        'user_id',
        'room_id',
        'booker_name',
        'booker_contact',
        'start_time',
        'end_time',
        'participants',
        'room_price',
        'additional_facilities_price',
        'total_price',
        'payment_method',
        'payment_status',
        'payment_reference',
        'qris_payload',
        'payment_expired_at',
        'paid_at',
        'status',
        'cancelled_at',
        'penalty_amount',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
            'room_id' => 'integer',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'cancelled_at' => 'datetime',
            'payment_expired_at' => 'datetime',
            'paid_at' => 'datetime',
            'participants' => 'integer',
            'room_price' => 'integer',
            'additional_facilities_price' => 'integer',
            'total_price' => 'integer',
            'penalty_amount' => 'integer',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function additionalFacilities(): HasMany
    {
        return $this->hasMany(BookingAdditionalFacility::class);
    }

    /**
     * Format booking data with WIB display fields while preserving UTC storage.
     */
    public function toApiArray(): array
    {
        $startUtc = Carbon::parse($this->getRawOriginal('start_time') ?? $this->start_time, 'UTC')->utc();
        $endUtc = Carbon::parse($this->getRawOriginal('end_time') ?? $this->end_time, 'UTC')->utc();

        $startWib = $startUtc->copy()->setTimezone(self::TIMEZONE_WIB);
        $endWib = $endUtc->copy()->setTimezone(self::TIMEZONE_WIB);

        $cancelledUtc = $this->cancelled_at
            ? Carbon::parse($this->getRawOriginal('cancelled_at') ?? $this->cancelled_at, 'UTC')->utc()
            : null;
        $cancelledWib = $cancelledUtc?->copy()->setTimezone(self::TIMEZONE_WIB);

        $paidUtc = $this->paid_at
            ? Carbon::parse($this->getRawOriginal('paid_at') ?? $this->paid_at, 'UTC')->utc()
            : null;
        $paidWib = $paidUtc?->copy()->setTimezone(self::TIMEZONE_WIB);

        $expiredUtc = $this->payment_expired_at
            ? Carbon::parse($this->getRawOriginal('payment_expired_at') ?? $this->payment_expired_at, 'UTC')->utc()
            : null;
        $expiredWib = $expiredUtc?->copy()->setTimezone(self::TIMEZONE_WIB);

        $addonsPrice = (int) ($this->additional_facilities_price ?? 0);
        $roomPrice = $this->room_price !== null
            ? (int) $this->room_price
            : max(0, (int) $this->total_price - $addonsPrice);

        $bookingCode = $this->booking_code ?: sprintf('RK-%s-%04d', $startWib->format('Ymd'), $this->id);

        $data = [
            'id' => $this->id,
            'booking_code' => $bookingCode,
            'user_id' => $this->user_id,
            'room_id' => $this->room_id,
            'booker_name' => $this->booker_name,
            'booker_contact' => $this->booker_contact,
            'date' => $startWib->format('Y-m-d'),
            'start_time' => $startWib->toIso8601String(),
            'end_time' => $endWib->toIso8601String(),
            'start_hour' => $startWib->format('H:i'),
            'end_hour' => $endWib->format('H:i'),
            'start_time_utc' => $startUtc->toIso8601ZuluString(),
            'end_time_utc' => $endUtc->toIso8601ZuluString(),
            'duration_hours' => round($startUtc->diffInMinutes($endUtc) / 60, 2),
            'participants' => $this->participants,
            'room_price' => $roomPrice,
            'additional_facilities_price' => $addonsPrice,
            'total_price' => $this->total_price,
            'payment_method' => $this->payment_method ?: self::PAYMENT_METHOD_QRIS,
            'payment_status' => $this->payment_status ?: self::PAYMENT_STATUS_UNPAID,
            'payment_reference' => $this->payment_reference,
            'qris_payload' => $this->qris_payload,
            'payment_expired_at' => $expiredWib?->toIso8601String(),
            'paid_at' => $paidWib?->toIso8601String(),
            'status' => $this->status,
            'cancelled_at' => $cancelledWib?->toIso8601String(),
            'penalty_amount' => $this->penalty_amount,
            'created_at' => $this->created_at?->copy()->setTimezone(self::TIMEZONE_WIB)->toIso8601String(),
            'additional_facilities' => $this->relationLoaded('additionalFacilities')
                ? $this->additionalFacilities->map(fn (BookingAdditionalFacility $item) => $item->toApiArray())->values()->all()
                : [],
        ];

        if ($this->relationLoaded('room') && $this->room) {
            $data['room'] = $this->room;
        }

        return $data;
    }
}
