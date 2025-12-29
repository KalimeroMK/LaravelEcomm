<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Illuminate\Database\Eloquent\Model;
use Modules\Core\Support\Media\MediaUploader;
use Modules\Core\Support\Relations\SyncRelations;
use Modules\Product\DTOs\ProductDTO;
use Modules\Product\Repository\ProductRepository;

readonly class UpdateProductAction
{
    public function __construct(private ProductRepository $repository) {}

    public function execute(int $id, ProductDTO $dto): Model
    {
        $product = $this->repository->update($id, [
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
            'sku' => $dto->sku,
            'special_price' => $dto->special_price,
            'special_price_start' => $dto->special_price_start,
            'special_price_end' => $dto->special_price_end,
        ]);

        // Sync relations
        SyncRelations::execute($product, [
            'categories' => $dto->categories,
            'tags' => $dto->tags,
            'brand' => $dto->brand_id,
        ]);

        // Sync product attributes
        SyncProductAttributesAction::execute($product, $dto->attributes ?? []);

        // Upload media
        MediaUploader::uploadMultiple($product, ['images'], 'product');

        return $product;
    }
}
