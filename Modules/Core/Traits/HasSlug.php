<?php

declare(strict_types=1);

namespace Modules\Core\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Increment slug if it already exists.
     */
    public function incrementSlug(string $slug): string
    {
        $original = $slug;
        $count = 2;
        while (static::whereSlug($slug)->exists()) {
            $slug = "{$original}-{$count}";
            $count++;
        }

        return $slug;
    }

    /**
     * Boot the trait.
     */
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model): void {
            if (empty($model->slug) && ! empty($model->title)) {
                $slug = Str::slug($model->title);
                $model->slug = $model->incrementSlug($slug);
            }
        });

        static::updating(function ($model): void {
            if (empty($model->slug) && ! empty($model->title)) {
                $slug = Str::slug($model->title);
                $model->slug = $model->incrementSlug($slug);
            }
        });
    }
}
