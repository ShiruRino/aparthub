<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    /**
     * System roles cannot be deleted or have their slug changed.
     *
     * @var list<string>
     */
    public const SYSTEM_SLUGS = ['admin'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
    ];

    /**
     * Get users assigned to the role.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Determine whether the role is protected by the system.
     */
    public function isSystem(): bool
    {
        return in_array($this->slug, self::SYSTEM_SLUGS, true);
    }
}
