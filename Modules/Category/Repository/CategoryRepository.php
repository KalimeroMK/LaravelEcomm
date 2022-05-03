<?php

    namespace Modules\Category\Repository;

    use Illuminate\Contracts\Pagination\LengthAwarePaginator;
    use Illuminate\Database\Eloquent\Collection;
    use Illuminate\Database\Eloquent\Model;
    use LaravelIdea\Helper\Modules\Banner\Models\_IH_Banner_C;
    use Modules\Banner\Models\Banner;
    use Modules\Category\Models\Category;

    class CategoryRepository
    {
        /**
         * @return LengthAwarePaginator|_IH_Banner_C|\Illuminate\Pagination\LengthAwarePaginator|array
         */
        public function getAll(): LengthAwarePaginator|_IH_Banner_C|\Illuminate\Pagination\LengthAwarePaginator|array
        {
            return Category::orderBy('id', 'DESC')->paginate(10);
        }

        /**
         * @param $title
         * @param $parent_id
         *
         * @return Model|Banner
         */
        public function storeCategory($title, $parent_id = null): Model|Banner
        {
            return Category::create(["title" => $title, "parent_id" => $parent_id]);
        }

        /**
         * @param $id
         *
         * @return Model|Banner|Collection|_IH_Banner_C|array
         */
        public function getById($id): Model|Banner|Collection|_IH_Banner_C|array
        {
            return Category::findOrFail($id);
        }

        /**
         * @param $title
         * @param $parent_id
         * @param  int  $id
         *
         * @return bool|int
         */
        public function updateCategory($title, $parent_id, int $id,): bool|int
        {
            return Category::find($id)->update(["title" => $title, "parent_id" => $parent_id]);
        }

        /**
         * @param  int  $id
         *
         * @return int
         */
        public function deleteCategory(int $id): int
        {
            return Category::destroy($id);
        }
    }
