<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_number',
        'resident_id',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'source',
        'assigned_to',
        'completion_notes',
        'completed_at',
    ];

    /**
     * Cast date fields.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'completed_at' => 'datetime',
        ];
    }

    /**
     * Resident that created or owns the request.
     */
    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }
}
