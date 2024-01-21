<?php

namespace Modules\Core\Traits;

use Exception;
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
        $imageName = $this->getUniqueImageName($image);
        $paths = $this->makePaths();

        $this->ensureDirectoriesExist($paths);
        $this->moveImageToFolder($image, $paths->original, $imageName);

        $find_image = $paths->original.$imageName;
        $this->resizeImage($find_image, 200, $paths->thumbnail.$imageName);
        $this->resizeImage($find_image, 600, $paths->medium.$imageName);
        return $imageName;
    }

    public function getUniqueImageName($image): string
    {
        return Str::random(15).'.'.$image->getClientOriginalExtension();
    }

    public function ensureDirectoriesExist($paths): void
    {
        File::makeDirectory($paths->original, 0755, true, true);
        File::makeDirectory($paths->thumbnail, 0755, true, true);
        File::makeDirectory($paths->medium, 0755, true, true);
    }

    public function moveImageToFolder($image, $directory, $imageName): void
    {
        $image->move($directory, $imageName);
    }

    /**
     * @throws Exception
     */
    public function resizeImage($find_image, $size, $destination): void
    {
        error_log("Starting resizeImage function");
        try {
            error_log("Creating image from: ".$find_image);
            $resized_image = Image::make($find_image)->resize($size, null, function ($constraint) {
                $constraint->aspectRatio();
                error_log("Resizing image");
            });
            error_log("Saving image to: ".$destination);
            $resized_image->save($destination);
        } catch (Exception $e) {
            error_log('Intervention Image Error: '.$e->getMessage());
            throw $e;
        }
    }
}
