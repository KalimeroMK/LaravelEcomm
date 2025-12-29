<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers\Api;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use InvalidArgumentException;
use Log;
use Modules\Banner\Models\Banner;
use Modules\Billing\Services\WishlistService;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Front\Actions\BlogAction;
use Modules\Front\Actions\BlogByCategoryAction;
use Modules\Front\Actions\BlogByTagAction;
use Modules\Front\Actions\BlogDetailAction;
use Modules\Front\Actions\BlogFilterAction;
use Modules\Front\Actions\BlogSearchAction;
use Modules\Front\Actions\BundleDetailAction;
use Modules\Front\Actions\CouponStoreAction;
use Modules\Front\Actions\IndexAction;
use Modules\Front\Actions\MessageStoreAction;
use Modules\Front\Actions\NewsletterDeleteAction;
use Modules\Front\Actions\NewsletterSubscribeAction;
use Modules\Front\Actions\NewsletterVerifyAction;
use Modules\Front\Actions\PageDetailAction;
use Modules\Front\Actions\ProductBrandAction;
use Modules\Front\Actions\ProductBundlesAction;
use Modules\Front\Actions\ProductCatAction;
use Modules\Front\Actions\ProductDealAction;
use Modules\Front\Actions\ProductDetailAction;
use Modules\Front\Actions\ProductFilterAction;
use Modules\Front\Actions\ProductGridsAction;
use Modules\Front\Actions\ProductListsAction;
use Modules\Front\Actions\ProductSearchAction;
use Modules\Message\Http\Requests\Api\Store;
use Modules\Newsletter\Http\Requests\Store as NewsletterStoreRequest;
use Modules\Product\Services\ElasticsearchService;
use Modules\Product\Services\RecommendationService;

class FrontController extends CoreController
{
    public function index(IndexAction $indexAction): JsonResponse
    {
        return $this
            ->setMessage('Home page data retrieved successfully.')
            ->respond($indexAction());
    }

    public function productDetail(string $slug, ProductDetailAction $productDetailAction): JsonResponse
    {
        return $this
            ->setMessage('Product detail retrieved successfully.')
            ->respond($productDetailAction($slug));
    }

    public function productGrids(ProductGridsAction $productGridsAction): JsonResponse
    {
        return $this
            ->setMessage('Product grids retrieved successfully.')
            ->respond($productGridsAction());
    }

    public function productLists(ProductListsAction $productListsAction): JsonResponse
    {
        return $this
            ->setMessage('Product lists retrieved successfully.')
            ->respond($productListsAction());
    }

    public function productFilter(Request $request, ProductFilterAction $productFilterAction): JsonResponse
    {
        $currentRoute = $request->input('route', 'product-grids');
        $url = $productFilterAction->execute($request->all(), $currentRoute);

        return $this
            ->setMessage('Product filter applied successfully.')
            ->respond(['url' => $url]);
    }

    public function productSearch(Request $request, ProductSearchAction $productSearchAction): JsonResponse
    {
        return $this
            ->setMessage('Product search completed successfully.')
            ->respond($productSearchAction($request->all()));
    }

    public function productDeal(ProductDealAction $productDealAction): JsonResponse
    {
        return $this
            ->setMessage('Product deals retrieved successfully.')
            ->respond($productDealAction());
    }

    public function productBrand(string $slug, ProductBrandAction $productBrandAction): JsonResponse
    {
        return $this
            ->setMessage('Products by brand retrieved successfully.')
            ->respond($productBrandAction($slug));
    }

    public function productCat(string $slug, ProductCatAction $productCatAction): JsonResponse
    {
        return $this
            ->setMessage('Products by category retrieved successfully.')
            ->respond($productCatAction($slug));
    }

    public function blog(BlogAction $blogAction): JsonResponse
    {
        return $this
            ->setMessage('Blog posts retrieved successfully.')
            ->respond($blogAction());
    }

    public function blogDetail(string $slug, BlogDetailAction $blogDetailAction): JsonResponse
    {
        return $this
            ->setMessage('Blog detail retrieved successfully.')
            ->respond($blogDetailAction($slug));
    }

    public function blogSearch(Request $request, BlogSearchAction $blogSearchAction): JsonResponse
    {
        return $this
            ->setMessage('Blog search completed successfully.')
            ->respond($blogSearchAction($request));
    }

    public function blogFilter(Request $request, BlogFilterAction $blogFilterAction): JsonResponse
    {
        return $this
            ->setMessage('Blog filter applied successfully.')
            ->respond($blogFilterAction($request->all()));
    }

    public function blogByCategory(string $slug, BlogByCategoryAction $blogByCategoryAction): JsonResponse
    {
        return $this
            ->setMessage('Blog posts by category retrieved successfully.')
            ->respond($blogByCategoryAction($slug));
    }

    public function blogByTag(string $slug, BlogByTagAction $blogByTagAction): JsonResponse
    {
        return $this
            ->setMessage('Blog posts by tag retrieved successfully.')
            ->respond($blogByTagAction($slug));
    }

