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
     * @param string $slug
     *
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

    /**
     * @return Application|Factory|View
     */
    public function productLists()
    {
        return view('front::pages.product-lists', $this->front_service->productLists());
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function productFilter(Request $request): RedirectResponse
    {
        return $this->front_service->productFilter($request);
    }

    /**
     * @param ProductSearchRequest $request
     *
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
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function productBrand(Request $request)
    {
        if (request()->is('e-shop.loc/product-grids')) {
            return view('front::pages.product-grids', $this->front_service->productBrand($request));
        } else {
            return view('front::pages.product-lists', $this->front_service->productBrand($request));
        }
    }

    /**
     * @param string $slug
     *
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
     * @param string $slug
     *
     * @return Application|Factory|View
     */
    public function blogDetail(string $slug)
    {
        return view('front::pages.blog-detail', $this->front_service->blogDetail($slug));
    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function blogSearch(Request $request)
    {
        return view('front::pages.blog', $this->front_service->blogSearch($request));
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function blogFilter(Request $request): RedirectResponse
    {
        $filterData = $this->front_service->blogFilter($request);

        return redirect()->route('blog', http_build_query($filterData));
    }

    /**
     * @param Request $request
     *
     * @return Application|Factory|View
     */
    public function blogByCategory($slug)
    {
        return view('front::pages.blog', $this->front_service->blogByCategory($slug));
    }

    /**
     * @param $slug
     * @return Application|Factory|View
     */
    public function blogByTag($slug)
    {
        return view('front::pages.blog', $this->front_service->blogByTag($slug));
    }

    /**
     * @return Application|Factory|View
     */

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function couponStore(Request $request): RedirectResponse
    {
        return $this->front_service->couponStore($request);
    }

    /**
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function subscribe(Request $request): RedirectResponse
    {
        if (Newsletter::whereEmail($request->email) !== null) {
            $this->front_service->newsletter($request->all());

            return redirect()->back()->with('message', "Your comment successfully send.");
        }

        return redirect()->back()->with('message', "Your email is already in our mailing list.");
    }

    /**
     * @param string $token
     *
     * @return string
     */
    public function verifyNewsletter(string $token): string
    {
        if (Newsletter::where('token', $token)->first() !== null) {
            $this->front_service->validation($token);

            return redirect()->back()->with('message', "Your email is successfully validated.");
        }

        return redirect()->back()->with('message', "token mismatch ");
    }

    public function deleteNewsletter(string $token): string
    {
        if (Newsletter::where('token', $token)->first() !== null) {
            $this->front_service->deleteNewsletter($token);

            return redirect()->back()->with('message', "Your email is successfully deleted.");
        }

        return redirect()->back()->with('message', "token mismatch ");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Store $request
     *
     * @return string|null
     */
    public function messageStore(Store $request): string|null
    {
        return $this->front_service->messageStore($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     *
     * @return RedirectResponse
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

}
