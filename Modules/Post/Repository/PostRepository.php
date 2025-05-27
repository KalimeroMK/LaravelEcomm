<?php

declare(strict_types=1);

namespace Modules\Post\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Post\Models\Post;

class PostRepository extends EloquentRepository implements EloquentRepositoryInterface, SearchInterface
{
    private const LATEST_POSTS_LIMIT = 3;

    public function __construct()
    {
        parent::__construct(Post::class);
    }

    /**
     * Search posts based on given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): LengthAwarePaginator
    {
        $query = $this->buildQuery($data);

        return $this->applySortingAndPaginate($query, $data);
    }

    /**
     * Find all posts with eager-loaded relations.
     *
     * @return Collection<int, Post>
     */
    public function findAll(): Collection
    {
        return $this->eagerLoadRelations((new $this->modelClass)->newQuery())->get();
    }

    /**
     * Update an existing post with relations.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): Model
    {
        $item = $this->findById($id);

        $item->categories()->sync($data['category'] ?? []);
        $item->tags()->sync($data['tags'] ?? []);
        $item->fill($data)->save();

        return $item->fresh();
    }

    /**
     * Build search query.
     *
     * @param  array<string, mixed>  $data
     */
    private function buildQuery(array $data): Builder
    {
        $query = (new $this->modelClass)->newQuery();

        foreach ($this->searchableFields() as $key) {
            if (! empty($data[$key])) {
                $query->where($key, 'like', '%'.$data[$key].'%');
            }
        }

        return $query;
    }

    /**
     * Define searchable fields.
     *
     * @return array<int, string>
     */
    private function searchableFields(): array
    {
        return ['title', 'quote', 'summary', 'description', 'status'];
    }

    /**
     * Apply eager loading of relationships.
     */
    private function eagerLoadRelations(Builder $query): Builder
    {
        return $query->with(['categories', 'comments', 'post_comments', 'tags', 'author', 'media']);
    }

    /**
     * Apply ordering and pagination.
     *
     * @param  array<string, mixed>  $data
     */
    private function applySortingAndPaginate(Builder $query, array $data): LengthAwarePaginator
    {
        $orderBy = Arr::get($data, 'order_by', 'id');
        $sort = Arr::get($data, 'sort', 'desc');
        $perPage = Arr::get($data, 'per_page', (new $this->modelClass)->getPerPage());

        return $this->eagerLoadRelations($query)->orderBy($orderBy, $sort)->paginate($perPage);
    }
}
