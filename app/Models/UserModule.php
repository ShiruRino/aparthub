<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserModule extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'module_id',
        'can_create',
        'can_read',
        'can_update',
        'can_delete',
    ];

    /**
     * Get the user assigned to the module permission.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the module assigned to the user permission.
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'can_create' => 'boolean',
            'can_read' => 'boolean',
            'can_update' => 'boolean',
            'can_delete' => 'boolean',
        ];
    }
}
