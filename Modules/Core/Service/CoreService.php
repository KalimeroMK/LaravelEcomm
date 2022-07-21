<?php

namespace Modules\Core\Service;

class CoreService
{
    /**
     * Make paths for storing images.
     *
     * @return object
     */
    public function makePaths(): object
    {
        $original  = public_path().'/uploads/images/banner/';
        $thumbnail = public_path().'/uploads/images/banner/thumbnails/';
        $medium    = public_path().'/uploads/images/banner/medium/';
        
        return (object)compact('original', 'thumbnail', 'medium');
    }
}