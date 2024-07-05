<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Facades\Cache;

trait ClearsCache
{
    protected static function bootClearsCache(): void
    {
        static::creating(function ($model) {
            $model->clearCache();
        });

        static::updated(function ($model) {
            $model->clearCache();
        });
    }

    public function clearCache(): void
    {
        Cache::forget($this->cacheKey());
    }

    public function cacheKey(): string
    {
        return get_class($this).':'.$this->id;
    }
}
