<?php

declare(strict_types=1);

namespace Modules\Product\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\Product\Models\Product;
use Modules\Product\Services\ElasticsearchService;

/**
 * Keeps the Elasticsearch index in sync with a product without blocking the
 * request that saved it.
 */
class SyncProductToElasticsearch implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        public readonly int $productId,
        public readonly bool $remove = false,
    ) {}

    public function handle(ElasticsearchService $elasticsearchService): void
    {
        if ($this->remove) {
            $elasticsearchService->deleteProduct($this->productId);

            return;
        }

        $product = Product::find($this->productId);

        if ($product instanceof Product && $product->status === 'active') {
            $elasticsearchService->indexProduct($product);
        } else {
            // Deleted or deactivated since the job was queued.
            $elasticsearchService->deleteProduct($this->productId);
        }
    }
}
