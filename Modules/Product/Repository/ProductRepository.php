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
        $cacheKey = 'search_'.md5(json_encode($data));

        return Cache::store('redis')->remember($cacheKey, 86400, function () use ($data) {
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
        Cache::store('redis')->forget("product_{$id}");

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
     * Clear product-related Redis cache keys.
     */
    private function clearProductCache(): void
    {
        Cache::store('redis')->forget('latest_products');
        Cache::store('redis')->forget('featured_products');
        Cache::store('redis')->forget('all_products');
    }
}
