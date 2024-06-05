<?php

namespace Modules\Product\Service;

use Exception;
use Illuminate\Support\Collection;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
use Modules\Brand\Models\Brand;
use Modules\Category\Models\Category;
use Modules\Core\Helpers\Condition;
use Modules\Core\Service\CoreService;
use Modules\Core\Traits\ImageUpload;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;
use Modules\Size\Models\Size;
use Modules\Tag\Models\Tag;

class ProductService extends CoreService
{
    use ImageUpload;

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
     * @return Collection
     */
    public function search(array $data): Collection
    {
        return $this->product_repository->search($data);
    }

    /**
     * Store a newly created product.
     *
     * @param  array<string, mixed>  $data
     * @return Product
     * @throws Exception
     */
    public function store(array $data): Product
    {
        $this->handleColor($data);

        $product = $this->product_repository->create($data);

        if (isset($data['attributes'])) {
            $this->syncAttributes($product, $data['attributes']);
        }

        $product->categories()->attach($data['category']);

        if (isset($data['size'])) {
            $product->sizes()->attach($data['size']);
        }

        $product->tags()->attach($data['tag']);

        return $product;
    }

    /**
     * Get the data for editing a product.
     *
     * @param  int  $id
     * @return array<string, mixed>
     */
    public function edit(int $id): array
    {
        return [
            'brands' => Brand::get(),
            'categories' => Category::all(),
            'product' => $this->product_repository->findById($id),
            'sizes' => Size::get(),
            'conditions' => Condition::get(),
            'tags' => Tag::get(),
            'attributes' => Attribute::all()
        ];
    }

    /**
     * Update an existing product.
     *
     * @param  int  $id
     * @param  array<string, mixed>  $data
     * @return Product
     * @throws Exception
     */
    public function update(int $id, array $data): Product
    {
        $this->handleColor($data);

        if (isset($data['photo'])) {
            $data['photo'] = $this->verifyAndStoreImage($data['photo']);
        } else {
            $data['photo'] = Product::find($id)->photo;
        }

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
     * Delete a product.
     *
     * @param  int  $id
     * @return void
     */
    public function destroy(int $id): void
    {
        $this->product_repository->delete($id);
    }

    /**
     * Handle the color data for the product.
     *
     * @param  array<string, mixed>  $data
     * @return void
     */
    private function handleColor(array &$data): void
    {
        $color = $data['color'];
        if (is_array($color)) {
            $color = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        }
    }

    /**
     * Show a specific product.
     *
     * @param  int  $id
     * @return mixed
     */
    public function show(int $id): mixed
    {
        return $this->product_repository->findById($id);
    }

    /**
     * Sync attributes for the product.
     *
     * @param  Product  $product
     * @param  array<string, mixed>  $attributeData
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
