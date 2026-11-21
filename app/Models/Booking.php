<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public const STATUS_CONFIRMED = 'CONFIRMED';
    public const STATUS_CANCELLED = 'CANCELLED';
    public const TIMEZONE_WIB = 'Asia/Jakarta';

    protected $fillable = [
        'room_id',
        'booker_name',
        'booker_contact',
        'start_time',
        'end_time',
        'participants',
        'total_price',
        'status',
        'cancelled_at',
        'penalty_amount',
    ];

    protected function casts(): array
    {
        return [
            'room_id' => 'integer',
            'start_time' => 'datetime',
            'end_time' => 'datetime',
            'cancelled_at' => 'datetime',
            'participants' => 'integer',
            'total_price' => 'integer',
            'penalty_amount' => 'integer',
        ];
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
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

        $data = [
            'id' => $this->id,
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
            'total_price' => $this->total_price,
            'status' => $this->status,
            'cancelled_at' => $cancelledWib?->toIso8601String(),
            'penalty_amount' => $this->penalty_amount,
            'created_at' => $this->created_at?->copy()->setTimezone(self::TIMEZONE_WIB)->toIso8601String(),
        ];

        if ($this->relationLoaded('room') && $this->room) {
            $data['room'] = $this->room;
        }

        return $data;
    }
}
