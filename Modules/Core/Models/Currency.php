<?php

declare(strict_types=1);

namespace Modules\Core\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

/**
 * Display currency for the storefront. All amounts are STORED in the base
 * currency (is_default) and converted for display only.
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $symbol
 * @property string $symbol_position
 * @property float $exchange_rate
 * @property int $decimals
 * @property bool $is_default
 * @property bool $is_active
 */
class Currency extends Model
{
    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'exchange_rate',
        'decimals',
        'is_default',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'exchange_rate' => 'float',
        'decimals' => 'int',
        'is_default' => 'bool',
        'is_active' => 'bool',
    ];

    protected static function booted(): void
    {
        $flush = static fn () => Cache::forget('currencies.active');
        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * @return \Illuminate\Support\Collection<int, Currency>
     */
    public static function activeList(): \Illuminate\Support\Collection
    {
        return Cache::remember(
            'currencies.active',
            3600,
            fn () => self::where('is_active', true)->orderBy('sort_order')->get()
        );
    }

    public static function default(): ?self
    {
        return self::activeList()->firstWhere('is_default', true)
            ?? self::activeList()->first();
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Convert a base-currency amount into this currency and format it.
     */
    public function format(float $amount): string
    {
        $converted = number_format($amount * $this->exchange_rate, $this->decimals);

        return $this->symbol_position === 'right'
            ? $converted.' '.$this->symbol
            : $this->symbol.$converted;
    }
}
