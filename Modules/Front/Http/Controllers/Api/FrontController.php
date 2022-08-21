<?php

namespace Modules\Front\Http\Controllers\Api;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json($this->front_service->index(), 200);
    }
    
    /**
     * @param $slug
     *
     * @return JsonResponse
     */
    public function productDetail($slug)
    {
        return response()->json($this->front_service->productDetail($slug), 200);
    }
    
    /**
     * @return JsonResponse
     */
    public function productGrids()
    {
        return response()->json($this->front_service->productGrids(), 200);
    }
    
    /**
     * @return JsonResponse
     */
    public function productLists()
    {
        return response()->json($this->front_service->productLists(), 200);
    }
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function productFilter(Request $request): JsonResponse
    {
        return response()->json($this->front_service->productFilter($request), 200);
    }
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function productSearch(Request $request)
    {
        return response()->json($this->front_service->productSearch($request), 200);
    }
    
    /**
     * @return JsonResponse
     */
    public function productDeal()
    {
        return response()->json($this->front_service->productDeal(), 200);
    }
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function productBrand(Request $request)
    {
        return response()->json($this->front_service->productBrand($request), 200);
    }
    
    /**
     * @param $slug
     *
     * @return JsonResponse
     */
    public function productCat($slug)
    {
        return response()->json($this->front_service->productCat($slug), 200);
    }
    
    /**
     * @return JsonResponse
     */
    public function blog()
    {
        return response()->json($this->front_service->blog(), 200);
    }
    
    /**
     * @param $slug
     *
     * @return JsonResponse
     */
    public function blogDetail($slug)
    {
        return response()->json($this->front_service->blogDetail($slug), 200);
    }
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function blogSearch(Request $request)
    {
        return response()->json($this->front_service->blogSearch($request), 200);
    }
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function blogFilter(Request $request): JsonResponse
    {
        return response()->json($this->front_service->blogFilter($request->all()), 200);
    }
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function blogByCategory(Request $request)
    {
        return response()->json($this->front_service->blogByCategory($request), 200);
    }
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function blogByTag(Request $request)
    {
        return response()->json($this->front_service->blogByTag($request), 200);
    }
    
    /**
     * @return Application|Factory|View
     */
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function couponStore(Request $request): JsonResponse
    {
        return response()->json($this->front_service->couponStore($request), 200);
    }
    
    /**
     * @param  Request  $request
     *
     * @return JsonResponse
     */
    public function subscribe(Request $request): JsonResponse
    {
        if (Newsletter::whereEmail($request->email) !== null) {
            return response()->json($this->front_service->newsletter($request->all(), 200), 200);
        }
        
        return response()->json('Email already present in the database ', 200);
    }
    
    /**
     * @param $token
     *
     * @return string
     */
    public function verifyNewsletter($token): string
    {
        if (Newsletter::where('token', $token)->first() !== null) {
            $this->front_service->validation(['id' => Newsletter::where('token', $token)->first()->id]);
            
            return redirect()->back()->with('message', "Your email is successfully validated.");
        }
        
        return redirect()->back()->with('message', "token mismatch ");
    }
    
    public function deleteNewsletter($token)
    {
        if (Newsletter::where('token', $token)->first() !== null) {
            $this->front_service->deleteNewsletter(['id' => Newsletter::where('token', $token)->first()->id]);
            
            return response()->json('Your email is successfully deleted ', 200);
        }
        
        return response()->json('token mismatch  ', 500);
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
        $data = $this->front_service->orderStore($request->all());
        
        if (request('payment_method') == 'paypal') {
            return redirect()->route('payment')->with($data[0], $data[1]);
        } elseif (request('payment_method') == 'stripe') {
            return redirect()->route('stripe')->with($data);
        } else {
            session()->forget('cart');
            session()->forget('coupon');
        }
        
        request()->session()->flash('success', 'Your product successfully placed in order');
        
        return redirect()->route('home');
    }
}
