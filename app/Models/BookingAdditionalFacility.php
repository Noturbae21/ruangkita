<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookingAdditionalFacility extends Model
{
    protected $table = 'booking_additional_facility';

    protected $fillable = [
        'booking_id',
        'additional_facility_id',
        'facility_name',
        'pricing_type',
        'unit_price',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'booking_id' => 'integer',
            'additional_facility_id' => 'integer',
            'unit_price' => 'integer',
            'quantity' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function additionalFacility(): BelongsTo
    {
        return $this->belongsTo(AdditionalFacility::class, 'additional_facility_id');
    }

    public function toApiArray(): array
    {
        return [
            'id' => $this->id,
            'additional_facility_id' => $this->additional_facility_id,
            'name' => $this->facility_name,
            'pricing_type' => $this->pricing_type,
            'unit_price' => $this->unit_price,
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
        ];
    }
}
