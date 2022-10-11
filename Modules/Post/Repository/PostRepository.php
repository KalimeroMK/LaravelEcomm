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
     * @return mixed
     */
    public function findAll(): mixed
    {
        return $this->model::with('categories', 'comments', 'post_comments', 'post_tag', 'author_info')->get();
    }
    
    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function search(array $data): mixed
    {
        $query = $this->model::query();
        if (Arr::has($data, 'title')) {
            $query->where('title', 'like', '%' . Arr::get($data, 'title') . '%');
        }
        if (Arr::has($data, 'quote')) {
            $query->where('quote', 'like', '%' . Arr::get($data, 'quote') . '%');
        }
        if (Arr::has($data, 'summary')) {
            $query->where('summary', 'like', '%' . Arr::get($data, 'summary') . '%');
        }
        if (Arr::has($data, 'description')) {
            $query->where('description', 'like', '%' . Arr::get($data, 'description') . '%');
        }
        if (Arr::has($data, 'status')) {
            $query->where('status', 'like', '%' . Arr::get($data, 'status') . '%');
        }
        if (Arr::has($data, 'all_included') && (bool)Arr::get($data, 'all_included') === true) {
            return $query->get();
        }
        $query->orderBy(Arr::get($data, 'order_by') ?? 'id', Arr::get($data, 'sort') ?? 'desc');
        
        return $query->paginate(Arr::get($data, 'per_page') ?? (new $this->model)->getPerPage());
    }
}