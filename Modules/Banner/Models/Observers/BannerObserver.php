<?php

namespace Modules\Banner\Models\Observers;

use Illuminate\Support\Str;
use Modules\Banner\Models\Banner;

class BannerObserver
{
    /**
     * Handle the banner "created" event.
     *
     * @param  Banner  $banner
     */
    public function creating(Banner $banner): void
    {
        $slug = Str::slug($banner->title);
        if (Banner::whereSlug($slug)->count() > 0) {
            $banner->slug = $slug;
        }
        $banner->slug = $banner->incrementSlug($slug);
    }
}
