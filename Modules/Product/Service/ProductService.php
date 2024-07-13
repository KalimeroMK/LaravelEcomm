<?php

namespace Modules\Product\Service;

use Exception;
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
     *
     * @param  array<string, mixed>  $data
     */
    public function index(): mixed
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
            $product->addMultipleMediaFromRequest(['images'])->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('product');
            });
        }

        $product->categories()->attach($data['category']);

        if (isset($data['size'])) {
            $product->sizes()->attach($data['size']);
        }

        if (isset($data['tag'])) {
            $product->tags()->attach($data['tag']);
        }

        return $product;
    }

    /**
     * Update an existing product.
     *
     * @param  array<string, mixed>  $data
     *
     * @throws Exception
     */
    public function update(int $id, array $data): Product
    {
        $this->handleColor($data);

        /** @var Product $product */
        $product = $this->product_repository->update($id, $data);

        if (request()->hasFile('images')) {
            $product->addMultipleMediaFromRequest(['images'])->each(function ($fileAdder) {
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

        // Handle attributes
        if (isset($data['attributes'])) {
            foreach ($data['attributes'] as $attributeCode => $attributeValue) {
                $attribute = Attribute::where('code', $attributeCode)->first();
                if ($attribute) {
                    $valueColumn = $attribute->getValueColumnName();
                    $product->attributes()->updateOrCreate(
                        ['attribute_id' => $attribute->id],
                        [$valueColumn => $attributeValue]
                    );
                }
            }
        }

        return $product;
    }

    /**
     * Handle the color data for the product.
     *
     * @param  array<string, mixed>  $data
     */
    private function handleColor(array $data): void
    {
        $color = $data['color'] ?? null;
        if (is_array($color)) {
            $color = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        }
    }
}
