<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FacilityBooking extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'facility_id',
        'resident_id',
        'booking_title',
        'booking_date',
        'time_slot',
        'status',
        'notes',
    ];

    /**
     * Cast date fields.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
        ];
    }

    /**
     * Facility tied to the booking.
     */
    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }

    /**
     * Resident tied to the booking.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
