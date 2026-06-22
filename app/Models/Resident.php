<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Resident extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'unit_id',
        'name',
        'resident_type',
        'status',
        'move_in_date',
        'move_out_date',
        'avatar_tone',
    ];

    /**
     * The attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'move_in_date' => 'date',
            'move_out_date' => 'date',
        ];
    }

    /**
     * Unit currently linked to the resident.
     */
    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * Family members linked to the resident.
     */
    public function familyMembers(): HasMany
    {
        return $this->hasMany(ResidentFamilyMember::class);
    }

    /**
     * Move requests linked to the resident.
     */
    public function moveRequests(): HasMany
    {
        return $this->hasMany(ResidentMoveRequest::class);
    }

    /**
     * Vehicles linked to the resident.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(ResidentVehicle::class);
    }

    /**
     * Facility bookings linked to the resident.
     */
    public function facilityBookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class);
    }
}
