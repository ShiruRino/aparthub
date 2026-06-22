<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Facility extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'location',
        'category',
        'status',
        'capacity',
        'description',
    ];

    /**
     * Bookings for this facility.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(FacilityBooking::class);
    }
}
