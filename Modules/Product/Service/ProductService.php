<?php

namespace Modules\Product\Service;

use Exception;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
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
     * @param array<string, mixed> $data
     * @return mixed
     */
    public function search(array $data): mixed
    {
        return $this->product_repository->search($data);
    }

    /**
     * Store a newly created product.
     *
     * @param array<string, mixed> $data
     * @return Product
     * @throws Exception
     */
    public function store(array $data): Product
    {
        $this->handleColor($data);

        /** @var Product $product */
        $product = $this->product_repository->create($data);

        if (isset($data['attributes'])) {
            $this->syncAttributes($product, $data['attributes']);
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
     * @param int $id
     * @param array<string, mixed> $data
     * @return Product
     * @throws Exception
     */
    public function update(int $id, array $data): Product
    {
        $this->handleColor($data);

        /** @var Product $product */
        $product = $this->product_repository->update($id, $data);

        if (isset($data['attributes'])) {
            $this->syncAttributes($product, $data['attributes']);
        }

        $product->categories()->sync($data['category']);

        if (isset($data['size'])) {
            $product->sizes()->sync($data['size']);
        }

        if (isset($data['tag'])) {
            $product->tags()->sync($data['tag']);
        }

        return $product;
    }

    /**
     * Handle the color data for the product.
     *
     * @param array<string, mixed> $data
     * @return void
     */
    private function handleColor(array &$data): void
    {
        $color = $data['color'] ?? null;
        if (is_array($color)) {
            $color = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        }
    }

    /**
     * Sync attributes for the product.
     *
     * @param Product $product
     * @param array<string, mixed> $attributeData
     * @return void
     * @throws Exception
     */
    private function syncAttributes(Product $product, array $attributeData): void
    {
        foreach ($attributeData as $attributeId => $value) {
            $attribute = Attribute::findOrFail($attributeId);
            $valueColumn = $attribute->getValueColumnName();

            $attributeValue = AttributeValue::firstOrCreate([
                'attribute_id' => $attributeId,
                $valueColumn => $value,
            ]);

            $product->attributeValues()->syncWithoutDetaching([$attributeValue->id]);
        }
    }
}
