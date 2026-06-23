<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceRequestSubcategory extends Model
{
    protected $fillable = [
        'service_request_category_id',
        'name',
        'is_active',
        'sort_order',
        'low_sla_minutes',
        'medium_sla_minutes',
        'high_sla_minutes',
        'emergency_sla_minutes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
            'low_sla_minutes' => 'integer',
            'medium_sla_minutes' => 'integer',
            'high_sla_minutes' => 'integer',
            'emergency_sla_minutes' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestCategory::class, 'service_request_category_id');
    }

    public function serviceRequests(): HasMany
    {
        return $this->hasMany(ServiceRequest::class);
    }

    public function slaMinutesFor(string $priority): int
    {
        return match ($priority) {
            ServiceRequest::PRIORITY_LOW => $this->low_sla_minutes,
            ServiceRequest::PRIORITY_MEDIUM => $this->medium_sla_minutes,
            ServiceRequest::PRIORITY_HIGH => $this->high_sla_minutes,
            ServiceRequest::PRIORITY_EMERGENCY => $this->emergency_sla_minutes,
            default => $this->medium_sla_minutes,
        };
    }
}
