<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'code',
        'tower',
        'floor',
        'unit_type',
        'occupancy_status',
        'payment_status',
        'thumbnail_tone',
    ];

    /**
     * Residents currently or previously linked to the unit.
     */
    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }

    /**
     * Move requests linked to the unit.
     */
    public function moveRequests(): HasMany
    {
        return $this->hasMany(ResidentMoveRequest::class);
    }

    /**
     * Vehicles linked to the unit.
     */
    public function vehicles(): HasMany
    {
        return $this->hasMany(ResidentVehicle::class);
    }
}
