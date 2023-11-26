<?php

namespace Modules\Product\Service;

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
    public ProductRepository $product_repository;

    public function __construct(ProductRepository $product_repository)
    {
        $this->product_repository = $product_repository;
    }

    use ImageUpload;

    public function getAll($data): mixed
    {
        return $this->product_repository->search($data);
    }

    public function create(): array
    {
        return [
            'brands' => Brand::get(),
            'categories' => Category::get(),
            'product' => new Product(),
            'sizes' => Size::get(),
            'conditions' => Condition::get(),
            'tags' => Tag::get(),
            'attributes' => Attribute::all()
        ];
    }

    public function store(array $data): Product
    {
        $this->handleColor($data);

        if (isset($data['photo'])) {
            $data['photo'] = $this->verifyAndStoreImage($data['photo']);
        }
        $product = $this->product_repository->create($data);
        $this->syncAttributes($product, $data['attributes']); // Assuming 'attributes' is the input name
        $product->categories()->attach($data['category']);
        if (isset($data['size'])) {
            $product->sizes()->attach($data['size']);
        }
        $product->tags()->attach($data['tag']);

        return $product;
    }

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

    public function update(int $id, array $data): Product
    {
        $this->handleColor($data);

        if (isset($data['photo'])) {
            $data['photo'] = $this->verifyAndStoreImage($data['photo']);
        } else {
            $data['photo'] = Product::find($id)->photo;
        }

        $product = $this->product_repository->update($id, $data);
        $this->syncAttributes($product, $data['attributes']);
        $product->categories()->sync($data['category']);
        if (isset($data['size'])) {
            $product->sizes()->sync($data['size']);
        }
        $product->tags()->sync($data['tag']);

        return $product;
    }


    public function destroy(int $id): void
    {
        $this->product_repository->delete($id);
    }

    private function handleColor(array &$data): void
    {
        $color = $data['color'];
        if (is_array($color)) {
            $color = preg_replace('/\s+/', '', $color);
            $data['color'] = implode(',', $color);
        }
    }

    public function show(int $id)
    {
        return $this->product_repository->findById($id);
    }

    private function syncAttributes(Product $product, array $attributeData): void
    {
        foreach ($attributeData as $attributeId => $value) {
            $attribute = Attribute::findOrFail($attributeId);
            $valueColumn = $attribute->getValueColumnName(
            ); // Make sure this method exists and returns the correct column name

            $attributeValue = AttributeValue::firstOrCreate([
                'attribute_id' => $attributeId,
                $valueColumn => $value,
            ]);

            $product->attributeValues()->syncWithoutDetaching([$attributeValue->id]);
        }
    }

}
