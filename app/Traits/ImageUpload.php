<?php

    namespace App\Traits;

    use File;
    use Illuminate\Http\Request;
    use Illuminate\Support\Str;
    use Intervention\Image\ImageManagerStatic as Image;

    trait ImageUpload
    {
        /**
         * @param  Request  $request
         *
         * @return string
         */
        public function verifyAndStoreImage(Request $request): string
        {
            if ($request->hasFile('photo')) {
                $image     = $request->file('photo');
                $imageName = Str::random(15).'.'.$image->getClientOriginalExtension();
                $paths     = $this->makePaths();
                File::makeDirectory($paths->original, $mode = 0755, true, true);
                File::makeDirectory($paths->thumbnail, $mode = 0755, true, true);
                File::makeDirectory($paths->medium, $mode = 0755, true, true);
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

    }
