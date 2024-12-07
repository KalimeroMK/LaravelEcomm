<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Modules\Front\Http\Requests\ProductSearchRequest;
use Modules\Front\Service\FrontService;
use Modules\Newsletter\Models\Newsletter;

class FrontController extends Controller
{
    private FrontService $front_service;

    public function __construct(FrontService $frontService)
    {
        $this->front_service = $frontService;
    }

    /**
     * Resolve the view path dynamically based on the active theme.
     */
    private function resolveView(string $view, array $data = []): View
    {
        $theme = config('theme.active_theme', 'default');
        $themeViewPath = "front::$theme.$view";

        if (!view()->exists($themeViewPath)) {
            $themeViewPath = "front::default.$view";
        }

        return view($themeViewPath, $data);
    }

    public function index(): View
    {
        return $this->resolveView('index', $this->front_service->index());
    }

    public function aboutUs(): View
    {
        return $this->resolveView('pages.about-us');
    }

    public function contact(): View
    {
        return $this->resolveView('pages.contact');
    }

    public function productDetail(string $slug): View
    {
        return $this->resolveView('pages.product_detail', $this->front_service->productDetail($slug));
    }

    public function productGrids(): View
    {
        return $this->resolveView('pages.product-grids', $this->front_service->productGrids());
    }

    public function productLists(): View
    {
        return $this->resolveView('pages.product-lists', $this->front_service->productLists());
    }

    public function bundles(): View
    {
        return $this->resolveView('pages.bundles', $this->front_service->productBundles());
    }

    public function bundleDetail(string $slug): View
    {
        return $this->resolveView('pages.bundle_detail', $this->front_service->bundleDetail($slug));
    }

    public function productFilter(Request $request): RedirectResponse
    {
        return $this->front_service->productFilter($request->all());
    }

    public function productSearch(ProductSearchRequest $request): View
    {
        return $this->resolveView('pages.product-grids', $this->front_service->productSearch($request->validated()));
    }

    public function productDeal(): View
    {
        return $this->resolveView('pages.product-grids', $this->front_service->productDeal());
    }

    public function productBrand(Request $request): View
    {
        $viewType = request()->is('e-shop.loc/product-grids') ? 'product-grids' : 'product-lists';
        return $this->resolveView("pages.$viewType", $this->front_service->productBrand($request->all()));
    }

    public function productCat(string $slug): View
    {
        $viewType = request()->is('e-shop.loc/product-grids') ? 'product-grids' : 'product-lists';
        return $this->resolveView("pages.$viewType", $this->front_service->productCat($slug));
    }

    public function blog(): View
    {
        return $this->resolveView('pages.blog', $this->front_service->blog());
    }

    public function blogDetail(string $slug): View
    {
        return $this->resolveView('pages.blog-detail', $this->front_service->blogDetail($slug));
    }

    public function blogSearch(Request $request): View
    {
        return $this->resolveView('pages.blog', $this->front_service->blogSearch($request));
    }

    public function blogFilter(Request $request): RedirectResponse
    {
        $filterData = $this->front_service->blogFilter($request->all());
        return redirect()->route('blog', http_build_query($filterData));
    }

    public function blogByCategory(string $slug): View
    {
        return $this->resolveView('pages.blog', $this->front_service->blogByCategory($slug));
    }

    public function blogByTag(string $slug): View
    {
        return $this->resolveView('pages.blog', $this->front_service->blogByTag($slug));
    }

    public function couponStore(Request $request): RedirectResponse
    {
        return $this->front_service->couponStore($request);
    }

    public function subscribe(Request $request): RedirectResponse
    {
        $request->validate(['email' => 'required|email']);

        if (Newsletter::whereEmail($request->email)->exists()) {
            return redirect()->back()->with('message', 'Your email is already in our mailing list.');
        }

        $this->front_service->newsletter($request->all());
        return redirect()->back()->with('message', 'You have successfully subscribed.');
    }

    public function verifyNewsletter(string $token): RedirectResponse
    {
        $newsletter = Newsletter::where('token', $token)->first();

        if (!$newsletter) {
            return redirect()->back()->with('message', 'Invalid token.');
        }

        $this->front_service->validation($token);
        return redirect()->back()->with('message', 'Your email has been successfully validated.');
    }

    public function deleteNewsletter(string $token): RedirectResponse
    {
        $newsletter = Newsletter::where('token', $token)->first();

        if (!$newsletter) {
            return redirect()->back()->with('message', 'Invalid token.');
        }

        $this->front_service->deleteNewsletter($token);
        return redirect()->back()->with('message', 'Your email has been successfully deleted.');
    }

    public function store(Request $request): RedirectResponse
    {
        $paymentMethod = Arr::get($request, 'payment_method');

        switch ($paymentMethod) {
            case 'paypal':
                return redirect()->route('payment');

            case 'stripe':
                return redirect()->route('stripe', Auth::id());

            default:
                session()->forget(['cart', 'coupon']);
                return redirect()->route('front.index');
        }
    }

    public function pages(string $slug): View
    {
        return $this->resolveView('pages.page', $this->front_service->pages($slug));
    }
}
