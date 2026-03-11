<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use InvalidArgumentException;
use Log;
use Modules\Banner\Models\Banner;
use Modules\Billing\Services\WishlistService;
use Modules\Front\Actions\BlogAction;
use Modules\Front\Actions\BlogByCategoryAction;
use Modules\Front\Actions\BlogByTagAction;
use Modules\Front\Actions\BlogDetailAction;
use Modules\Front\Actions\BlogFilterAction;
use Modules\Front\Actions\BlogSearchAction;
use Modules\Front\Actions\BundleDetailAction;
use Modules\Coupon\Actions\ApplyCouponAction;
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
use Modules\Core\Helpers\Helper;
use Modules\Front\Http\Requests\ProductSearchRequest;
use Modules\Order\Actions\ReorderAction;
use Modules\Order\Actions\StoreOrderAction;
use Modules\Order\DTOs\OrderDTO;
use Modules\Order\Http\Requests\Store as OrderStoreRequest;
use Modules\Order\Models\Order;
use Modules\Message\Http\Requests\Store;
use Modules\Product\Services\ElasticsearchService;
use Modules\Product\Services\RecentlyViewedService;
use Modules\Product\Services\RecommendationService;

// theme_view() is loaded via composer autoload files

class FrontController extends Controller
{
    /**
     * @return Application|Factory|View
     */
    public function index(IndexAction $indexAction): Factory|View
    {
        return view(theme_view('index'), $indexAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function aboutUs(): Factory|View
    {
        return view(theme_view('pages.about-us'), []);
    }

    /**
     * @return Application|Factory|View
     */
    public function contact(): Factory|View
    {
        return view(theme_view('pages.contact'), []);
    }

    /**
     * @return Application|Factory|View
     */
    public function productDetail(string $locale, string $slug, ProductDetailAction $productDetailAction): Factory|View
    {
        try {
            $data = $productDetailAction($slug);
            return view(theme_view('pages.product_detail'), $data);
        } catch (\Exception $e) {
            \Log::error('productDetail error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function productGrids(ProductGridsAction $productGridsAction): Factory|View
    {
        return view(theme_view('pages.product-grids'), $productGridsAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function products(ProductGridsAction $productGridsAction): Factory|View
    {
        return view(theme_view('pages.product-grids'), $productGridsAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function bundles(ProductBundlesAction $productBundlesAction): Factory|View
    {
        return view(theme_view('pages.bundles'), $productBundlesAction());
    }

    public function bundleDetail(string $locale, string $slug, BundleDetailAction $bundleDetailAction): Factory|View
    {
        return view(theme_view('pages.bundle_detail'), $bundleDetailAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function productLists(ProductListsAction $productListsAction): Factory|View
    {
        return view(theme_view('pages.product-lists'), $productListsAction());
    }

    public function productFilter(Request $request, ProductFilterAction $productFilterAction): RedirectResponse
    {
        $appUrl = config('app.url');
        $currentRoute = request()->is($appUrl.'/product-grids') ? 'product-grids' : 'product-lists';
        $url = $productFilterAction->execute($request->all(), $currentRoute);

        return redirect($url);
    }

    /**
     * @return Application|Factory|View
     */
    public function productSearch(ProductSearchRequest $request, ProductSearchAction $productSearchAction): Factory|View
    {
        return view(theme_view('pages.product-grids'), $productSearchAction($request->validated()));
    }

    /**
     * @return Application|Factory|View
     */
    public function productDeal(ProductDealAction $productDealAction): Factory|View
    {
        return view(theme_view('pages.product-deal'), $productDealAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function productBrand(string $locale, string $slug, ProductBrandAction $productBrandAction): Factory|View
    {
        if (request()->is('e-shop.loc/product-grids')) {
            return view(theme_view('pages.product-grids'), $productBrandAction($slug));
        }

        return view(theme_view('pages.product-lists'), $productBrandAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function productCat(string $locale, string $slug, ProductCatAction $productCatAction): Factory|View
    {
        return view(theme_view('pages.category-detail'), $productCatAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function categories(): Factory|View
    {
        // Get only parent categories (top-level) with their children count
        $categories = \Modules\Category\Models\Category::active()
            ->whereNull('parent_id')
            ->withCount('children')
            ->get();
        
        return view(theme_view('pages.categories'), ['categories' => $categories]);
    }

    /**
     * @return Application|Factory|View
     */
    public function categoryDetail(string $locale, string $slug, ProductCatAction $productCatAction): Factory|View
    {
        return view(theme_view('pages.product-lists'), $productCatAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function blog(BlogAction $blogAction): Factory|View
    {
        return view(theme_view('pages.blog'), $blogAction());
    }

    /**
     * @return Application|Factory|View
     */
    public function blogDetail(string $locale, string $slug, BlogDetailAction $blogDetailAction): Factory|View
    {
        return view(theme_view('pages.blog-detail'), $blogDetailAction($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function blogSearch(Request $request, BlogSearchAction $blogSearchAction): Factory|View
    {
        return view(theme_view('pages.blog'), $blogSearchAction($request));
    }

    public function blogFilter(Request $request, BlogFilterAction $blogFilterAction): RedirectResponse
    {
        return redirect()->route('front.blog', $blogFilterAction($request->all()));
    }

    /**
     * @return Application|Factory|View
     */
    public function blogByCategory(string $locale, string $slug, BlogByCategoryAction $blogByCategoryAction): Factory|View
    {
        return view(theme_view('pages.blog'), $blogByCategoryAction($slug));
    }

    /**
     * Display the blog posts filtered by tag.
     *
     * @param  string  $slug  The tag slug to filter blog posts by.
     * @return View The view displaying the filtered blog posts.
     */
    public function blogByTag(string $locale, string $slug, BlogByTagAction $blogByTagAction): View
    {
        return view(theme_view('pages.blog'), $blogByTagAction($slug));
    }

    public function couponStore(Request $request, ApplyCouponAction $applyCouponAction): RedirectResponse
    {
        try {
            $user = Auth::user();
            $result = $applyCouponAction->execute(
                $request->code,
                $user?->id ?? 0,
                session()->getId(),
                $user?->customer_group_id ?? null
            );
            request()->session()->flash('success', $result['message']);
        } catch (InvalidArgumentException $e) {
            request()->session()->flash('error', $e->getMessage());
        }

        return redirect()->back();
    }

    public function couponRemove(ApplyCouponAction $applyCouponAction): RedirectResponse
    {
        $result = $applyCouponAction->remove();
        request()->session()->flash($result['success'] ? 'success' : 'info', $result['message']);
        return redirect()->back();
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
    public function messageStore(Store $request, MessageStoreAction $messageStoreAction): RedirectResponse
    {
        try {
            $message = $messageStoreAction->execute($request->validated());
            request()->session()->flash('success', 'Message sent successfully!');
        } catch (Exception $e) {
            request()->session()->flash('error', $e->getMessage());
        }

        return redirect()->back();
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
            'sort_by',
        ]);

        $products = collect();
        $totalResults = 0;
        $searchPerformed = false;

        if ($query) {
            $searchPerformed = true;

            // Try Elasticsearch first, fallback to SQL if it fails
            $products = $elasticsearchService->search($query, $filters);

            if ($products === null) {
                // Elasticsearch failed, use SQL fallback with proper relationships
                Log::warning('Elasticsearch unavailable, falling back to SQL search');
                $products = $elasticsearchService->searchFallback($query, $filters);
            }

            $totalResults = $products ? $products->count() : 0;
        }

        // Get available filters
        $availableFilters = $this->getAvailableFilters($query);

        return view(theme_view('pages.advanced-search'), [
            'products' => $products,
            'query' => $query,
            'filters' => $filters,
            'availableFilters' => $availableFilters,
            'totalResults' => $totalResults,
            'searchPerformed' => $searchPerformed,
        ]);
    }

    /**
     * Get search suggestions for autocomplete
     */
    public function searchSuggestions(Request $request): JsonResponse
    {
        $query = $request->input('query', '');

        if (mb_strlen($query) < 2) {
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
            } catch (Exception $e) {
                // Fallback to trending products
                $recommendations = $recommendationService->getTrendingProducts($limit);
                $recommendationType = 'Trending now';
            }
        } else {
            // For non-authenticated users, show trending products
            $recommendations = $recommendationService->getTrendingProducts($limit);
            $recommendationType = 'Trending now';
        }

        return view(theme_view('pages.recommendations'), [
            'recommendations' => $recommendations,
            'type' => $type,
            'recommendationType' => $recommendationType,
            'totalCount' => $recommendations->count(),
        ]);
    }

    /**
     * Get related products for a specific product
     */
    public function relatedProducts(string $locale, string $slug, Request $request, RecommendationService $recommendationService): View
    {
        $product = \Modules\Product\Models\Product::where('slug', $slug)->firstOrFail();
        $limit = min($request->input('limit', 8), 20);

        $relatedProducts = $recommendationService->getContentBasedRecommendations($product, $limit);

        return view(theme_view('pages.related-products'), [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'totalCount' => $relatedProducts->count(),
        ]);
    }

    /**
     * Enhanced wishlist page with advanced features
     */
    public function enhancedWishlist(Request $request, WishlistService $wishlistService): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
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

        return view(theme_view('pages.enhanced-wishlist'), [
            'wishlist' => $wishlist,
            'statistics' => $stats,
            'recommendations' => $recommendations,
            'withPriceAlerts' => $withPriceAlerts,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(OrderStoreRequest $request, StoreOrderAction $storeOrderAction, ApplyCouponAction $applyCouponAction): RedirectResponse
    {
        $user = Auth::user();
        
        // Calculate cart totals
        $cartItems = Helper::getAllProductFromCart((string) ($user?->id ?? ''));
        $subtotal = Helper::totalCartPrice((string) ($user?->id ?? ''));
        $quantity = $cartItems->sum('quantity');
        
        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }
        
        // Get shipping cost (skip for virtual/downloadable products)
        $shippingId = null;
        $shippingCost = 0;
        
        if (\Modules\Core\Helpers\Helper::cartRequiresShipping()) {
            $shippingId = $request->input('shipping');
            if ($shippingId) {
                $shipping = \Modules\Shipping\Models\Shipping::find($shippingId);
                $shippingCost = $shipping?->price ?? 0;
            }
        }
        
        // Calculate total with coupon discount
        $couponData = session('coupon');
        $couponDiscount = $couponData['discount'] ?? 0;
        $couponId = $couponData['id'] ?? null;
        $freeShipping = $couponData['free_shipping'] ?? false;
        
        // Apply free shipping discount
        if ($freeShipping && $couponDiscount === 0) {
            $couponDiscount = $shippingCost;
        }
        
        $totalAmount = $subtotal + $shippingCost - $couponDiscount;
        
        // Create order DTO
        $orderData = [
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'user_id' => $user?->id,
            'sub_total' => $subtotal,
            'shipping_id' => $shippingId,
            'total_amount' => max(0, $totalAmount),
            'quantity' => $quantity,
            'payment_method' => $request->input('payment_method', 'cod'),
            'payment_status' => 'pending',
            'status' => 'pending',
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'address1' => $request->input('address1'),
            'address2' => $request->input('address2'),
            'post_code' => $request->input('post_code'),
        ];
        
        // Check if the payment method is PayPal and redirect to the payment route
        if ($request->input('payment_method') === 'paypal') {
            // Store order data in session for after PayPal payment
            session()->put('pending_order', $orderData);
            return redirect()->route('payment');
        }

        // Check if the payment method is Stripe and redirect to the Stripe route with the user ID
        if ($request->input('payment_method') === 'stripe') {
            // Store order data in session for after Stripe payment
            session()->put('pending_order', $orderData);
            return redirect()->route('stripe', Auth::id());
        }

        // For COD - create order immediately
        $dto = OrderDTO::fromArray($orderData);
        $order = $storeOrderAction->execute($dto);
        
        // Associate cart items with the order
        foreach ($cartItems as $cartItem) {
            $cartItem->update(['order_id' => $order->id]);
        }
        
        // Save address to user's address book if logged in
        if ($user && $request->has('save_address')) {
            $this->saveUserAddress($user, $request);
        }

        // Record coupon usage if coupon was applied
        if ($couponId) {
            $applyCouponAction->recordUsage(
                $couponId,
                $order->id,
                $user?->id,
                session()->getId(),
                $couponDiscount
            );
        }

        // Clear the cart and coupon from the session
        session()->forget('cart');
        session()->forget('coupon');
        session()->forget('pending_order');

        // Redirect to the home page with success message
        return redirect()->route('front.index')->with('success', 'Order placed successfully! Order number: ' . $order->order_number);
    }
    
    /**
     * Save address to user's address book.
     */
    private function saveUserAddress($user, Request $request): void
    {
        $user->addresses()->create([
            'type' => 'shipping',
            'is_default' => $request->has('make_default_address'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'phone' => $request->input('phone'),
            'country' => $request->input('country'),
            'city' => $request->input('city'),
            'address1' => $request->input('address1'),
            'address2' => $request->input('address2'),
            'post_code' => $request->input('post_code'),
        ]);
    }

    /**
     * Show cart page
     */
    public function cart(): View
    {
        return view(theme_view('pages.cart'), []);
    }

    /**
     * Add to cart
     */
    public function cartAdd(Request $request): RedirectResponse
    {
        // Implementation depends on cart service
        return redirect()->back()->with('success', 'Product added to cart');
    }

    /**
     * Show checkout page
     */
    public function checkout(): View
    {
        return view(theme_view('pages.checkout'), []);
    }

    /**
     * Process checkout
     */
    public function checkoutProcess(Request $request): RedirectResponse
    {
        // Implementation depends on order service
        return redirect()->route('front.index', ['locale' => app()->getLocale()]);
    }

    /**
     * Show blog post detail
     */
    public function postDetail(string $locale, string $slug, BlogDetailAction $blogDetailAction): View
    {
        return view(theme_view('pages.blog-detail'), $blogDetailAction($slug));
    }

    public function pages(string $locale, string $slug, PageDetailAction $pageDetailAction): View
    {
        return view(theme_view('pages.page'), $pageDetailAction($slug));
    }

    /**
     * Show banners on frontend (filtered by active status and optionally by category).
     */
    public function banners(Request $request): View
    {
        $categoryId = $request->query('category');
        $query = Banner::with('categories');
        if ($categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId): void {
                $q->where('categories.id', $categoryId);
            });
        }
        $banners = $query->get()->filter(fn ($b): bool => $b->isActive());

        return view(theme_view('banner'), ['banners' => $banners]);
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

    /**
     * Get available filters for search
     */
    private function getAvailableFilters(?string $query): array
    {
        $cacheKey = 'front_search_filters_'.md5(json_encode(['query' => $query]));

        return Cache::remember($cacheKey, 3600, function () use ($query) {
            $baseQuery = \Modules\Product\Models\Product::query();

            if ($query) {
                $baseQuery->where(function ($q) use ($query): void {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('summary', 'like', "%{$query}%")
                        ->orWhere('description', 'like', "%{$query}%");
                });
            }

            $baseQuery->where('status', 'active');

            // Get price range
            $priceRange = $baseQuery->selectRaw('MIN(price) as min_price, MAX(price) as max_price')->first();

            // Get available brands
            // Clone query to avoid modifying the base query for subsequent calls if any (though here we build new ones)
            $brandsQuery = clone $baseQuery;
            $brands = $brandsQuery->join('brands', 'products.brand_id', '=', 'brands.id')
                ->select('brands.id', 'brands.name')
                ->distinct()
                ->get();

            // Get available categories
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

    /**
     * Display user's order history.
     */
    public function myOrders(): View|RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('front.login')->with('message', 'Please login to view your orders.');
        }
        
        $orders = Order::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        
        return view(theme_view('pages.my-orders'), compact('orders'));
    }

    /**
     * Display order detail.
     */
    public function orderDetail(Order $order): View|RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user || $order->user_id !== $user->id) {
            return redirect()->route('front.my-orders')->with('error', 'Order not found.');
        }
        
        $order->load('carts.product');
        
        return view(theme_view('pages.order-detail'), compact('order'));
    }

    /**
     * Reorder a previous order.
     */
    public function reorder(Order $order, ReorderAction $reorderAction): RedirectResponse
    {
        $user = Auth::user();
        
        if (!$user || $order->user_id !== $user->id) {
            return redirect()->route('front.my-orders')->with('error', 'Order not found.');
        }
        
        $result = $reorderAction->execute($order->id, $user->id);
        
        if ($result['success']) {
            return redirect()->route('cart-list')->with('success', $result['message']);
        }
        
        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Display recently viewed products page.
     */
    public function recentlyViewed(RecentlyViewedService $recentlyViewedService): View
    {
        $products = $recentlyViewedService->getForCurrentUser(12);
        
        return view('front::pages.recently-viewed', compact('products'));
    }
}
