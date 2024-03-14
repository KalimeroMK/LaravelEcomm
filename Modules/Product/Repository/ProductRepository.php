<?php

namespace Modules\Product\Repository;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Repositories\Repository;
use Modules\Product\Models\Product;

class ProductRepository extends Repository
{
    public $model = Product::class;


    const DEFAULT_ORDER_BY = 'id';
    const DEFAULT_SORT = 'desc';

    private const LATEST_PRODUCTS_LIMIT = 4;

    /**
     * @param  $id
     * @param  array  $data
     *
     * @return mixed
     */
    public function update($id, array $data): mixed
    {
        $item = $this->findById($id);
        $item->fill($data);
        $item->save();

        return $item->fresh();
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function findById($id): mixed
    {
        return $this->model::with('brand', 'categories', 'carts', 'condition', 'sizes', 'tags')->find($id);
    }

    protected function withRelations(): array
    {
        return ['brand', 'categories', 'carts', 'condition', 'sizes', 'tags', 'attributeValues.attribute'];
    }

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
                Arr::get($data, 'order_by') ?? 'id', // Assuming 'id' as the default order by
                Arr::get($data, 'sort') ?? 'desc' // Assuming 'desc' as the default sort
            );

            // Assuming withRelations() is a method that returns an array of relations to load
            return $query->with($this->withRelations())->paginate(
                Arr::get($data, 'per_page') ?? (new $this->model)->getPerPage()
            );
        });
    }


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
     * @return \Illuminate\Database\Eloquent\Collection
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
