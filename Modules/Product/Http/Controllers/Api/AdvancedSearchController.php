<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Product\Services\ElasticsearchService;
use Modules\Product\Services\RecommendationService;

class AdvancedSearchController extends Controller
{
    protected ElasticsearchService $elasticsearchService;
    protected RecommendationService $recommendationService;

    public function __construct(
        ElasticsearchService $elasticsearchService,
        RecommendationService $recommendationService
    ) {
        $this->elasticsearchService = $elasticsearchService;
        $this->recommendationService = $recommendationService;
    }

    /**
     * Advanced search with filters
     */
    public function search(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2|max:100',
            'price_min' => 'nullable|numeric|min:0',
            'price_max' => 'nullable|numeric|min:0',
            'brand' => 'nullable|string',
            'categories' => 'nullable|array',
            'categories.*' => 'integer|exists:categories,id',
            'status' => 'nullable|string|in:active,inactive',
            'in_stock' => 'nullable|boolean',
            'sort_by' => 'nullable|string|in:relevance,price_asc,price_desc,newest,popular',
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100'
        ]);

        $filters = $request->only([
            'price_min',
            'price_max',
            'brand',
            'categories',
            'status',
            'in_stock'
        ]);

        $query = $request->input('query');
        $products = $this->elasticsearchService->search($query, $filters);

        // Apply sorting
        $products = $this->applySorting($products, $request->input('sort_by', 'relevance'));

        // Paginate results
        $perPage = $request->input('per_page', 20);
        $page = $request->input('page', 1);

        $paginatedProducts = $products->forPage($page, $perPage);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $paginatedProducts,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $perPage,
                    'total' => $products->count(),
                    'last_page' => ceil($products->count() / $perPage),
                ],
                'filters_applied' => $filters,
                'search_query' => $query
            ]
        ]);
    }

    /**
     * Get search suggestions
     */
    public function suggestions(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:50'
        ]);

        $query = $request->input('query');

        // Get popular search terms and product suggestions
        $suggestions = $this->getSearchSuggestions($query);

        return response()->json([
            'success' => true,
            'data' => $suggestions
        ]);
    }

    /**
     * Get search filters and facets
     */
    public function filters(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'nullable|string|min:1'
        ]);

        $query = $request->input('query');

        // Get available filters based on search results
        $filters = $this->getAvailableFilters($query);

        return response()->json([
            'success' => true,
            'data' => $filters
        ]);
    }

    /**
     * Get product recommendations
     */
    public function recommendations(Request $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Authentication required for personalized recommendations'
            ], 401);
        }

        $type = $request->input('type', 'ai');
        $limit = min((int) $request->input('limit', 10), 50);

        $recommendations = match ($type) {
            'ai' => $this->recommendationService->getAIRecommendations($user, $limit),
            'collaborative' => $this->recommendationService->getCollaborativeRecommendations($user, $limit),
            'trending' => $this->recommendationService->getTrendingProducts($limit),
            default => $this->recommendationService->getAIRecommendations($user, $limit)
        };

        return response()->json([
            'success' => true,
            'data' => [
                'recommendations' => $recommendations,
                'type' => $type,
                'count' => $recommendations->count()
            ]
        ]);
    }

    /**
     * Get related products
     */
    public function relatedProducts(Request $request, int $productId): JsonResponse
    {
        $request->validate([
            'limit' => 'nullable|integer|min:1|max:20'
        ]);

        $product = \Modules\Product\Models\Product::with(['brand', 'categories', 'tags', 'attributeValues.attribute'])->findOrFail($productId);
        $limit = min((int) $request->input('limit', 10), 20);

        $relatedProducts = $this->recommendationService->getContentBasedRecommendations($product, $limit);

        return response()->json([
            'success' => true,
            'data' => [
                'products' => $relatedProducts,
                'base_product' => [
                    'id' => $product->id,
                    'title' => $product->title,
                    'slug' => $product->slug
                ]
            ]
        ]);
    }

    /**
     * Apply sorting to products
     */
    protected function applySorting($products, string $sortBy)
    {
        return match ($sortBy) {
            'price_asc' => $products->sortBy('price'),
            'price_desc' => $products->sortByDesc('price'),
            'newest' => $products->sortByDesc('created_at'),
            'popular' => $products->sortByDesc(function ($product) {
                return $product->clicks()->count();
            }),
            default => $products // relevance - already sorted by Elasticsearch
        };
    }

    /**
     * Get search suggestions
     */
    protected function getSearchSuggestions(string $query): array
    {
        // Get popular search terms
        $popularTerms = \Modules\Product\Models\Product::where('title', 'like', "%{$query}%")
            ->orWhere('summary', 'like', "%{$query}%")
            ->pluck('title')
            ->take(5)
            ->toArray();

        // Get category suggestions
        $categorySuggestions = \Modules\Category\Models\Category::where('title', 'like', "%{$query}%")
            ->pluck('title')
            ->take(3)
            ->toArray();

        // Get brand suggestions
        $brandSuggestions = \Modules\Brand\Models\Brand::where('title', 'like', "%{$query}%")
            ->pluck('title')
            ->take(3)
            ->toArray();

        return [
            'popular_terms' => $popularTerms,
            'categories' => $categorySuggestions,
            'brands' => $brandSuggestions,
            'suggested_query' => $this->generateSuggestedQuery($query)
        ];
    }

    /**
     * Generate suggested search query
     */
    protected function generateSuggestedQuery(string $query): string
    {
        // Simple query correction logic
        $corrections = [
            'laptop' => 'laptop computer',
            'phone' => 'smartphone',
            'tv' => 'television',
            'pc' => 'personal computer'
        ];

        return $corrections[$query] ?? $query;
    }

    /**
     * Get available filters
     */
    protected function getAvailableFilters(?string $query): array
    {
        $baseQuery = \Modules\Product\Models\Product::query();

        if ($query) {
            $baseQuery->where(function ($q) use ($query) {
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%");
            });
        }

        $baseQuery->where('status', 'active');

        // Get price range
        $priceRange = $baseQuery->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

        // Get available brands
        $brands = $baseQuery->join('brands', 'products.brand_id', '=', 'brands.id')
            ->select('brands.id', 'brands.title as name')
            ->distinct()
            ->get();

        // Get available categories
        $categories = $baseQuery->join('category_product', 'products.id', '=', 'category_product.product_id')
            ->join('categories', 'category_product.category_id', '=', 'categories.id')
            ->select('categories.id', 'categories.name')
            ->distinct()
            ->get();

        return [
            'price_range' => [
                'min' => $priceRange->min_price ?? 0,
                'max' => $priceRange->max_price ?? 1000
            ],
            'brands' => $brands,
            'categories' => $categories,
            'statuses' => ['active', 'inactive'],
            'stock_options' => ['in_stock', 'out_of_stock']
        ];
    }
}
