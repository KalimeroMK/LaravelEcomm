<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Exception;
use Modules\Product\Repository\ProductRepository;

readonly class DeleteProductMediaAction
{
    public function __construct(private ProductRepository $repository) {}

    public function execute(int $modelId, int $mediaId): void
    {
        $product = $this->repository->findById($modelId);

        if (! $product) {
            throw new Exception("Product not found with ID: {$modelId}");
        }

        $media = $product->media()->where('id', $mediaId)->first();

        if ($media) {
            $media->delete();
        }
    }
}
