<?php

declare(strict_types=1);

namespace Modules\Product\Service;

use Exception;
use Illuminate\Support\Collection;
use Modules\Attribute\Models\Attribute;
use Modules\Core\Service\CoreService;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

class ProductService extends CoreService
{
    public ProductRepository $product_repository;

    public function __construct(ProductRepository $product_repository)
    {
        parent::__construct($product_repository);
        $this->product_repository = $product_repository;
    }

    /**
     * Get all products based on given data.
     */
    public function index(): Collection
    {
        return $this->product_repository->findAll();
    }

    /**
     * Get all products based on given data.
     *
     * @param  array<string, mixed>  $data
     */
    public function search(array $data): mixed
    {
        return $this->product_repository->search($data);
    }

    /**
     * Store a newly created product.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws Exception
     */
    public function store(array $data): Product
    {
        $this->handleColor($data);

        /** @var Product $product */
        $product = $this->product_repository->create($data);

        if (request()->hasFile('images')) {
            $product->addMultipleMediaFromRequest(['images'])->each(function ($fileAdder): void {
                $fileAdder->preservingOriginal()->toMediaCollection('product');
            });
        }

        $product->categories()->attach($data['category']);

        if (isset($data['tag'])) {
            $product->tags()->attach($data['tag']);
        }

        $this->syncAttributes($product, $data);

        return $product;
    }

    /**
     * Update an existing product with relations and media.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws Exception
     */
    public function updateWithRelationsAndMedia(int $id, array $data): Product
    {
        $this->handleColor($data);

        /** @var Product $product */
        $product = $this->product_repository->update($id, $data);

        if (request()->hasFile('images')) {
            $product->addMultipleMediaFromRequest(['images'])->each(function ($fileAdder): void {
                $fileAdder->preservingOriginal()->toMediaCollection('product');
            });
        }

        $product->categories()->sync($data['category']);

        if (isset($data['size'])) {
            $product->sizes()->sync($data['size']);
        }

        if (isset($data['tag'])) {
            $product->tags()->sync($data['tag']);
        }

        $this->syncAttributes($product, $data);

        return $product;
    }

    /**
     * Sync attribute values to the product.
     */
    private function syncAttributes(Product $product, array $data): void
    {
        if (! isset($data['attributes']) || ! is_array($data['attributes'])) {
            return;
        }

        foreach ($data['attributes'] as $code => $value) {
            if ($value === '__custom__') {
                $value = $data['attributes_custom'][$code] ?? null;
            }

            if ($value !== null) {
                $attribute = Attribute::where('code', $code)->first();
                if ($attribute) {
                    $column = $attribute->getValueColumnName();
                    $product->attributes()->updateOrCreate(
                        ['attribute_id' => $attribute->id],
                        [$column => $value]
                    );
                }
            }
        }
    }

    /**
     * Handle the color data for the product.
     *
     * @param  array<string, mixed>  $data
     */
    private function handleColor(array &$data): void
    {
        $color = $data['color'] ?? null;
        if (is_array($color)) {
            $color = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        }
    }
}
