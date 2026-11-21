<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'capacity',
        'facilities',
        'price_per_hour',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'capacity' => 'integer',
            'price_per_hour' => 'integer',
            'is_active' => 'boolean',
        ];
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
