<?php

namespace Modules\Core\Traits;

use Illuminate\Support\Str;

trait HasSlug
{
    /**
     * Boot the trait.
     */
    protected static function bootHasSlug(): void
    {
        static::creating(function ($model) {
            $slug = Str::slug($model->title);
            $model->slug = $model->incrementSlug($slug);
        });
    }

    /**
     * Increment slug if it already exists.
     *
     * @param string $slug
     * @return string
     */
    public function incrementSlug(string $slug): string
    {
        $original = $slug;
        $count = 2;
        while (static::whereSlug($slug)->exists()) {
            $slug = "{$original}-" . $count++;
        }

        return $slug;
    }
}
