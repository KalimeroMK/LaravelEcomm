<?php

namespace Modules\Post\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\Repository;
use Modules\Post\Models\Post;

class PostRepository extends Repository implements SearchInterface
{
    public $model = Post::class;

    private const LATEST_POSTS_LIMIT = 3;

    /**
     * Search posts based on given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): LengthAwarePaginator
    {
        $query = $this->buildQuery($data);

        if (Arr::get($data, 'all_included', false)) {
            return $this->eagerLoadRelations($query)->paginate();
        }

        return $this->applySortingAndPaginate($query, $data);
    }

    /**
     * Build query based on search data.
     *
     * @param  array<string, mixed>  $data
     */
    private function buildQuery(array $data): Builder
    {
        /** @var Builder<Post> $query */
        $query = $this->model::query();

        foreach ($this->searchableFields() as $key) {
            if (! empty($data[$key])) {
                $query->where($key, 'like', '%'.$data[$key].'%');
            }
        }

        return $query;
    }

    /**
     * Get searchable fields.
     *
     * @return array<int, string>
     */
    private function searchableFields(): array
    {
        return ['title', 'quote', 'summary', 'description', 'status'];
    }

    /**
     * Eager load relations for the query.
     *
     * @param  Builder<Post>  $query
     * @return Builder<Post>
     */
    private function eagerLoadRelations(Builder $query): Builder
    {
        return $query->with(['categories', 'comments', 'post_comments', 'tags', 'author', 'media']);
    }

    /**
     * Apply sorting and paginate the query results.
     *
     * @param  Builder<Post>  $query
     * @param  array<string, mixed>  $data
     */
    private function applySortingAndPaginate(Builder $query, array $data): LengthAwarePaginator
    {
        $orderBy = Arr::get($data, 'order_by', 'id');
        $sort = Arr::get($data, 'sort', 'desc');
        $perPage = Arr::get($data, 'per_page', (new Post())->getPerPage());

        return $this->eagerLoadRelations($query)->orderBy($orderBy, $sort)->paginate($perPage);
    }

    /**
     * Get active posts.
     *
     * @return Collection<int, Post>
     */
    public function getActivePosts(): Collection
    {
        return $this->model::where('status', 'active')
            ->orderBy('id', 'desc')
            ->limit(self::LATEST_POSTS_LIMIT)
            ->get();
    }

    /**
     * Find all posts.
     *
     * @return Collection<int, Post>
     */
    public function findAll(): Collection
    {
        /** @var Collection<int, Post> $posts */
        $posts = $this->eagerLoadRelations($this->model::query())->get();

        return $posts;
    }

    /**
     * Update an existing record in the repository.
     *
     * @param  int  $id  The ID of the model to update.
     * @param  array<string, mixed>  $data  The data to update in the model.
     * @return Model The updated model instance.
     */
    public function update(int $id, array $data): Model
    {
        $item = $this->findById($id);
        $item->categories()->sync($data['category'] ?? []);
        $item->tags()->sync($data['tags'] ?? []);
        $item->fill($data)->save();

        return $item->fresh();
    }
}
