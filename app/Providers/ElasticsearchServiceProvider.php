<?php

namespace App\Providers;

use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use Illuminate\Support\ServiceProvider;

class ElasticsearchServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function ($app) {
            $config = config('elasticsearch');

            $builder = ClientBuilder::create();

            // Set hosts
            $builder->setHosts($config['hosts']);

            // Set connection timeout
            $builder->setConnectionParams([
                'client' => [
                    'timeout' => $config['connection_timeout'],
                    'connect_timeout' => $config['connection_timeout'],
                ]
            ]);

            // Set request timeout
            $builder->setRequestTimeout($config['request_timeout']);

            // Set retry settings
            $builder->setRetries($config['max_retries']);

            // Enable logging if configured
            if ($config['logging']['enabled']) {
                $builder->setLogger(app('log')->driver());
            }

            return $builder->build();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Create indices if they don't exist
        if (app()->environment('local', 'staging')) {
            $this->createIndices();
        }
    }

    /**
     * Create Elasticsearch indices
     */
    protected function createIndices(): void
    {
        try {
            $client = app(Client::class);
            $config = config('elasticsearch');

            foreach ($config['indices'] as $indexName => $indexConfig) {
                $fullIndexName = $config['index_prefix'] . $indexConfig['name'];

                if (!$client->indices()->exists(['index' => $fullIndexName])) {
                    $client->indices()->create([
                        'index' => $fullIndexName,
                        'body' => [
                            'settings' => $indexConfig['settings'],
                            'mappings' => $this->getIndexMappings($indexName)
                        ]
                    ]);

                    info("Created Elasticsearch index: {$fullIndexName}");
                }
            }
        } catch (\Exception $e) {
            info("Failed to create Elasticsearch indices: " . $e->getMessage());
        }
    }

    /**
     * Get index mappings based on index type
     */
    protected function getIndexMappings(string $indexName): array
    {
        return match ($indexName) {
            'products' => [
                'properties' => [
                    'title' => ['type' => 'text', 'analyzer' => 'product_analyzer'],
                    'summary' => ['type' => 'text', 'analyzer' => 'product_analyzer'],
                    'description' => ['type' => 'text', 'analyzer' => 'product_analyzer'],
                    'sku' => ['type' => 'keyword'],
                    'price' => ['type' => 'float'],
                    'special_price' => ['type' => 'float'],
                    'brand' => ['type' => 'keyword'],
                    'categories' => ['type' => 'keyword'],
                    'tags' => ['type' => 'keyword'],
                    'attributes' => ['type' => 'nested'],
                    'status' => ['type' => 'keyword'],
                    'is_featured' => ['type' => 'boolean'],
                    'stock' => ['type' => 'integer'],
                    'created_at' => ['type' => 'date']
                ]
            ],
            default => []
        };
    }
}
