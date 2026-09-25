<?php

declare(strict_types=1);

namespace Modules\Product\Observers;

use Modules\Product\Jobs\SyncProductToElasticsearch;
use Modules\Product\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        if ($product->status === 'active') {
            SyncProductToElasticsearch::dispatch($product->id)->afterCommit();
        }
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // The job re-checks status and indexes or removes accordingly.
        SyncProductToElasticsearch::dispatch($product->id)->afterCommit();
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        SyncProductToElasticsearch::dispatch($product->id, remove: true)->afterCommit();
    }
}
