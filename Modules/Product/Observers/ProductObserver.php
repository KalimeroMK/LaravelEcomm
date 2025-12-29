<?php

declare(strict_types=1);

namespace Modules\Product\Observers;

use Modules\Product\Models\Product;
use Modules\Product\Services\ElasticsearchService;

class ProductObserver
{
    protected ElasticsearchService $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        if ($product->status === 'active') {
            $this->elasticsearchService->indexProduct($product);
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        if ($product->status === 'active') {
            $this->elasticsearchService->indexProduct($product);
        } else {
            // If status changed to inactive, remove from index
            $this->elasticsearchService->deleteProduct($product->id);
        }
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        $this->elasticsearchService->deleteProduct($product->id);
    }
}
