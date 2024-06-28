<?php

namespace Modules\Brand\Models\Observers;

use Illuminate\Support\Str;
use Modules\Brand\Models\Brand;

class BrandObserver
{
    /**
     * Handle the brand "created" event.
     *
     * @param  Brand  $brand
     */
    public function creating(Brand $brand): void
    {
        $slug = Str::slug($brand->title);
        if (Brand::whereSlug($slug)->count() > 0) {
            $brand->slug = $slug;
        }
        $brand->slug = $brand->incrementSlug($slug);
    }
}
