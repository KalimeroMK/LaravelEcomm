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
     * Find a post by slug with full relations.
     */
    public function findBySlug(string $slug): ?Post
    {
        return Post::with(['author', 'categories', 'tags', 'media'])
            ->whereSlug($slug)
            ->first();
    }

    /**
     * Get active paginated posts for the blog listing page.
     */
    public function getActivePaginated(int $perPage = 9): LengthAwarePaginator
    {
        return Post::with(['author', 'media'])
            ->whereStatus('active')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Get a small number of recent active posts (for sidebars).
     */
    public function getRecent(int $limit = 3): Collection
    {
        return Post::with(['author', 'media'])
            ->whereStatus('active')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get active posts filtered by category slug.
     */
    public function getByCategory(string $categorySlug, int $perPage = 10): LengthAwarePaginator
    {
        return Post::with(['author', 'media'])
            ->whereHas('categories', fn ($q) => $q->whereSlug($categorySlug))
            ->paginate($perPage);
    }

    /**
     * Get active posts filtered by tag slug.
     */
    public function getByTag(string $tagSlug, int $perPage = 10): LengthAwarePaginator
    {
        return Post::with(['author', 'tags', 'media'])
            ->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug))
            ->paginate($perPage);
    }

    /**
     * Search posts by title term (front-facing).
     */
    public function searchByTerm(string $term, int $perPage = 10): LengthAwarePaginator
    {
        return Post::with(['author', 'media'])
            ->whereStatus('active')
            ->where('title', 'like', "%{$term}%")
            ->paginate($perPage);
    }

    /**
     * Get active posts filtered by multiple conditions (category, search term).
     */
    public function filter(array $data, int $perPage = 10): LengthAwarePaginator
    {
        $query = Post::with(['author', 'media'])->whereStatus('active');

        if (! empty($data['search'])) {
            $query->where('title', 'like', '%'.$data['search'].'%');
        }

        if (! empty($data['category'])) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $data['category']));
        }

        return $query->paginate($perPage);
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
