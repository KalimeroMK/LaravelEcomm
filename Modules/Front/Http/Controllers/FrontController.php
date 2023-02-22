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
use JetBrains\PhpStorm\NoReturn;
use Modules\Front\Service\FrontService;
use Modules\Message\Http\Requests\Api\Store;
use Modules\Newsletter\Models\Newsletter;

class FrontController extends Controller
{

    private FrontService $front_service;

    public function __construct()
    {
        $this->front_service = new FrontService();
    }

    /**
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('front::index')->with($this->front_service->index());
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
     * @param $slug
     *
     * @return Application|Factory|View
     */
    public function productDetail($slug)
    {
        return view('front::pages.product_detail')->with($this->front_service->productDetail($slug));
    }

    /**
     * @return Application|Factory|View
     */
    public function productGrids()
    {
        return view('front::pages.product-grids')->with($this->front_service->productGrids());
    }

    /**
     * @return Application|Factory|View
     */
    public function productLists()
    {
        return view('front::pages.product-lists')->with($this->front_service->productLists());
    }

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function productFilter(Request $request): RedirectResponse
    {
        return $this->front_service->productFilter($request);
    }

    /**
     * @param  Request  $request
     *
     * @return Application|Factory|View
     */
    public function productSearch(Request $request)
    {
        return view('front::pages.product-grids')->with($this->front_service->productSearch($request));
    }

    /**
     * @return Application|Factory|View
     */
    public function productDeal()
    {
        return view('front::pages.product-grids')->with($this->front_service->productDeal());
    }

    /**
     * @param  Request  $request
     *
     * @return Application|Factory|View
     */
    public function productBrand(Request $request)
    {
        if (request()->is('e-shop.loc/product-grids')) {
            return view('front::pages.product-grids')->with($this->front_service->productBrand($request));
        } else {
            return view('front::pages.product-lists')->with($this->front_service->productBrand($request));
        }
    }

    /**
     * @param $slug
     *
     * @return Application|Factory|View
     */
    public function productCat($slug)
    {
        if (request()->is('e-shop.loc/product-grids')) {
            return view('front::pages.product-grids')->with($this->front_service->productCat($slug));
        } else {
            return view('front::pages.product-lists')->with($this->front_service->productCat($slug));
        }
    }

    /**
     * @return Application|Factory|View
     */
    public function blog()
    {
        return view('front::pages.blog')->with($this->front_service->blog());
    }

    /**
     * @param $slug
     *
     * @return Application|Factory|View
     */
    public function blogDetail($slug)
    {
        return view('front::pages.blog-detail')->with($this->front_service->blogDetail($slug));
    }

    /**
     * @param  Request  $request
     *
     * @return Application|Factory|View
     */
    public function blogSearch(Request $request)
    {
        return view('front::pages.blog')->with($this->front_service->blogSearch($request));
    }

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function blogFilter(Request $request): RedirectResponse
    {
        return $this->front_service->blogFilter($request);
    }

    /**
     * @param  Request  $request
     *
     * @return Application|Factory|View
     */
    public function blogByCategory(Request $request)
    {
        return view('front::pages.blog')->with($this->front_service->blogByCategory($request));
    }

    /**
     * @param  Request  $request
     *
     * @return Application|Factory|View
     */
    public function blogByTag(Request $request)
    {
        return view('front::pages.blog')->with($this->front_service->blogByTag($request));
    }

    /**
     * @return Application|Factory|View
     */

    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function couponStore(Request $request): RedirectResponse
    {
        return $this->front_service->couponStore($request);
    }

    /**
     * @param  Request  $request
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
     * @param $token
     *
     * @return string
     */
    public function verifyNewsletter($token): string
    {
        if (Newsletter::where('token', $token)->first() !== null) {
            $this->front_service->validation((int)['id' => Newsletter::where('token', $token)->first()->id]);

            return redirect()->back()->with('message', "Your email is successfully validated.");
        }

        return redirect()->back()->with('message', "token mismatch ");
    }

    public function deleteNewsletter($token)
    {
        if (Newsletter::where('token', $token)->first() !== null) {
            $this->front_service->deleteNewsletter((int)['id' => Newsletter::where('token', $token)->first()->id]);

            return redirect()->back()->with('message', "Your email is successfully deleted.");
        }

        return redirect()->back()->with('message', "token mismatch ");
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     *
     * @return string|null
     */
    #[NoReturn] public function messageStore(Store $request): string|null
    {
        return $this->front_service->messageStore($request);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
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
        return redirect()->route('home');
    }

}
