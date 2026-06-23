<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

class Visitor extends Model
{
    public const STATUS_PENDING = 'Pending';

    public const STATUS_APPROVED = 'Approved';

    public const STATUS_REJECTED = 'Rejected';

    public const STATUS_CHECKED_IN = 'Checked In';

    public const STATUS_CHECKED_OUT = 'Checked Out';

    public const STATUS_CANCELLED = 'Cancelled';

    public const STATUS_EXPIRED = 'Expired';

    public const SOURCE_RESIDENT_APP = 'Resident App';

    public const SOURCE_ADMIN_WALK_IN = 'Admin Walk-In';

    /**
     * @var list<string>
     */
    protected $fillable = [
        'resident_id',
        'visitor_name',
        'visitor_phone',
        'visit_date',
        'estimated_arrival_time',
        'guest_count',
        'visit_purpose',
        'identity_photo_path',
        'status',
        'registration_source',
        'access_code',
        'approved_at',
        'rejected_at',
        'cancelled_at',
        'checked_in_at',
        'checked_out_at',
        'expires_at',
        'access_card_number',
        'rejection_reason',
        'cancellation_reason',
    ];

    /**
     * @var list<string>
     */
    protected $hidden = [
        'access_code',
        'identity_photo_path',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'estimated_arrival_time' => 'datetime:H:i',
            'guest_count' => 'integer',
            'approved_at' => 'datetime',
            'rejected_at' => 'datetime',
            'cancelled_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public static function statuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_REJECTED,
            self::STATUS_CHECKED_IN,
            self::STATUS_CHECKED_OUT,
            self::STATUS_CANCELLED,
            self::STATUS_EXPIRED,
        ];
    }

    public static function adminCreatableStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_APPROVED,
            self::STATUS_CHECKED_IN,
        ];
    }

    public function resident(): BelongsTo
    {
        return $this->belongsTo(Resident::class);
    }

    public function scopeStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        if (! $search) {
            return $query;
        }

        return $query->where(function (Builder $builder) use ($search) {
            $builder->where('visitor_name', 'like', '%'.$search.'%')
                ->orWhere('visitor_phone', 'like', '%'.$search.'%')
                ->orWhere('access_code', 'like', '%'.$search.'%')
                ->orWhereHas('resident', fn (Builder $residentQuery) => $residentQuery->where('name', 'like', '%'.$search.'%'))
                ->orWhereHas('resident.unit', fn (Builder $unitQuery) => $unitQuery->where('code', 'like', '%'.$search.'%'));
        });
    }

    public function isExpired(?Carbon $now = null): bool
    {
        $now ??= now();

        if ($this->status === self::STATUS_EXPIRED) {
            return true;
        }

        if (! $this->expires_at) {
            return false;
        }

        return $now->greaterThan($this->expires_at);
    }

    public function canResidentEdit(): bool
    {
        return $this->status === self::STATUS_PENDING && ! $this->isExpired();
    }

    public function canResidentCancel(): bool
    {
        return in_array($this->status, [self::STATUS_PENDING, self::STATUS_APPROVED], true)
            && ! $this->checked_in_at
            && ! $this->isExpired();
    }

    public function canAdminApprove(): bool
    {
        return $this->status === self::STATUS_PENDING && ! $this->isExpired();
    }

    public function canAdminReject(): bool
    {
        return $this->status === self::STATUS_PENDING && ! $this->isExpired();
    }

    public function canAdminCheckIn(): bool
    {
        return $this->status === self::STATUS_APPROVED && $this->hasValidAccessCodeNow();
    }

    public function canAdminCheckOut(): bool
    {
        return $this->status === self::STATUS_CHECKED_IN && ! $this->checked_out_at;
    }

    public function hasValidAccessCodeNow(?Carbon $now = null): bool
    {
        $now ??= now();

        if ($this->status !== self::STATUS_APPROVED) {
            return false;
        }

        if ($this->isExpired($now)) {
            return false;
        }

        if (! $this->visit_date || ! $this->expires_at) {
            return false;
        }

        return $now->isSameDay($this->visit_date) && $now->lessThanOrEqualTo($this->expires_at);
    }

    public function qrAvailable(): bool
    {
        return $this->hasValidAccessCodeNow();
    }

    /**
     * @return list<array{label:string,timestamp:?string}>
     */
    public function timeline(): array
    {
        return array_values(array_filter([
            [
                'label' => 'Created',
                'timestamp' => $this->created_at?->toIso8601String(),
            ],
            $this->approved_at ? [
                'label' => self::STATUS_APPROVED,
                'timestamp' => $this->approved_at->toIso8601String(),
            ] : null,
            $this->rejected_at ? [
                'label' => self::STATUS_REJECTED,
                'timestamp' => $this->rejected_at->toIso8601String(),
            ] : null,
            $this->cancelled_at ? [
                'label' => self::STATUS_CANCELLED,
                'timestamp' => $this->cancelled_at->toIso8601String(),
            ] : null,
            $this->checked_in_at ? [
                'label' => self::STATUS_CHECKED_IN,
                'timestamp' => $this->checked_in_at->toIso8601String(),
            ] : null,
            $this->checked_out_at ? [
                'label' => self::STATUS_CHECKED_OUT,
                'timestamp' => $this->checked_out_at->toIso8601String(),
            ] : null,
            $this->status === self::STATUS_EXPIRED ? [
                'label' => self::STATUS_EXPIRED,
                'timestamp' => $this->updated_at?->toIso8601String(),
            ] : null,
        ]));
    }
}
