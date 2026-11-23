<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    public const LAYOUT_BOARDROOM = 'BOARDROOM';
    public const LAYOUT_U_SHAPE = 'U_SHAPE';
    public const LAYOUT_CLASSROOM = 'CLASSROOM';
    public const LAYOUT_POD = 'POD';

    public const VALID_LAYOUT_TYPES = [
        self::LAYOUT_BOARDROOM,
        self::LAYOUT_U_SHAPE,
        self::LAYOUT_CLASSROOM,
        self::LAYOUT_POD,
    ];

    protected $fillable = [
        'name',
        'capacity',
        'length_m',
        'width_m',
        'layout_type',
        'facilities',
        'price_per_hour',
        'is_active',
    ];

    protected $appends = [
        'area_sqm',
        'dimensions_label',
        'layout_label',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'length_m' => 'float',
            'width_m' => 'float',
            'price_per_hour' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function getAreaSqmAttribute(): float
    {
        $l = (float) ($this->length_m ?: 6.0);
        $w = (float) ($this->width_m ?: 4.0);

        return round($l * $w, 1);
    }

    public function getDimensionsLabelAttribute(): string
    {
        $l = rtrim(rtrim(number_format((float) ($this->length_m ?: 6.0), 1, '.', ''), '0'), '.');
        $w = rtrim(rtrim(number_format((float) ($this->width_m ?: 4.0), 1, '.', ''), '0'), '.');

        return "{$l}m × {$w}m";
    }

    public function getLayoutLabelAttribute(): string
    {
        return match (strtoupper((string) $this->layout_type)) {
            self::LAYOUT_U_SHAPE => 'U-Shape Conference',
            self::LAYOUT_CLASSROOM => 'Classroom / Seminar',
            self::LAYOUT_POD => 'Compact Huddle Pod',
            default => 'Boardroom Executive',
        };
    }

    /**
     * Normalize facilities to always be stored and returned as an array of strings.
     */
    protected function facilities(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value): array {
                if (is_array($value)) {
                    return array_values($value);
                }

                if (is_string($value) && $value !== '') {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        return array_values($decoded);
                    }

                    return array_values(array_filter(array_map('trim', explode(',', $value))));
                }

                return [];
            },
            set: function (mixed $value): string {
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    if (is_array($decoded)) {
                        $value = array_values($decoded);
                    } else {
                        $value = array_values(array_filter(array_map('trim', explode(',', $value))));
                    }
                } elseif (! is_array($value)) {
                    $value = [];
                }

                return json_encode(array_values($value), JSON_UNESCAPED_UNICODE);
            }
        );
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
