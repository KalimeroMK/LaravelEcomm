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
        $original  = public_path().'/uploads/images/';
        $thumbnail = public_path().'/uploads/images/thumbnails/';
        $medium    = public_path().'/uploads/images/medium/';
        
        return (object)compact('original', 'thumbnail', 'medium');
    }
}