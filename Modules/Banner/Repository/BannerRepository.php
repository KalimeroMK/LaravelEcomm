<?php

    namespace Modules\Banner\Repository;

    use App\Traits\ImageUpload;
    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;
    use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
    use Modules\Banner\Models\Banner;

    class BannerRepository
    {
        use ImageUpload;

        /**
         * @return LengthAwarePaginator|_IH_Banner_C|\Illuminate\Pagination\LengthAwarePaginator|array
         */
        public function getAll(): LengthAwarePaginator|_IH_Banner_C|\Illuminate\Pagination\LengthAwarePaginator|array
        {
            return Banner::orderBy('id', 'DESC')->paginate(10);
        }

        public function storeBanner($data, $image): Model|Banner
        {
            return Banner::create(
                $data + [
                    'photo' => $this->verifyAndStoreImage($image),
                ]
            );
        }

        public function getById($id): Model|Banner|Collection|_IH_Banner_C|array
        {
            return Banner::findOrFail($id);
        }

        public function updateBanner($data, $image, int $id): bool|int
        {
            return Banner::find($id)->update($data + [
                    'photo' => $this->verifyAndStoreImage($image),
                ]);
        }

        /**
         * @param  int  $id
         *
         * @return int
         */
        public function deleteBanner(int $id): int
        {
            return Banner::destroy($id);
        }

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
