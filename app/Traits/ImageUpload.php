<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Intervention\Image\ImageManagerStatic as Image;

trait ImageUpload
{
    /**
     * @param $image
     *
     * @return string
     */
    public function verifyAndStoreImage($image): string
    {
        $imageName = Str::random(15).'.'.$image->getClientOriginalExtension();
        $paths     = $this->makePaths();
        File::makeDirectory($paths->original, 0755, true, true);
        File::makeDirectory($paths->thumbnail, 0755, true, true);
        File::makeDirectory($paths->medium, 0755, true, true);
        $image->move($paths->original, $imageName);
        $find_image   = $paths->original.$imageName;
        $image_thumb  = Image::make($find_image)->resize(
            200,
            null,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        );
        $image_medium = Image::make($find_image)->resize(
            600,
            null,
            function ($constraint) {
                $constraint->aspectRatio();
            }
        );
        $image_thumb->save($paths->thumbnail.$imageName);
        $image_medium->save($paths->medium.$imageName);
        
        return $imageName;
    }
}
