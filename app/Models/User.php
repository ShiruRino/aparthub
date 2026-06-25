<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'role_id',
        'name',
        'username',
        'email',
        'mobile_no',
        'is_active',
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

    public function technicianProfile(): HasOne
    {
        return $this->hasOne(TechnicianProfile::class);
    }

    public function technicianTeams(): BelongsToMany
    {
        return $this->belongsToMany(TechnicianTeam::class, 'technician_team_user')
            ->withTimestamps();
    }

    public function serviceRequestEvents(): HasMany
    {
        return $this->hasMany(ServiceRequestEvent::class, 'acted_by_user_id');
    }

    /**
     * Determine whether the user has the admin role.
     */
    public function isAdmin(): bool
    {
        return $this->role?->slug === 'admin';
    }

    public function isTechnician(): bool
    {
        return $this->role?->slug === 'technician';
    }

    public function activeForApi(): bool
    {
        return $this->is_active !== false;
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
            'is_active' => 'boolean',
        ];
    }
}
