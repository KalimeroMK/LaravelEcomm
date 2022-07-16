<?php

namespace Modules\Front\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use JetBrains\PhpStorm\NoReturn;
use Modules\Front\Service\FrontService;
use Modules\Message\Http\Requests\Store;

class FrontController extends Controller
{
    
    private FrontService $front_service;
    
    public function __construct() { $this->front_service = new FrontService(); }
    
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
    
    public function subscribe(Request $request): RedirectResponse
    {
        return $this->front_service->subscribe($request);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     *
     * @return Response
     */
    #[NoReturn] public function messageStore(Store $request): Response
    {
        return $this->front_service->messageStore($request);
    }
}
