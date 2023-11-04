<?php

namespace Modules\Product\Service;

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
        ];
    }

    public function store(array $data): Product
    {
        $this->handleColor($data);

        if (isset($data['photo'])) {
            $data['photo'] = $this->verifyAndStoreImage($data['photo']);
        }

        $product = $this->product_repository->create($data);
        $product->categories()->attach($data['category']);
        $product->sizes()->attach($data['size']);
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
        $product->categories()->sync($data['category']);
        $product->sizes()->sync($data['size']);
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
}