    public function couponStore(Request $request, CouponStoreAction $couponStoreAction): JsonResponse
    {
        try {
            $couponData = $couponStoreAction->execute($request->code);

            return $this
                ->setMessage('Coupon successfully applied.')
                ->respond($couponData);
        } catch (InvalidArgumentException $e) {
            return $this
                ->setMessage($e->getMessage())
                ->setStatusCode(422)
                ->respond(null);
        }
    }

    public function subscribe(NewsletterStoreRequest $request, NewsletterSubscribeAction $newsletterSubscribeAction): JsonResponse
    {
        if ($newsletterSubscribeAction($request)) {
            return $this
                ->setMessage('Subscribed successfully.')
                ->respond(null);
        }

        return $this
            ->setMessage('Email already present in the database.')
            ->setStatusCode(409)
            ->respond(null);
    }

    public function verifyNewsletter(string $token, NewsletterVerifyAction $newsletterVerifyAction): JsonResponse
    {
        if ($newsletterVerifyAction($token)) {
            return $this
                ->setMessage('Your email is successfully validated.')
                ->respond(null);
        }

        return $this
            ->setMessage('Token mismatch.')
            ->setStatusCode(400)
            ->respond(null);
    }

    public function deleteNewsletter(string $token, NewsletterDeleteAction $newsletterDeleteAction): JsonResponse
    {
        if ($newsletterDeleteAction($token)) {
            return $this
                ->setMessage('Your email is successfully deleted.')
                ->respond(null);
        }

        return $this
            ->setMessage('Token mismatch.')
            ->setStatusCode(400)
            ->respond(null);
    }

    public function messageStore(Store $request, MessageStoreAction $messageStoreAction): JsonResponse
    {
        try {
            $message = $messageStoreAction->execute($request->validated());

            return $this
                ->setMessage('Message sent successfully!')
                ->respond($message);
        } catch (Exception $e) {
            return $this
                ->setMessage($e->getMessage())
                ->setStatusCode(500)
                ->respond(null);
        }
    }

    public function bundles(ProductBundlesAction $productBundlesAction): JsonResponse
    {
        return $this
            ->setMessage('Bundles retrieved successfully.')
            ->respond($productBundlesAction());
    }

    public function bundleDetail(string $slug, BundleDetailAction $bundleDetailAction): JsonResponse
    {
        return $this
            ->setMessage('Bundle detail retrieved successfully.')
            ->respond($bundleDetailAction($slug));
    }

    public function pages(string $slug, PageDetailAction $pageDetailAction): JsonResponse
    {
        return $this
            ->setMessage('Page detail retrieved successfully.')
            ->respond($pageDetailAction($slug));
    }

