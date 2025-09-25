<?php

declare(strict_types=1);

namespace Modules\Product\Services;

// use Elasticsearch\Client;
use Illuminate\Support\Collection;
use Modules\Product\Models\Product;

class ElasticsearchService
{
    // protected Client $elasticsearch;
    protected string $index = 'products';

    public function __construct()
    {
        // $this->elasticsearch = $elasticsearch;
    }

    /**
     * Index a product in Elasticsearch
     */
    public function indexProduct(Product $product): void
    {
        [
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
                'attributes' => $product->attributeValues->map(function ($attr): array {
                    return [
                        'name' => $attr->attribute->name,
                        'value' => $attr->value,
                    ];
                })->toArray(),
                'status' => $product->status,
                'is_featured' => $product->is_featured,
                'stock' => $product->stock,
                'created_at' => $product->created_at?->toISOString(),
            ],
        ];

        // $this->elasticsearch->index($params); // Commented out for now as Elasticsearch is not configured
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
                                'fuzziness' => 'AUTO',
                            ],
                        ],
                        'filter' => [],
                    ],
                ],
                'sort' => [
                    '_score' => ['order' => 'desc'],
                    'created_at' => ['order' => 'desc'],
                ],
                'size' => 50,
            ],
        ];

        // Add filters
        if (! empty($filters['price_min'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['price' => ['gte' => $filters['price_min']]],
            ];
        }

        if (! empty($filters['price_max'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['price' => ['lte' => $filters['price_max']]],
            ];
        }

        if (! empty($filters['brand'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'term' => ['brand' => $filters['brand']],
            ];
        }

        if (! empty($filters['categories'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'terms' => ['categories' => $filters['categories']],
            ];
        }

        if (! empty($filters['status'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'term' => ['status' => $filters['status']],
            ];
        }

        if (! empty($filters['in_stock'])) {
            $searchParams['body']['query']['bool']['filter'][] = [
                'range' => ['stock' => ['gt' => 0]],
            ];
        }

        // For now, fallback to database search since Elasticsearch is not configured
        $productQuery = Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])
            ->where('status', 'active')
            ->where('stock', '>', 0);

        // Add basic text search
        if ($query !== '' && $query !== '0') {
            $productQuery->where(function ($q) use ($query): void {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        // Add filters
        if (! empty($filters['price_min'])) {
            $productQuery->where('price', '>=', $filters['price_min']);
        }

        if (! empty($filters['price_max'])) {
            $productQuery->where('price', '<=', $filters['price_max']);
        }

        if (! empty($filters['brand'])) {
            $productQuery->whereHas('brand', function ($q) use ($filters): void {
                $q->where('name', $filters['brand']);
            });
        }

        if (! empty($filters['categories'])) {
            $productQuery->whereHas('categories', function ($q) use ($filters): void {
                $q->whereIn('id', $filters['categories']);
            });
        }

        if (! empty($filters['status'])) {
            $productQuery->where('status', $filters['status']);
        }

        if (! empty($filters['in_stock'])) {
            $productQuery->where('stock', '>', 0);
        }

        return $productQuery->limit(50)->get();
    }

    /**
     * Create the Elasticsearch index
     */
    public function createIndex(): void {}

    /**
     * Delete a product from Elasticsearch
     */
    public function deleteProduct(int $productId): void {}

    /**
     * Delete the entire index
     */
    public function deleteIndex(): void {}

    /**
     * Reindex all products
     */
    public function reindexAll(): void
    {
        $this->createIndex();

        Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])->chunk(100, function ($products): void {
            foreach ($products as $product) {
                $this->indexProduct($product);
            }
        });
    }
}
