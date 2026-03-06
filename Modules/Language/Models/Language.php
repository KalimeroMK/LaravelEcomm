<?php

declare(strict_types=1);

namespace Modules\Language\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Core\Models\Core;

/**
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $native_name
 * @property string|null $flag
 * @property bool $is_default
 * @property bool $is_active
 * @property string $direction
 * @property int $sort_order
 * @property array|null $meta
 */
class Language extends Core
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'native_name',
        'flag',
        'is_default',
        'is_active',
        'direction',
        'sort_order',
        'meta',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'meta' => 'array',
    ];

    /**
     * Get only active languages
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Get only default language
     */
    public function scopeDefault(Builder $query): Builder
    {
        return $query->where('is_default', true);
    }

    /**
     * Order by sort order
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Get the default language code
     */
    public static function getDefaultCode(): string
    {
        return cache()->remember('language.default_code', 3600, function () {
            return static::default()->value('code') ?? config('app.locale', 'en');
        });
    }

    /**
     * Get all active language codes
     *
     * @return array<string>
     */
    public static function getActiveCodes(): array
    {
        return cache()->remember('language.active_codes', 3600, function () {
            return static::active()->pluck('code')->toArray();
        });
    }

    /**
     * Get all active languages as key-value pairs
     *
     * @return array<string, string>
     */
    public static function getActiveList(): array
    {
        return cache()->remember('language.active_list', 3600, function () {
            return static::active()->ordered()->pluck('name', 'code')->toArray();
        });
    }

    /**
     * Check if a language code is valid (active)
     */
    public static function isValidCode(string $code): bool
    {
        return in_array($code, static::getActiveCodes(), true);
    }

    /**
     * Clear language cache
     */
    public static function clearLanguageCache(): void
    {
        cache()->forget('language.default_code');
        cache()->forget('language.active_codes');
        cache()->forget('language.active_list');
    }

    /**
     * Boot the model
     */
    protected static function boot(): void
    {
        parent::boot();

        static::saved(function (): void {
            static::clearLanguageCache();
        });

        static::deleted(function (): void {
            static::clearLanguageCache();
        });
    }
}
