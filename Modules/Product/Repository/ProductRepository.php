<?php

declare(strict_types=1);

namespace Modules\Product\Repository;

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
     * Search for products based on given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        // Generation counter increments on every product create/update/delete,
        // so stale search results are invalidated immediately instead of waiting 24 h.
        $generation = (int) Cache::get('product_search_generation', 0);
        $cacheKey = 'search_'.$generation.'_'.md5(json_encode($data));

        return Cache::remember($cacheKey, 3600, function () use ($data) {
            $query = (new $this->modelClass)->newQuery();

            $searchableFields = [
                'title',
                'summary',
                'description',
                'color',
                'stock',
                'brand_id',
                'price',
                'discount',
                'status',
            ];

            foreach ($searchableFields as $field) {
                if (Arr::has($data, $field)) {
                    $query->where($field, 'like', '%'.Arr::get($data, $field).'%');
                }
            }

            $query->orderBy(
                Arr::get($data, 'order_by', 'id'),
                Arr::get($data, 'sort', 'desc')
            );

            return $query->with($this->withRelations())->paginate(
                Arr::get($data, 'per_page', (new $this->modelClass)->getPerPage())
            );
        });
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
     * Relations to eager load with product.
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
        // Bump generation so all existing search_* cache keys become orphaned
        // and new searches build fresh results immediately.
        Cache::increment('product_search_generation');

        $keys = [
            'latest_products',
            'featured_products',
            'all_products',
            'hot_products',
            'active_banners_with_categories',
        ];

        foreach ($keys as $key) {
            Cache::forget($key);
        }
    }
}
