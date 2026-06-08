<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Module extends Model
{
    /**
     * System modules cannot be deleted or have their slug changed.
     *
     * @var list<string>
     */
    public const SYSTEM_SLUGS = ['resident-management', 'visitor-management', 'service-request', 'users', 'modules', 'access', 'roles'];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'sort_order',
        'is_active',
    ];

    /**
     * Get user permissions assigned for the module.
     */
    public function userModules(): HasMany
    {
        return $this->hasMany(UserModule::class);
    }

    /**
     * Determine whether the module is protected by the system.
     */
    public function isSystem(): bool
    {
        return in_array($this->slug, self::SYSTEM_SLUGS, true);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }
}
