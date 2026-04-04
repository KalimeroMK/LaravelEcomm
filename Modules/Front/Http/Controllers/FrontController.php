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
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Services\WishlistService;
use Modules\Front\Actions\BlogAction;
use Modules\Front\Actions\BlogByCategoryAction;
use Modules\Front\Actions\BlogByTagAction;
use Modules\Front\Actions\BlogDetailAction;
use Modules\Front\Actions\BlogFilterAction;
use Modules\Front\Actions\BlogSearchAction;
use Modules\Front\Actions\BundleDetailAction;
use Modules\Front\Actions\GetAdvancedSearchAction;
use Modules\Front\Actions\GetBannersAction;
use Modules\Front\Actions\GetCategoriesAction;
use Modules\Front\Actions\GetSearchSuggestionsAction;
use Modules\Front\Actions\IndexAction;
use Modules\Front\Actions\MessageStoreAction;
use Modules\Front\Actions\NewsletterDeleteAction;
use Modules\Front\Actions\NewsletterSubscribeAction;
use Modules\Front\Actions\NewsletterVerifyAction;
use Modules\Front\Actions\PageDetailAction;
use Modules\Front\Actions\ProcessCheckoutAction;
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
use Modules\Order\Actions\ReorderAction;
use Modules\Order\Http\Requests\Store as OrderStoreRequest;
use Modules\Order\Models\Order;
use Modules\Message\Http\Requests\Store;
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
    public function categories(GetCategoriesAction $getCategoriesAction): Factory|View
    {
        return view(theme_view('pages.categories'), $getCategoriesAction());
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
    public function advancedSearch(Request $request, GetAdvancedSearchAction $getAdvancedSearchAction): View
    {
        return view(theme_view('pages.advanced-search'), $getAdvancedSearchAction(
            $request->input('query', ''),
            $request->only(['price_min', 'price_max', 'brand', 'categories', 'status', 'in_stock', 'sort_by'])
        ));
    }

    public function searchSuggestions(Request $request, GetSearchSuggestionsAction $getSearchSuggestionsAction): JsonResponse
    {
        $query = $request->input('query', '');

        if (mb_strlen($query) < 2) {
            return response()->json(['suggestions' => []]);
        }

        return response()->json($getSearchSuggestionsAction($query));
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
        $limit   = min($request->input('limit', 8), 20);

        $relatedProducts = $recommendationService->getContentBasedRecommendations($product, $limit);

        return view(theme_view('pages.related-products'), [
            'product'         => $product,
            'relatedProducts' => $relatedProducts,
            'totalCount'      => $relatedProducts->count(),
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
     * Store a newly created order in storage.
     * All business logic is delegated to ProcessCheckoutAction.
     */
    public function store(OrderStoreRequest $request, ProcessCheckoutAction $processCheckoutAction): RedirectResponse
    {
        return $processCheckoutAction->execute($request);
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
    public function banners(Request $request, GetBannersAction $getBannersAction): View
    {
        $categoryId = $request->query('category') ? (int) $request->query('category') : null;

        return view(theme_view('banner'), $getBannersAction($categoryId));
    }

    public function bannerImpression(int $id): JsonResponse
    {
        $banner = \Modules\Banner\Models\Banner::findOrFail($id);
        $banner->incrementImpression();

        return response()->json(['success' => true]);
    }

    public function myOrders(): View|RedirectResponse
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('front.login')->with('message', 'Please login to view your orders.');
        }

        $orders = Order::where('user_id', $user->id)
            ->with(['carts.product', 'shipping'])
            ->orderByDesc('created_at')
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
