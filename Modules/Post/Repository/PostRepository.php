<?php

namespace Modules\Post\Repository;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;
use Modules\Post\Models\Post;

class PostRepository extends Repository implements SearchInterface
{
    public $model = Post::class;
    private const LATEST_POSTS_LIMIT = 3;

    /**
     * @param  array  $data
     *
     * @return mixed
     */
    public function search(array $data): LengthAwarePaginator
    {
        $query = $this->buildQuery($data);

        if (Arr::get($data, 'all_included', false)) {
            return $this->eagerLoadRelations($query)->get();
        }

        return $this->applySortingAndPaginate($query, $data);
    }

    private function buildQuery(array $data): Builder
    {
        $query = $this->model::query();

        foreach ($this->searchableFields() as $key) {
            if (!empty($data[$key])) {
                $query->where($key, 'like', '%'.$data[$key].'%');
            }
        }

        return $query;
    }

    private function searchableFields(): array
    {
        return ['title', 'quote', 'summary', 'description', 'status'];
    }

    private function eagerLoadRelations(Builder $query): Builder
    {
        return $query->with(['categories', 'comments', 'post_comments', 'tags', 'author_info', 'media']);
    }

    private function applySortingAndPaginate(Builder $query, array $data): LengthAwarePaginator
    {
        $orderBy = Arr::get($data, 'order_by', $this->model::DEFAULT_ORDER_BY);
        $sort = Arr::get($data, 'sort', $this->model::DEFAULT_SORT);
        $perPage = Arr::get($data, 'per_page', (new $this->model)->getPerPage());

        return $this->eagerLoadRelations($query)->orderBy($orderBy, $sort)->paginate($perPage);
    }


    public function getActivePosts()
    {
        return $this->model::where('status', 'active')
            ->orderBy('id', 'desc')
            ->limit(self::LATEST_POSTS_LIMIT)
            ->get();
    }

    /**
     * @return Collection
     */
    public function findAll(): Collection
    {
        return $this->eagerLoadRelations($this->model::query())->get();
    }

}
