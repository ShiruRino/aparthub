<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'username',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the role assigned to the user.
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Get module permissions assigned directly to the user.
     */
    public function userModules(): HasMany
    {
        return $this->hasMany(UserModule::class);
    }

    /**
     * Determine whether the user has the admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    /**
     * Check whether the user can perform an action on an active module.
     */
    public function canAccessModule(string $moduleSlug, string $action): bool
    {
        if ($this->isAdmin()) {
            return true;
        }

        $column = match ($action) {
            'create' => 'can_create',
            'read' => 'can_read',
            'update' => 'can_update',
            'delete' => 'can_delete',
            default => null,
        };

        if ($column === null) {
            return false;
        }

        $permission = $this->userModules()
            ->whereHas('module', fn ($query) => $query
                ->where('slug', $moduleSlug)
                ->where('is_active', true))
            ->first();

        return (bool) ($permission?->{$column});
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
