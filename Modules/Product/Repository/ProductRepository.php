<?php

declare(strict_types=1);

namespace Modules\Product\Repository;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Repositories\Repository;
use Modules\Product\Models\Product;

class ProductRepository extends Repository
{
    private const LATEST_PRODUCTS_LIMIT = 4;

    /**
     * The model that the repository works with.
     *
     * @var string
     */
    public $model = Product::class;

    /**
     * Search for products based on given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        $cacheKey = 'search_'.(json_encode($data));

        return Cache::store('redis')->remember($cacheKey, 86400, function () use ($data) {
            $query = $this->model::query();

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
                Arr::get($data, 'per_page', (new Product)->getPerPage())
            );
        });
    }

    /**
     * Get all products.
     */
    public function findAll(): Collection
    {
        return $this->model::with($this->withRelations())->get();
    }

    /**
     * Find a product by ID.
     */
    public function findById(int $id): ?Model
    {
        return $this->model::with(['brand', 'categories', 'carts', 'tags'])->find($id);
    }

    /**
     * Create a new product.
     */
    public function create(array $data): Model
    {
        $this->clearProductCache();

        return $this->model::create($data)->fresh();
    }

    /**
     * Update a product by ID.
     *
     * @param  array<string, mixed>  $data
     */
    public function update(int $id, array $data): Model
    {
        $item = $this->findById($id);
        $item->fill($data);
        $item->save();

        $this->clearProductCache();
        Cache::store('redis')->forget("product_{$id}");

        return $item->fresh();
    }

    /**
     * Get the relations to be loaded.
     *
     * @return array<int, string>
     */
    protected function withRelations(): array
    {
        return ['brand', 'categories', 'carts', 'condition', 'tags', 'attributeValues.attribute'];
    }

    /**
     * Clear relevant product caches.
     */
    private function clearProductCache(): void
    {
        Cache::store('redis')->forget('latest_products');
        Cache::store('redis')->forget('featured_products');
        Cache::store('redis')->forget('all_products');
    }
}
