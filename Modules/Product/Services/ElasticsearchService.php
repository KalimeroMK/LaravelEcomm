<?php

declare(strict_types=1);

namespace Modules\Product\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\ClientInterface;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Modules\Product\Models\Product;
use stdClass;

class ElasticsearchService
{
    protected ClientInterface $elasticsearch;

    protected string $index;

    public function __construct()
    {
        $config = config('elasticsearch');
        $hosts = $config['hosts'] ?? [];
        $this->index = $config['index'] ?? 'products';

        $formattedHosts = array_map(function ($host) {
            $scheme = $host['scheme'] ?? 'http';
            $address = $host['host'] ?? 'localhost';
            $port = $host['port'] ?? 9200;
            $user = $host['user'] ?? null;
            $pass = $host['pass'] ?? null;

            $url = "{$scheme}://{$address}:{$port}";

            if ($user && $pass) {
                $url = "{$scheme}://{$user}:{$pass}@{$address}:{$port}";
            }

            return $url;

        }, $hosts);

        $this->elasticsearch = ClientBuilder::create()
            ->setHosts($formattedHosts)
            ->build();
    }

    /**
     * Index a product in Elasticsearch
     */
    public function indexProduct(Product $product): void
    {
        // Ensure relationships are loaded if not already
        if (! $product->relationLoaded('categories')) {
            $product->load('categories');
        }
        if (! $product->relationLoaded('brand')) {
            $product->load('brand');
        }
        if (! $product->relationLoaded('tags')) {
            $product->load('tags');
        }
        if (! $product->relationLoaded('attributeValues')) {
            $product->load('attributeValues.attribute');
        }

        $params = [
            'index' => $this->index,
            'id' => (string) $product->id, // ES IDs should be strings
            'body' => [
                'title' => $product->title,
                'summary' => $product->summary,
                'description' => $product->description,
                'sku' => $product->sku,
                'price' => (float) $product->price,
                'special_price' => $product->special_price ? (float) $product->special_price : null,
                'brand' => $product->brand?->title,
                'categories' => $product->categories->pluck('id')->toArray(), // Use IDs for filtering
                'category_names' => $product->categories->pluck('name')->toArray(), // Use names for display/search
                'tags' => $product->tags->pluck('name')->toArray(),
                'attributes' => $product->attributeValues->map(function ($attrValue) {
                    $attribute = $attrValue->attribute;
                    $valueColumn = $attribute->getValueColumnName();
                    $value = $valueColumn ? $attrValue->{$valueColumn} : null;

                    return [
                        'name' => $attribute->name,
                        'value' => $value,
                    ];
                })->filter(fn ($attr) => $attr['value'] !== null)->toArray(),
                'status' => $product->status,
                'is_featured' => (bool) $product->is_featured,
                'stock' => (int) $product->stock,
                'created_at' => $product->created_at?->toIso8601String(),
            ],
        ];

        try {
            $this->elasticsearch->index($params);
        } catch (Exception $e) {
            Log::error("Elasticsearch indexing failed for product {$product->id}: ".$e->getMessage());
        }
    }

