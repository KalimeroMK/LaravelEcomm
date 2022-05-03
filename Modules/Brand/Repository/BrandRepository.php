<?php

    namespace Modules\Brand\Repository;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Pagination\LengthAwarePaginator;
    use Illuminate\Support\Collection;
    use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
    use LaravelIdea\Helper\Modules\Brand\Models\_IH_Brand_C;
    use Modules\Brand\Models\Brand;

    class BrandRepository
    {
        /**
         * @return _IH_Brand_C|\Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator|array
         */
        public function getAll(): _IH_Brand_C|\Illuminate\Contracts\Pagination\LengthAwarePaginator|LengthAwarePaginator|array
        {
            return Brand::orderBy('id', 'DESC')->paginate();
        }

        public function storeBrand($data): Model|Brand
        {
            return Brand::create($data);
        }

        public function getById($id): Model|Brand|Collection|_IH_Banner_C|array
        {
            return Brand::findOrFail($id);
        }

        /**
         * @param $data
         * @param  int  $id
         *
         * @return bool|int
         */
        public function updateBrand($data, int $id): bool|int
        {
            return Brand::find($id)->update($data);
        }

        /**
         * @param  int  $id
         *
         * @return int
         */
        public function deleteBrand(int $id): int
        {
            return Brand::destroy($id);
        }
    }
