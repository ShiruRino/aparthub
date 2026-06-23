<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class ServiceRequest extends Model
{
    public const PRIORITY_LOW = 'Low';

    public const PRIORITY_MEDIUM = 'Medium';

    public const PRIORITY_HIGH = 'High';

    public const PRIORITY_EMERGENCY = 'Emergency';

    public const STATUS_SUBMITTED = 'Submitted';

    public const STATUS_ASSIGNED = 'Assigned';

    public const STATUS_IN_PROGRESS = 'In Progress';

    public const STATUS_COMPLETED = 'Completed';

    public const STATUS_CANCELLED = 'Cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ticket_number',
        'resident_id',
        'service_request_category_id',
        'service_request_subcategory_id',
        'category',
        'title',
        'description',
        'priority',
        'status',
        'source',
        'sla_target_minutes',
        'sla_due_at',
        'assigned_to',
        'assigned_at',
        'in_progress_at',
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
            'sla_target_minutes' => 'integer',
            'sla_due_at' => 'datetime',
            'assigned_at' => 'datetime',
            'in_progress_at' => 'datetime',
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

    public function categoryMaster(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestCategory::class, 'service_request_category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(ServiceRequestSubcategory::class, 'service_request_subcategory_id');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ServiceRequestAttachment::class);
    }

    public static function priorityOptions(): array
    {
        return [
            self::PRIORITY_LOW,
            self::PRIORITY_MEDIUM,
            self::PRIORITY_HIGH,
            self::PRIORITY_EMERGENCY,
        ];
    }

    public static function canonicalStatusOptions(): array
    {
        return [
            self::STATUS_SUBMITTED,
            self::STATUS_ASSIGNED,
            self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED,
            self::STATUS_CANCELLED,
        ];
    }

    public static function mobileVisibleStatus(string $status): string
    {
        return match ($status) {
            'New', 'Pending', self::STATUS_SUBMITTED => self::STATUS_SUBMITTED,
            self::STATUS_ASSIGNED => self::STATUS_ASSIGNED,
            'Over SLA', self::STATUS_IN_PROGRESS => self::STATUS_IN_PROGRESS,
            self::STATUS_COMPLETED => self::STATUS_COMPLETED,
            self::STATUS_CANCELLED => self::STATUS_CANCELLED,
            default => self::STATUS_SUBMITTED,
        };
    }

    public function canonicalStatus(): string
    {
        return self::mobileVisibleStatus($this->status);
    }

    public function isOverSla(?Carbon $now = null): bool
    {
        $now ??= now();

        if (! $this->sla_due_at) {
            return false;
        }

        if ($this->canonicalStatus() === self::STATUS_COMPLETED) {
            return (bool) $this->completed_at?->gt($this->sla_due_at);
        }

        if ($this->canonicalStatus() === self::STATUS_CANCELLED) {
            return false;
        }

        return $now->gt($this->sla_due_at);
    }

    public function slaState(): string
    {
        return $this->isOverSla() ? 'Over SLA' : 'Within SLA';
    }

    public function operationalTimestamp(): ?Carbon
    {
        return $this->created_at;
    }

    public function timeline(): array
    {
        return array_values(array_filter([
            [
                'label' => self::STATUS_SUBMITTED,
                'timestamp' => $this->created_at?->toIso8601String(),
            ],
            $this->assigned_at ? [
                'label' => self::STATUS_ASSIGNED,
                'timestamp' => $this->assigned_at->toIso8601String(),
            ] : null,
            $this->in_progress_at ? [
                'label' => self::STATUS_IN_PROGRESS,
                'timestamp' => $this->in_progress_at->toIso8601String(),
            ] : null,
            $this->completed_at ? [
                'label' => self::STATUS_COMPLETED,
                'timestamp' => $this->completed_at->toIso8601String(),
            ] : null,
        ]));
    }

    public function scopeCanonicalStatus(Builder $query, string $status): Builder
    {
        return match ($status) {
            self::STATUS_SUBMITTED => $query->whereIn('status', ['New', 'Pending', self::STATUS_SUBMITTED]),
            self::STATUS_ASSIGNED => $query->where('status', self::STATUS_ASSIGNED),
            self::STATUS_IN_PROGRESS => $query->whereIn('status', ['Over SLA', self::STATUS_IN_PROGRESS]),
            self::STATUS_COMPLETED => $query->where('status', self::STATUS_COMPLETED),
            self::STATUS_CANCELLED => $query->where('status', self::STATUS_CANCELLED),
            default => $query,
        };
    }

    public function scopeOverSla(Builder $query): Builder
    {
        return $query->whereNotNull('sla_due_at')
            ->where(function (Builder $builder) {
                $builder
                    ->where(function (Builder $active) {
                        $active->whereNotIn('status', [self::STATUS_COMPLETED, self::STATUS_CANCELLED])
                            ->where('sla_due_at', '<', now());
                    })
                    ->orWhere(function (Builder $completed) {
                        $completed->where('status', self::STATUS_COMPLETED)
                            ->whereColumn('completed_at', '>', 'sla_due_at');
                    });
            });
    }
}
