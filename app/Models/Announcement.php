<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    public const STATUS_DRAFT = 'Draft';

    public const STATUS_PUBLISHED = 'Published';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'category',
        'status',
        'is_pinned',
        'published_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    /**
     * Scope published announcements.
     */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_PUBLISHED);
    }

    /**
     * Scope admin ordering.
     */
    public function scopeOrderedForAdmin(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_pinned')
            ->orderByRaw('CASE WHEN published_at IS NULL THEN 1 ELSE 0 END')
            ->orderByDesc('published_at')
            ->orderByDesc('updated_at');
    }

    /**
     * Scope resident ordering.
     */
    public function scopeOrderedForResident(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_pinned')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }
}
