<?php

namespace Modules\Product\Repository;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Repositories\Repository;
use Modules\Product\Models\Product;

class ProductRepository extends Repository
{
    /**
     * The model that the repository works with.
     *
     * @var string
     */
    public $model = Product::class;

    private const LATEST_PRODUCTS_LIMIT = 4;

    /**
     * Update a product by ID.
     *
     * @param  int  $id
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    public function update(int $id, array $data): mixed
    {
        $item = $this->findById($id);
        $item->fill($data);
        $item->save();

        return $item->fresh();
    }

    /**
     * Find a product by ID.
     *
     * @param  int  $id
     * @return mixed
     */
    public function findById(int $id): mixed
    {
        return $this->model::with('brand', 'categories', 'carts', 'condition', 'sizes', 'tags')->find($id);
    }

    /**
     * Get the relations to be loaded.
     *
     * @return array<int, string>
     */
    protected function withRelations(): array
    {
        return ['brand', 'categories', 'carts', 'condition', 'sizes', 'tags', 'attributeValues.attribute'];
    }

    /**
     * Search for products based on given data.
     *
     * @param  array<string, mixed>  $data
     * @return mixed
     */
    public function search(array $data): mixed
    {
        $cacheKey = 'search_'.md5(json_encode($data));

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
                'status'
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
                Arr::get($data, 'per_page', (new $this->model)->getPerPage())
            );
        });
    }

    /**
     * Get the latest products.
     *
     * @return Collection<int, Product>
     */
    public function getLatestProducts(): Collection
    {
        return Cache::remember('latest_products', 86400, function () {
            return $this->model::with('categories', 'condition')
                ->where('status', 'active')
                ->orderBy('id', 'desc')
                ->limit(self::LATEST_PRODUCTS_LIMIT)
                ->get();
        });
    }

    /**
     * Get the featured products.
     *
     * @return Collection<int, Product>
     */
    public function getFeaturedProducts(): Collection
    {
        return Cache::remember('featured_products', 86400, function () {
            return $this->model::with('categories')
                ->orderBy('price', 'desc')
                ->limit(4)
                ->get();
        });
    }
}
