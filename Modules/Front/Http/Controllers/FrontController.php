<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Front\Http\Requests\ProductSearchRequest;
use Modules\Front\Service\FrontService;
use Modules\Message\Http\Requests\Api\Store;
use Modules\Newsletter\Models\Newsletter;

class FrontController extends Controller
{
    private FrontService $front_service;

    public function __construct(FrontService $frontService)
    {
        $this->front_service = $frontService;
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('front::index', $this->front_service->index());
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
    public function productDetail(string $slug)
    {
        return view('front::pages.product_detail', $this->front_service->productDetail($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function productGrids()
    {
        return view('front::pages.product-grids', $this->front_service->productGrids());
    }

    /**
     * @return Application|Factory|View
     */
    public function bundles()
    {
        return view('front::pages.bundles', $this->front_service->productBundles());
    }

    public function bundleDetail(string $slug)
    {
        return view('front::pages.bundle_detail', $this->front_service->bundleDetail($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function productLists()
    {
        return view('front::pages.product-lists', $this->front_service->productLists());
    }

    public function productFilter(Request $request): RedirectResponse
    {
        return $this->front_service->productFilter($request->all());
    }

    /**
     * @return Application|Factory|View
     */
    public function productSearch(ProductSearchRequest $request)
    {
        return view('front::pages.product-grids', $this->front_service->productSearch($request->validated()));
    }

    /**
     * @return Application|Factory|View
     */
    public function productDeal()
    {
        return view('front::pages.product-grids', $this->front_service->productDeal());
    }

    /**
     * @return Application|Factory|View
     */
    public function productBrand(Request $request)
    {
        if (request()->is('e-shop.loc/product-grids')) {
            return view('front::pages.product-grids', $this->front_service->productBrand($request->all()));
        } else {
            return view('front::pages.product-lists', $this->front_service->productBrand($request->all()));
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function productCat(string $slug)
    {
        if (request()->is('e-shop.loc/product-grids')) {
            return view('front::pages.product-grids', $this->front_service->productCat($slug));
        } else {
            return view('front::pages.product-lists', $this->front_service->productCat($slug));
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function blog()
    {
        return view('front::pages.blog', $this->front_service->blog());
    }

    /**
     * @return Application|Factory|View
     */
    public function blogDetail(string $slug)
    {
        return view('front::pages.blog-detail', $this->front_service->blogDetail($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function blogSearch(Request $request)
    {
        return view('front::pages.blog', $this->front_service->blogSearch($request));
    }

    public function blogFilter(Request $request): RedirectResponse
    {
        $filterData = $this->front_service->blogFilter($request->all());

        return redirect()->route('blog', http_build_query($filterData));
    }

    /**
     * @return Application|Factory|View
     */
    public function blogByCategory(string $slug)
    {
        return view('front::pages.blog', $this->front_service->blogByCategory($slug));
    }

    /**
     * Display the blog posts filtered by tag.
     *
     * @param  string  $slug  The tag slug to filter blog posts by.
     * @return View The view displaying the filtered blog posts.
     */
    public function blogByTag(string $slug): View
    {
        return view('front::pages.blog', $this->front_service->blogByTag($slug));
    }

    public function couponStore(Request $request): RedirectResponse
    {
        return $this->front_service->couponStore($request);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        if (Newsletter::whereEmail($request->email) !== null) {
            $this->front_service->newsletter($request->all());

            return redirect()->back()->with('message', 'Your comment successfully send.');
        }

        return redirect()->back()->with('message', 'Your email is already in our mailing list.');
    }

    public function verifyNewsletter(string $token): string
    {
        if (Newsletter::where('token', $token)->first() !== null) {
            $this->front_service->validation($token);

            return redirect()->back()->with('message', 'Your email is successfully validated.');
        }

        return redirect()->back()->with('message', 'token mismatch ');
    }

    public function deleteNewsletter(string $token): string
    {
        if (Newsletter::where('token', $token)->first() !== null) {
            $this->front_service->deleteNewsletter($token);

            return redirect()->back()->with('message', 'Your email is successfully deleted.');
        }

        return redirect()->back()->with('message', 'token mismatch ');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function messageStore(Store $request): ?string
    {
        return $this->front_service->messageStore($request);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Check if the payment method is PayPal and redirect to the payment route
        if (Arr::get($request, 'payment_method') == 'paypal') {
            return redirect()->route('payment');
        }

        // Check if the payment method is Stripe and redirect to the Stripe route with the user ID
        if (Arr::get($request, 'payment_method') == 'stripe') {
            return redirect()->route('stripe', Auth::id());
        }

        // Clear the cart and coupon from the session
        session()->forget('cart');
        session()->forget('coupon');

        // Redirect to the home page
        return redirect()->route('front.index');
    }

    public function pages(string $slug): View
    {
        return view('front::pages.page', $this->front_service->pages($slug));
    }
}
