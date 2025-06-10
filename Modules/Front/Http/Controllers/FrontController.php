<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
        return view('front::pages.about-us');
    }

    /**
     * @return Application|Factory|View
     */
    public function contact()
    {
        return view('front::pages.contact');
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
}
