<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsCache
{
    public function clearCache(): void
    {
        Cache::forget($this->cacheKey());
    }

    public function cacheKey(): string
    {
        return get_class($this).':'.$this->id;
    }

    protected static function bootClearsCache(): void
    {
        static::creating(function ($model): void {
            $model->clearCache();
        });

        static::updated(function ($model): void {
            $model->clearCache();
        });
    }
}
