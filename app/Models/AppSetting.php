<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    public static function getInteger(string $key): ?int
    {
        $value = static::query()->where('key', $key)->value('value');

        return is_numeric($value) ? (int) $value : null;
    }

    public static function putInteger(string $key, ?int $value): void
    {
        static::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value === null ? null : (string) $value]
        );
    }
}
