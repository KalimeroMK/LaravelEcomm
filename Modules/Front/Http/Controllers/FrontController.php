<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Banner\Models\Banner;
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
use Modules\Front\Http\Requests\ProductSearchRequest;
use Modules\Message\Http\Requests\Api\Store;
use Modules\Product\Services\ElasticsearchService;
use Modules\Product\Services\RecommendationService;
use Modules\Billing\Services\WishlistService;

class FrontController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index(IndexAction $indexAction)
    {
        return view('front::index', $indexAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function aboutUs()
    {
        return view('front::pages.about-us', []);
    }

    /**
     * @return Application|Factory|View
     */
    public function contact()
    {
        return view('front::pages.contact', []);
    }

    /**
     * @return Application|Factory|View
     */
    public function productDetail(string $slug, ProductDetailAction $productDetailAction)
    {
        return view('front::pages.product_detail', $productDetailAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function productGrids(ProductGridsAction $productGridsAction)
    {
        return view('front::pages.product-grids', $productGridsAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function bundles(ProductBundlesAction $productBundlesAction)
    {
        return view('front::pages.bundles', $productBundlesAction());
    }

    public function bundleDetail(string $slug, BundleDetailAction $bundleDetailAction)
    {
        return view('front::pages.bundle_detail', $bundleDetailAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function productLists(ProductListsAction $productListsAction)
    {
        return view('front::pages.product-lists', $productListsAction());
    }

    public function productFilter(Request $request, ProductFilterAction $productFilterAction): RedirectResponse
    {
        return $productFilterAction($request->all());
    }

    /**
     * @return Application|Factory|View
     */
    public function productSearch(ProductSearchRequest $request, ProductSearchAction $productSearchAction)
    {
        return view('front::pages.product-grids', $productSearchAction($request->validated()));
    }

    /**
     * @return Application|Factory|View
     */
    public function productDeal(ProductDealAction $productDealAction)
    {
        return view('front::pages.product-deal', $productDealAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function productBrand(Request $request, ProductBrandAction $productBrandAction)
    {
        if (request()->is('e-shop.loc/product-grids')) {
            return view('front::pages.product-grids', $productBrandAction($request->all()));
        }

        return view('front::pages.product-lists', $productBrandAction($request->all()));
    }

    /**
     * @return Application|Factory|View
     */
    public function productCat(string $slug, ProductCatAction $productCatAction)
    {
        return view('front::pages.product-lists', $productCatAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function blog(BlogAction $blogAction)
    {
        return view('front::pages.blog', $blogAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function blogDetail(string $slug, BlogDetailAction $blogDetailAction)
    {
        return view('front::pages.blog-detail', $blogDetailAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function blogSearch(Request $request, BlogSearchAction $blogSearchAction)
    {
        return view('front::pages.blog', $blogSearchAction($request));
    }

    public function blogFilter(Request $request, BlogFilterAction $blogFilterAction): RedirectResponse
    {
        return redirect()->route('front.blog', $blogFilterAction($request->all()));
    }

    /**
     * @return Application|Factory|View
     */
    public function blogByCategory(string $slug, BlogByCategoryAction $blogByCategoryAction)
    {
        return view('front::pages.blog', $blogByCategoryAction($slug));
    }

    /**
     * Display the blog posts filtered by tag.
     *
     * @param  string  $slug  The tag slug to filter blog posts by.
     * @return View The view displaying the filtered blog posts.
     */
    public function blogByTag(string $slug, BlogByTagAction $blogByTagAction): View
    {
        return view('front::pages.blog', $blogByTagAction($slug));
    }

    public function couponStore(Request $request, CouponStoreAction $couponStoreAction): RedirectResponse
    {
        return $couponStoreAction($request);
    }

    public function subscribe(Request $request, NewsletterSubscribeAction $newsletterSubscribeAction): RedirectResponse
    {
        if ($newsletterSubscribeAction($request)) {
            return redirect()->back()->with('message', 'Your comment successfully send.');
        }

        return redirect()->back()->with('message', 'Your email is already in our mailing list.');
    }

    public function verifyNewsletter(string $token, NewsletterVerifyAction $newsletterVerifyAction): RedirectResponse
    {
        if ($newsletterVerifyAction($token)) {
            return redirect()->back()->with('message', 'Your email is successfully validated.');
        }

        return redirect()->back()->with('message', 'token mismatch ');
    }

    public function deleteNewsletter(string $token, NewsletterDeleteAction $newsletterDeleteAction): RedirectResponse
    {
        if ($newsletterDeleteAction($token)) {
            return redirect()->back()->with('message', 'Your email is successfully deleted.');
        }

        return redirect()->back()->with('message', 'token mismatch ');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function messageStore(Store $request, MessageStoreAction $messageStoreAction): ?string
    {
        return $messageStoreAction($request);
    }

    /**
     * Advanced search with Elasticsearch
     */
    public function advancedSearch(Request $request, ElasticsearchService $elasticsearchService): View
    {
        $query = $request->input('query', '');
        $filters = $request->only([
            'price_min',
            'price_max',
            'brand',
            'categories',
            'status',
            'in_stock',
            'sort_by'
        ]);

        $products = collect();
        $totalResults = 0;
        $searchPerformed = false;

        if ($query) {
            $searchPerformed = true;
            try {
                $products = $elasticsearchService->search($query, $filters);
                $totalResults = $products->count();
            } catch (\Exception $e) {
                // Fallback to basic search if Elasticsearch fails
                $products = \Modules\Product\Models\Product::where('title', 'like', "%{$query}%")
                    ->orWhere('summary', 'like', "%{$query}%")
                    ->where('status', 'active')
                    ->get();
                $totalResults = $products->count();
            }
        }

        // Get available filters
        $availableFilters = $this->getAvailableFilters($query);

        return view('front::pages.advanced-search', [
            'products' => $products,
            'query' => $query,
            'filters' => $filters,
            'availableFilters' => $availableFilters,
            'totalResults' => $totalResults,
            'searchPerformed' => $searchPerformed
        ]);
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        if (strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        $suggestions = $this->getSearchSuggestions($query);

        return response()->json($suggestions);
    }

    /**
     * Get product recommendations
     */
    public function recommendations(Request $request, RecommendationService $recommendationService): View
    {
        $user = Auth::user();
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
            } catch (\Exception $e) {
                // Fallback to trending products
                $recommendations = $recommendationService->getTrendingProducts($limit);
                $recommendationType = 'Trending now';
            }
        } else {
            // For non-authenticated users, show trending products
            $recommendations = $recommendationService->getTrendingProducts($limit);
            $recommendationType = 'Trending now';
        }

        return view('front::pages.recommendations', [
            'recommendations' => $recommendations,
            'type' => $type,
            'recommendationType' => $recommendationType,
            'totalCount' => $recommendations->count()
        ]);
    }

    /**
     * Get related products for a specific product
     */
    public function relatedProducts(string $slug, Request $request, RecommendationService $recommendationService): View
    {
        $product = \Modules\Product\Models\Product::where('slug', $slug)->firstOrFail();
        $limit = min($request->input('limit', 8), 20);

        $relatedProducts = $recommendationService->getContentBasedRecommendations($product, $limit);

        return view('front::pages.related-products', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'totalCount' => $relatedProducts->count()
        ]);
    }

    /**
     * Enhanced wishlist page with advanced features
     */
    public function enhancedWishlist(Request $request, WishlistService $wishlistService): View|RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('front.login')->with('message', 'Please login to view your wishlist.');
        }

        $withPriceAlerts = $request->boolean('with_price_alerts', false);

        if ($withPriceAlerts) {
            $wishlist = $wishlistService->getWishlistWithPriceAlerts($user);
        } else {
            $wishlist = $wishlistService->getUserWishlist($user);
        }

        $stats = $wishlistService->getWishlistStats($user);
        $recommendations = $wishlistService->getWishlistRecommendations($user, 6);

        return view('front::pages.enhanced-wishlist', [
            'wishlist' => $wishlist,
            'statistics' => $stats,
            'recommendations' => $recommendations,
            'withPriceAlerts' => $withPriceAlerts
        ]);
    }

    /**
     * Get available filters for search
     */
    private function getAvailableFilters(?string $query): array
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
            ->select('brands.id', 'brands.name')
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

    /**
     * Get search suggestions
     */
    private function getSearchSuggestions(string $query): array
    {
        // Get popular search terms
        $popularTerms = \Modules\Product\Models\Product::where('title', 'like', "%{$query}%")
            ->orWhere('summary', 'like', "%{$query}%")
            ->pluck('title')
            ->take(5)
            ->toArray();

        // Get category suggestions
        $categorySuggestions = \Modules\Category\Models\Category::where('name', 'like', "%{$query}%")
            ->pluck('name')
            ->take(3)
            ->toArray();

        // Get brand suggestions
        $brandSuggestions = \Modules\Brand\Models\Brand::where('name', 'like', "%{$query}%")
            ->pluck('name')
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
    private function generateSuggestedQuery(string $query): string
    {
        $corrections = [
            'laptop' => 'laptop computer',
            'phone' => 'smartphone',
            'tv' => 'television',
            'pc' => 'personal computer'
        ];

        return $corrections[$query] ?? $query;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if the payment method is PayPal and redirect to the payment route
        if (Arr::get($request, 'payment_method') === 'paypal') {
            return redirect()->route('payment');
        }

        // Check if the payment method is Stripe and redirect to the Stripe route with the user ID
        if (Arr::get($request, 'payment_method') === 'stripe') {
            return redirect()->route('stripe', Auth::id());
        }

        // Clear the cart and coupon from the session
        session()->forget('cart');
        session()->forget('coupon');

        // Redirect to the home page
        return redirect()->route('front.index');
    }

    public function pages(string $slug, PageDetailAction $pageDetailAction): View
    {
        return view('front::pages.page', $pageDetailAction($slug));
    }

    /**
     * Show banners on frontend (filtered by active status and optionally by category).
     */
    public function banners(Request $request): View
    {
        $categoryId = $request->query('category');
        $query = Banner::with('categories');
        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        }
        $banners = $query->get()->filter(fn($b) => $b->isActive());

        return view('front::banner', ['banners' => $banners]);
    }

    /**
     * Track banner impression (AJAX).
     */
    public function bannerImpression($id): JsonResponse
    {
        $banner = Banner::findOrFail($id);
        $banner->incrementImpression();

        return response()->json(['success' => true]);
    }
}
