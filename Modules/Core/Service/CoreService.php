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
        $base_path = storage_path().'/uploads/images/';
        $original = $base_path;
        $thumbnail = $base_path.'thumbnails/';
        $medium = $base_path.'medium/';

        return (object)compact('original', 'thumbnail', 'medium');
    }
}