<?php

declare(strict_types=1);

namespace Modules\Product\Repository;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Interfaces\EloquentRepositoryInterface;
use Modules\Core\Interfaces\SearchInterface;
use Modules\Core\Repositories\EloquentRepository;
use Modules\Product\Models\Product;

class ProductRepository extends EloquentRepository implements EloquentRepositoryInterface, SearchInterface
{
    public function __construct()
    {
        parent::__construct(Product::class);
    }

    /**
     * Search products — admin-facing, NO caching (admin always needs fresh data).
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        $query = (new $this->modelClass)->newQuery();

        $searchableFields = ['title', 'summary', 'description', 'color', 'stock', 'brand_id', 'price', 'discount', 'status'];

        foreach ($searchableFields as $field) {
            if (Arr::has($data, $field)) {
                $query->where($field, 'like', '%'.Arr::get($data, $field).'%');
            }
        }

        $query->orderBy(Arr::get($data, 'order_by', 'id'), Arr::get($data, 'sort', 'desc'));

        return $query->with($this->withRelations())->paginate(
            Arr::get($data, 'per_page', (new $this->modelClass)->getPerPage())
        );
    }

    /**
     * Get all products with eager-loaded relations.
     */
    public function findAll(): Collection
    {
        return (new $this->modelClass)->with($this->withRelations())->get();
    }

    /**
     * Find a product by ID with relations.
     */
    public function findById(int $id): ?Model
    {
        return (new $this->modelClass)->with($this->withRelations())->find($id);
    }

    /**
     * Find a product by slug with full relations for the detail page.
     */
    public function findBySlug(string $slug): ?Product
    {
        return Product::with(['getReview', 'categories', 'attributeValues.attribute', 'brand', 'tags', 'media'])
            ->whereSlug($slug)
            ->first();
    }

    /**
     * Get featured active products.
     */
    public function getFeatured(int $limit = 4): Collection
    {
        return Product::with(['categories', 'brand', 'tags', 'media'])
            ->where('status', 'active')
            ->where('is_featured', true)
            ->orderByDesc('price')
            ->limit($limit)
            ->get();
    }

    /**
     * Get latest active products, optionally with offset.
     */
    public function getLatest(int $limit = 4, int $offset = 0): Collection
    {
        return Product::with(['categories', 'brand', 'tags', 'media'])
            ->where('status', 'active')
            ->orderByDesc('id')
            ->offset($offset)
            ->limit($limit)
            ->get();
    }

    /**
     * Get a small set of recent active products for sidebars.
     */
    public function getRecent(int $limit = 3): Collection
    {
        return Product::with(['categories', 'brand', 'tags', 'attributeValues.attribute', 'media'])
            ->where('status', 'active')
            ->whereNull('parent_id')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    /**
     * Get products related to a product via shared category IDs.
     * Uses category IDs (already loaded) — no extra query for names.
     *
     * @param  array<int>  $categoryIds
     */
    public function getRelatedByCategoryIds(array $categoryIds, int $excludeId, int $limit = 8): Collection
    {
        return Product::with(['categories', 'brand', 'tags', 'attributeValues.attribute', 'media'])
            ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
            ->where('id', '!=', $excludeId)
            ->where('status', 'active')
            ->limit($limit)
            ->get();
    }

    /**
     * Get deal products (d_deal = true).
     */
    public function getDeals(int $perPage = 9): LengthAwarePaginator
    {
        return Product::with(['categories', 'brand', 'media'])
            ->where('d_deal', true)
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Search products by text term for front-facing search.
     */
    public function searchByTerm(string $term, int $perPage = 9): LengthAwarePaginator
    {
        return Product::with(['categories', 'brand', 'tags', 'attributeValues.attribute', 'media'])
            ->where('status', 'active')
            ->where(fn ($q) => $q
                ->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
            )
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    /**
     * Get products by brand slug.
     */
    public function getByBrand(string $brandSlug, int $perPage = 9): LengthAwarePaginator
    {
        return Product::with(['categories', 'brand', 'tags', 'attributeValues.attribute', 'media'])
            ->whereHas('brand', fn ($q) => $q->where('slug', $brandSlug))
            ->paginate($perPage);
    }

    /**
     * Get the maximum active product price (for price-range slider).
     */
    public function getMaxPrice(): float
    {
        return (float) (Product::where('status', 'active')->max('price') ?? 1000);
    }

    /**
     * Create a new product and clear relevant caches.
     */
    public function create(array $data): Model
    {
        $this->clearProductCache();

        /** @var class-string<Model> $model */
        $model = $this->modelClass;

        return $model::create($data)->fresh();
    }

    /**
     * Update a product by ID and refresh related caches.
     */
    public function update(int $id, array $data): Model
    {
        $item = $this->findById($id);
        $item->fill($data)->save();

        $this->clearProductCache();
        Cache::forget("product_{$id}");

        return $item->fresh();
    }

    /**
     * Relations to eager load with product (admin / full detail).
     *
     * @return array<int, string>
     */
    protected function withRelations(): array
    {
        return ['brand', 'categories', 'carts', 'tags', 'attributeValues.attribute'];
    }

    /**
     * Clear product-related cache keys.
     */
    private function clearProductCache(): void
    {
        // Bump generation so all existing search_* cache keys become orphaned.
        Cache::increment('product_search_generation');

        $keys = [
            'featured_products',
            'latest_products',
            'hot_products',
            'all_products',
            'active_banners_with_categories',
            'recent_products',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
