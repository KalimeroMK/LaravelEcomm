<?php

namespace Modules\Post\Repository;

use Illuminate\Support\Arr;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;
use Modules\Post\Models\Post;

class PostRepository extends Repository implements SearchInterface
{
    public $model = Post::class;

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function search(array $data): mixed
    {
        $query = $this->model::query();

        $searchable = [
            'title',
            'quote',
            'summary',
            'description',
            'status'
        ];

        foreach ($searchable as $key) {
            if (Arr::has($data, $key)) {
                $query->where($key, 'like', '%' . Arr::get($data, $key) . '%');
            }
        }

        if (Arr::get($data, 'all_included', false)) {
            return $query->with([
                'categories', 'comments', 'post_comments', 'post_tag', 'author_info'
            ])->get();
        }

        $orderBy = Arr::get($data, 'order_by', 'id');
        $sort = Arr::get($data, 'sort', 'desc');
        $perPage = Arr::get($data, 'per_page', (new $this->model)->getPerPage());

        return $query->with([
            'categories', 'comments', 'post_comments', 'post_tag', 'author_info'
        ])->orderBy($orderBy, $sort)->paginate($perPage);
    }

}
