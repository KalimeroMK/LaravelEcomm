<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

readonly class StoreProductAction
{
    public function __construct(private ProductRepository $repository) {}

    public function execute(ProductDTO $dto): Product
    {
        return $this->repository->create([
            'title' => $dto->title,
            'slug' => $dto->slug,
            'summary' => $dto->summary,
            'description' => $dto->description,
            'stock' => $dto->stock,
            'status' => $dto->status,
            'price' => $dto->price,
            'discount' => $dto->discount,
            'is_featured' => $dto->is_featured,
            'd_deal' => $dto->d_deal,
            'brand_id' => $dto->brand_id,
            'attribute_set_id' => $dto->attribute_set_id,
            'sku' => $dto->sku,
            'special_price' => $dto->special_price,
            'special_price_start' => $dto->special_price_start,
            'special_price_end' => $dto->special_price_end,
        ]);
    }
}
