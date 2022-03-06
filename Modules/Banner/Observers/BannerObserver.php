<?php

    namespace Modules\Banner\Observers;

    use Illuminate\Support\Str;
    use Modules\Banner\Models\Banner;

    class BannerObserver
    {
        /**
         * Handle the banner "created" event.
         *
         * @param  Banner  $banner
         */
        public function creating(Banner $banner)
        {
            $slug = Str::slug($banner->title);
            if (Banner::whereSlug($slug)->count() > 0) {
                $banner->slug = $slug;
            }
            $banner->slug = $banner->incrementSlug($slug);
        }

        /**
         * Handle the banner "updated" event.
         *
         * @param  Banner  $banner
         */
        public function updating(Banner $banner)
        {
        }

        /**
         * Handle the banner "deleted" event.
         *
         * @param  Banner  $banner
         */
        public function deleted(Banner $banner)
        {
            //
        }

        /**
         * Handle the banner "restored" event.
         *
         * @param  Banner  $banner
         */
        public function restored(Banner $banner)
        {
            //
        }

        /**
         * Handle the banner "force deleted" event.
         *
         * @param  Banner  $banner
         */
        public function forceDeleted(Banner $banner)
        {
            //
        }
    }
