<?php

declare(strict_types=1);

namespace Modules\Product\Services;

use Elasticsearch\Client;
use Illuminate\Support\Collection;
use Modules\Product\Models\Product;

class ElasticsearchService
{
    protected Client $elasticsearch;
    protected string $index = 'products';

    public function __construct(Client $elasticsearch)
    {
        $this->elasticsearch = $elasticsearch;
    }

    /**
     * Index a product in Elasticsearch
     */
    public function indexProduct(Product $product): void
    {
        $params = [
            'index' => $this->index,
            'id' => $product->id,
            'body' => [
                'title' => $product->title,
                'summary' => $product->summary,
                'description' => $product->description,
                'sku' => $product->sku,
                'price' => $product->price,
                'special_price' => $product->special_price,
                'brand' => $product->brand?->name,
                'categories' => $product->categories->pluck('name')->toArray(),
                'tags' => $product->tags->pluck('name')->toArray(),
                'attributes' => $product->attributeValues->map(function ($attr) {
                    return [
                        'name' => $attr->attribute->name,
                        'value' => $attr->value
                    ];
                })->toArray(),
                'status' => $product->status,
                'is_featured' => $product->is_featured,
                'stock' => $product->stock,
                'created_at' => $product->created_at?->toISOString(),
            ]
        ];

        $this->elasticsearch->index($params);
    }

    /**
     * Advanced search with filters
     */
    public function search(string $query, array $filters = []): Collection
    {
        $searchParams = [
            'index' => $this->index,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            'multi_match' => [
                                'query' => $query,
                                'fields' => ['title^3', 'summary^2', 'description', 'sku'],
                                'type' => 'best_fields',
                                'fuzziness' => 'AUTO'
                            ]
                        ],
                        'filter' => []
                    ]
                ],
                'sort' => [
                    '_score' => ['order' => 'desc'],
                    'created_at' => ['order' => 'desc']
                ],
                'size' => 50
            ]
        ];

        // Add filters
        if (!empty($filters['price_min'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['price' => ['gte' => $filters['price_min']]]
            ];
        }

        if (!empty($filters['price_max'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['price' => ['lte' => $filters['price_max']]]
            ];
        }

        if (!empty($filters['brand'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'term' => ['brand' => $filters['brand']]
            ];
        }

        if (!empty($filters['categories'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'terms' => ['categories' => $filters['categories']]
            ];
        }

        if (!empty($filters['status'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'term' => ['status' => $filters['status']]
            ];
        }

        if (!empty($filters['in_stock'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['stock' => ['gt' => 0]]
            ];
        }

        $response = $this->elasticsearch->search($searchParams);

        $productIds = collect($response['hits']['hits'])->pluck('_id');

        return Product::whereIn('id', $productIds)
            ->orderByRaw("FIELD(id, " . $productIds->implode(',') . ")")
            ->get();
    }

    /**
     * Create the Elasticsearch index
     */
    public function createIndex(): void
    {
        $params = [
            'index' => $this->index,
            'body' => [
                'settings' => [
                    'analysis' => [
                        'analyzer' => [
                            'product_analyzer' => [
                                'type' => 'custom',
                                'tokenizer' => 'standard',
                                'filter' => ['lowercase', 'stop', 'snowball']
                            ]
                        ]
                    ]
                ],
                'mappings' => [
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
                ]
            ]
        ];

        try {
            $this->elasticsearch->indices()->create($params);
        } catch (\Exception $e) {
            // Index might already exist
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
                'id' => $productId
            ]);
        } catch (\Exception $e) {
            // Product might not exist in index
        }
    }

    /**
     * Delete the entire index
     */
    public function deleteIndex(): void
    {
        try {
            $this->elasticsearch->indices()->delete(['index' => $this->index]);
        } catch (\Exception $e) {
            // Index might not exist
        }
    }

    /**
     * Reindex all products
     */
    public function reindexAll(): void
    {
        $this->createIndex();

        Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])->chunk(100, function ($products) {
            foreach ($products as $product) {
                $this->indexProduct($product);
            }
        });
    }
}