    public function banners(Request $request): JsonResponse
    {
        $categoryId = $request->query('category');
        $query = Banner::with('categories');

        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId): void {
                $q->where('categories.id', $categoryId);
            });
        }

        $banners = $query->get()->filter(fn ($b): bool => $b->isActive());

        return $this
            ->setMessage('Banners retrieved successfully.')
            ->respond($banners);
    }

    public function bannerImpression(int $id): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        $banner->incrementImpression();

        return $this
            ->setMessage('Banner impression recorded successfully.')
            ->respond(['banner_id' => $id]);
    }

    public function advancedSearch(Request $request, ElasticsearchService $elasticsearchService): JsonResponse
    {
        $query = $request->input('query', '');
        $filters = $request->only([
            'price_min',
            'price_max',
            'brand',
            'categories',
            'status',
            'in_stock',
            'sort_by',
        ]);

        $products = collect();
        $totalResults = 0;
        $searchPerformed = false;

        if ($query) {
            $searchPerformed = true;
            $products = $elasticsearchService->search($query, $filters);

            if ($products === null) {
                Log::warning('Elasticsearch unavailable, falling back to SQL search');
                $products = $elasticsearchService->searchFallback($query, $filters);
            }

            $totalResults = $products ? $products->count() : 0;
        }

        $availableFilters = $this->getAvailableFilters($query);

        return $this
            ->setMessage('Advanced search completed successfully.')
            ->respond([
                'products' => $products,
                'query' => $query,
                'filters' => $filters,
                'availableFilters' => $availableFilters,
                'totalResults' => $totalResults,
                'searchPerformed' => $searchPerformed,
            ]);
    }

    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        if (mb_strlen($query) < 2) {
            return $this
                ->setMessage('Query too short.')
                ->respond(['suggestions' => []]);
        }

        $suggestions = $this->getSearchSuggestions($query);

        return $this
            ->setMessage('Search suggestions retrieved successfully.')
            ->respond($suggestions);
    }

    public function recommendations(Request $request, RecommendationService $recommendationService): JsonResponse
    {
        $user = auth()->user();
        $type = $request->input('type', 'ai');
        $limit = min($request->input('limit', 12), 50);

        $recommendations = collect();
        $recommendationType = 'trending';

        if ($user) {
            try {
                switch ($type) {
                    case 'ai':
                        $recommendations = $recommendationService->getAIRecommendations($user, $limit);
                        $recommendationType = 'AI-powered';
                        break;
                    case 'collaborative':
                        $recommendations = $recommendationService->getCollaborativeRecommendations($user, $limit);
                        $recommendationType = 'Based on similar users';
                        break;
                    case 'trending':
                        $recommendations = $recommendationService->getTrendingProducts($limit);
                        $recommendationType = 'Trending now';
                        break;
                }
            } catch (Exception $e) {
                $recommendations = $recommendationService->getTrendingProducts($limit);
                $recommendationType = 'Trending now';
            }
        } else {
            $recommendations = $recommendationService->getTrendingProducts($limit);
            $recommendationType = 'Trending now';
        }

        return $this
            ->setMessage('Recommendations retrieved successfully.')
            ->respond([
                'recommendations' => $recommendations,
                'type' => $type,
                'recommendationType' => $recommendationType,
                'totalCount' => $recommendations->count(),
            ]);
    }

    public function relatedProducts(string $slug, Request $request, RecommendationService $recommendationService): JsonResponse
    {
        $product = \Modules\Product\Models\Product::where('slug', $slug)->firstOrFail();
        $limit = min((int) $request->input('limit', 10), 20);

        $relatedProducts = $recommendationService->getContentBasedRecommendations($product, $limit);

        return $this
            ->setMessage('Related products retrieved successfully.')
            ->respond([
                'product' => $product,
                'relatedProducts' => $relatedProducts,
                'totalCount' => $relatedProducts->count(),
            ]);
    }

    public function enhancedWishlist(Request $request, WishlistService $wishlistService): JsonResponse
    {
        $user = auth()->user();

        if (! $user) {
            return $this
                ->setMessage('Please login to view your wishlist.')
                ->setStatusCode(401)
                ->respond(null);
        }

        $withPriceAlerts = $request->boolean('with_price_alerts', false);

        if ($withPriceAlerts) {
            $wishlist = $wishlistService->getWishlistWithPriceAlerts($user);
        } else {
            $wishlist = $wishlistService->getUserWishlist($user);
        }

        $stats = $wishlistService->getWishlistStats($user);
        $recommendations = $wishlistService->getWishlistRecommendations($user, 6);

        return $this
            ->setMessage('Wishlist retrieved successfully.')
            ->respond([
                'wishlist' => $wishlist,
                'statistics' => $stats,
                'recommendations' => $recommendations,
                'withPriceAlerts' => $withPriceAlerts,
            ]);
    }

    /**
     * Get available filters for search
     */
    private function getAvailableFilters(?string $query): array
    {
        $cacheKey = 'front_search_filters_'.md5(json_encode(['query' => $query]));

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($query) {
            $baseQuery = \Modules\Product\Models\Product::query();

            if ($query) {
                $baseQuery->where(function ($q) use ($query): void {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('summary', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            }

            $baseQuery->where('status', 'active');

            $priceRange = $baseQuery->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

            $brandsQuery = clone $baseQuery;
            $brands = $brandsQuery->join('brands', 'products.brand_id', '=', 'brands.id')
                ->select('brands.id', 'brands.name')
                ->distinct()
                ->get();

            $categoriesQuery = clone $baseQuery;
            $categories = $categoriesQuery->join('category_product', 'products.id', '=', 'category_product.product_id')
                ->join('categories', 'category_product.category_id', '=', 'categories.id')
                ->select('categories.id', 'categories.name')
                ->distinct()
                ->get();

            return [
                'price_range' => [
                    'min' => $priceRange->min_price ?? 0,
                    'max' => $priceRange->max_price ?? 1000,
                ],
                'brands' => $brands,
                'categories' => $categories,
                'statuses' => ['active', 'inactive'],
                'stock_options' => ['in_stock', 'out_of_stock'],
            ];
        });
    }

    /**
     * Get search suggestions
     */
    private function getSearchSuggestions(string $query): array
    {
        $popularTerms = \Modules\Product\Models\Product::where('title', 'like', "%{$query}%")
            ->orWhere('summary', 'like', "%{$query}%")
            ->pluck('title')
            ->take(5)
            ->toArray();

        $categorySuggestions = \Modules\Category\Models\Category::where('name', 'like', "%{$query}%")
            ->pluck('name')
            ->take(3)
            ->toArray();

        $brandSuggestions = \Modules\Brand\Models\Brand::where('name', 'like', "%{$query}%")
            ->pluck('name')
            ->take(3)
            ->toArray();

        return [
            'popular_terms' => $popularTerms,
            'categories' => $categorySuggestions,
            'brands' => $brandSuggestions,
            'suggested_query' => $this->generateSuggestedQuery($query),
        ];
    }

    /**
     * Generate suggested search query
     */
    private function generateSuggestedQuery(string $query): string
    {
        $corrections = [
            'laptop' => 'laptop computer',
            'phone' => 'smartphone',
            'tv' => 'television',
            'pc' => 'personal computer',
        ];

        return $corrections[$query] ?? $query;
    }
}
