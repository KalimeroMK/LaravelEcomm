<?php

declare(strict_types=1);

namespace Modules\Product\Console;

use Illuminate\Console\Command;
use Modules\Product\Services\ElasticsearchService;
use Symfony\Component\Console\Input\InputOption;

class IndexProducts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'product:index {--fresh : Delete existing index and recreate}';

    /**
     * The console command description.
     */
    protected $description = 'Index all products into Elasticsearch';

    /**
     * Execute the console command.
     */
    public function handle(ElasticsearchService $elasticsearchService): void
    {
        $this->info('Starting product indexing...');

        if ($this->option('fresh')) {
            $this->warn('Fresh indexing requested. Deleting existing index...');
            $elasticsearchService->deleteIndex();
        }

        // Ensure index exists
        $elasticsearchService->createIndex();

        $this->info('Indexing products...');
        
        $startTime = microtime(true);
        $elasticsearchService->reindexAll();
        $duration = round(microtime(true) - $startTime, 2);

        $this->info("Products successfully indexed in {$duration} seconds.");
    }
}
