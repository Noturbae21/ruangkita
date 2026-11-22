<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdditionalFacility extends Model
{
    use HasFactory;

    public const PRICING_PER_BOOKING = 'PER_BOOKING';
    public const PRICING_PER_HOUR = 'PER_HOUR';
    public const PRICING_PER_PARTICIPANT = 'PER_PARTICIPANT';

    public const VALID_PRICING_TYPES = [
        self::PRICING_PER_BOOKING,
        self::PRICING_PER_HOUR,
        self::PRICING_PER_PARTICIPANT,
    ];

    protected $fillable = [
        'name',
        'description',
        'price',
        'pricing_type',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function bookingItems(): HasMany
    {
        return $this->hasMany(BookingAdditionalFacility::class, 'additional_facility_id');
    }

    /**
     * Calculate subtotal for this facility given quantity, duration in minutes, and participants.
     */
    public function calculateSubtotal(int $quantity, int $durationMinutes, int $participants): int
    {
        $qty = max(1, $quantity);
        $durationHours = $durationMinutes / 60;

        return match ($this->pricing_type) {
            self::PRICING_PER_HOUR => (int) round($this->price * $durationHours * $qty),
            self::PRICING_PER_PARTICIPANT => (int) round($this->price * $participants * $qty),
            default => (int) round($this->price * $qty),
        };
    }

    public function pricingLabel(): string
    {
        return match ($this->pricing_type) {
            self::PRICING_PER_HOUR => 'Per Jam',
            self::PRICING_PER_PARTICIPANT => 'Per Peserta',
            default => 'Per Booking (Flat)',
        };
    }
}