    /**
     * Advanced search with filters
     * Returns Collection on success, null on failure (fallback to SQL needed)
     */
    public function search(string $query, array $filters = []): ?Collection
    {
        $searchParams = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [],
                        'filter' => [],
                    ],
                ],
                // Default sort
                'sort' => $filters['sort_by'] ?? [
                    '_score' => ['order' => 'desc'],
                    'created_at' => ['order' => 'desc'],
                ],
                'size' => 50,
            ],
        ];

        // 1. Full text search
        if (! empty($query)) {
            $searchParams['body']['query']['bool']['must'][] = [
                'multi_match' => [
                    'query' => $query,
                    'fields' => ['title^3', 'summary^2', 'description', 'sku', 'brand', 'tags', 'category_names'],
                    'type' => 'best_fields',
                    'fuzziness' => 'AUTO',
                ],
            ];
        } else {
            // If no query, match all (for browsing)
            $searchParams['body']['query']['bool']['must'][] = ['match_all' => new stdClass()];
        }

        // 2. Filters

        // Price Range
        if (! empty($filters['price_min']) || ! empty($filters['price_max'])) {
            $range = ['price' => []];
            if (! empty($filters['price_min'])) {
                $range['price']['gte'] = (float) $filters['price_min'];
            }
            if (! empty($filters['price_max'])) {
                $range['price']['lte'] = (float) $filters['price_max'];
            }
            $searchParams['body']['query']['bool']['filter'][] = ['range' => $range];
        }

        // Brand
        if (! empty($filters['brand'])) {
            // Term query uses exact match, suitable for keyword fields.
            // Ensure mapping treats brand as keyword or use match query on text field.
            // For simplicity, we'll try match phrase or assumes standard analyzer.
            $searchParams['body']['query']['bool']['filter'][] = [
                'match_phrase' => ['brand' => $filters['brand']],
            ];
        }

        // Categories
        if (! empty($filters['categories'])) {
            // Assuming categories filter provides IDs
            // If array
            if (is_array($filters['categories'])) {
                $searchParams['body']['query']['bool']['filter'][] = [
                    'terms' => ['categories' => $filters['categories']],
                ];
            } else {
                $searchParams['body']['query']['bool']['filter'][] = [
                    'term' => ['categories' => $filters['categories']],
                ];
            }
        }

        // Status
        $status = $filters['status'] ?? 'active'; // Default to active if not specified
        if ($status) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'term' => ['status' => $status],
            ];
        }

        // In Stock
        if (! empty($filters['in_stock'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['stock' => ['gt' => 0]],
            ];
        }

        try {
            $response = $this->elasticsearch->search($searchParams);
            $hits = $response['hits']['hits'];

            // Hydrate simple objects or fetch models from DB.
            // For performance, hydration from ES result is best, but to keep consistent with Eloquent:
            $ids = array_column($hits, '_id');

            if (empty($ids)) {
                return collect([]);
            }

            // Fetch models to ensure they have all methods/accessors available to view
            // Using whereIn preserves order only if we explicitly sort collection or use mysql ORDER BY FIELD
            // Eager load relationships to prevent N+1 queries
            $products = Product::with(['categories', 'brand', 'tags', 'attributeValues.attribute'])
                ->whereIn('id', $ids)
                ->get();

            // Re-sort collection based on ES hits order (relevance)
            $sortedProducts = $products->sortBy(function ($model) use ($ids) {
                return array_search($model->id, $ids);
            });

            return $sortedProducts->values();

        } catch (Exception $e) {
            Log::error('Elasticsearch search failed: '.$e->getMessage());

            // Return null to signal fallback needed (controller will handle SQL fallback)
            return null;
        }
    }

    /**
     * Create the Elasticsearch index with mappings
     */
    public function createIndex(): void
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'default' => [
                                'type' => 'standard',
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'title' => ['type' => 'text'],
                        'summary' => ['type' => 'text'],
                        'description' => ['type' => 'text'],
                        'sku' => ['type' => 'keyword'],
                        'price' => ['type' => 'float'],
                        'brand' => ['type' => 'text', 'fields' => ['keyword' => ['type' => 'keyword']]],
                        'categories' => ['type' => 'integer'], // IDs
                        'category_names' => ['type' => 'text'],
                        'tags' => ['type' => 'text'],
                        'status' => ['type' => 'keyword'],
                        'stock' => ['type' => 'integer'],
                        'created_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ];

        try {
            if (! $this->elasticsearch->indices()->exists(['index' => $this->index])) {
                $this->elasticsearch->indices()->create($params);
            }
        } catch (Exception $e) {
            Log::error('Failed to create index: '.$e->getMessage());
        }
    }

    /**
     * Delete a product from Elasticsearch
     */
    public function deleteProduct(int $productId): void
    {
        try {
            $this->elasticsearch->delete([
                'index' => $this->index,
                'id' => (string) $productId,
            ]);
        } catch (Exception $e) {
            // 404 is fine to ignore on delete
            if (mb_strpos($e->getMessage(), '404') === false) {
                Log::error('Failed to delete product from ES: '.$e->getMessage());
            }
        }
    }

    /**
     * Delete the entire index
     */
    public function deleteIndex(): void
    {
        try {
            if ($this->elasticsearch->indices()->exists(['index' => $this->index])) {
                $this->elasticsearch->indices()->delete(['index' => $this->index]);
            }
        } catch (Exception $e) {
            Log::error('Failed to delete index: '.$e->getMessage());
        }
    }

    /**
     * Reindex all products
     */
    public function reindexAll(): void
    {
        $this->deleteIndex();
        $this->createIndex();

        Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])
            ->chunk(100, function ($products) {
                foreach ($products as $product) {
                    $this->indexProduct($product);
                }
            });
    }

    /**
     * Fallback SQL search when Elasticsearch is unavailable
     */
    public function searchFallback(string $query, array $filters = []): Collection
    {
        $productsQuery = Product::with(['categories', 'brand', 'tags', 'attributeValues.attribute'])
            ->where('status', $filters['status'] ?? 'active');

        // Text search
        if (! empty($query)) {
            $productsQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('sku', 'like', "%{$query}%");
            });
        }

        // Price range filter
        if (! empty($filters['price_min'])) {
            $productsQuery->where('price', '>=', (float) $filters['price_min']);
        }
        if (! empty($filters['price_max'])) {
            $productsQuery->where('price', '<=', (float) $filters['price_max']);
        }

        // Brand filter
        if (! empty($filters['brand'])) {
            $productsQuery->whereHas('brand', function ($q) use ($filters) {
                $q->where('title', 'like', "%{$filters['brand']}%");
            });
        }

        // Categories filter
        if (! empty($filters['categories'])) {
            $categoryIds = is_array($filters['categories'])
                ? $filters['categories']
                : [$filters['categories']];
            $productsQuery->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('categories.id', $categoryIds);
            });
        }

        // In stock filter
        if (! empty($filters['in_stock'])) {
            $productsQuery->where('stock', '>', 0);
        }

        // Apply sorting
        $sortBy = $filters['sort_by'] ?? 'created_at';
        switch ($sortBy) {
            case 'price_asc':
                $productsQuery->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $productsQuery->orderBy('price', 'desc');
                break;
            case 'newest':
                $productsQuery->orderBy('created_at', 'desc');
                break;
            case 'popular':
                // Note: This would require a clicks/views counter
                $productsQuery->orderBy('created_at', 'desc');
                break;
            default:
                $productsQuery->orderBy('created_at', 'desc');
        }

        return $productsQuery->limit(50)->get();
    }
}
