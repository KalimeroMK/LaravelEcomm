<?php

declare(strict_types=1);

namespace Modules\Product\Console\Commands;

use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Modules\Product\Models\Product;
use Modules\Product\Services\ElasticsearchService;

class ReindexProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'products:reindex {--force : Force reindex even if index exists}';

    /**
     * The console command description.
     */
    protected $description = 'Reindex all products in Elasticsearch';

    /**
     * Execute the console command.
     */
    public function handle(ElasticsearchService $elasticsearchService): int
    {
        $this->info('Starting product reindexing...');

        try {
            if ($this->option('force')) {
                $this->info('Force reindexing enabled - recreating index...');
                // Delete existing index
                $elasticsearchService->deleteIndex();
            }

            $this->info('Creating/updating Elasticsearch index...');
            $elasticsearchService->createIndex();

            $this->info('Indexing products...');

            $totalProducts = Product::count();
            $bar = $this->output->createProgressBar($totalProducts);
            $bar->start();

            Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])->chunk(100, function ($products) use ($elasticsearchService, $bar): void {
                foreach ($products as $product) {
                    try {
                        $elasticsearchService->indexProduct($product);
                        $bar->advance();
                    } catch (Exception $e) {
                        Log::error("Failed to index product {$product->id}: ".$e->getMessage());
                        $this->error("Failed to index product {$product->id}: ".$e->getMessage());
                    }
                }
            });

            $bar->finish();
            $this->newLine();

            $this->info('Product reindexing completed successfully!');
            $this->info("Total products indexed: {$totalProducts}");

            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Product reindexing failed: '.$e->getMessage());
            Log::error('Product reindexing failed: '.$e->getMessage());

            return Command::FAILURE;
        }
    }
}
