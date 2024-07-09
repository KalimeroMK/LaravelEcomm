<?php

namespace Modules\Front\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
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

    public function index(): JsonResponse
    {
        return response()->json($this->front_service->index(), 200);
    }

    /**
     * @return JsonResponse
     */
    public function productDetail(string $slug)
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

    public function productFilter(Request $request): JsonResponse
    {
        return response()->json($this->front_service->productFilter($request->all()), 200);
    }

    /**
     * @return JsonResponse
     */
    public function productSearch(Request $request)
    {
        return response()->json($this->front_service->productSearch($request->all()), 200);
    }

    /**
     * @return JsonResponse
     */
    public function productDeal()
    {
        return response()->json($this->front_service->productDeal(), 200);
    }

    /**
     * @return JsonResponse
     */
    public function productBrand(Request $request)
    {
        return response()->json($this->front_service->productBrand($request->all()), 200);
    }

    /**
     * @return JsonResponse
     */
    public function productCat(string $slug)
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
     * @return JsonResponse
     */
    public function blogDetail(string $slug)
    {
        return response()->json($this->front_service->blogDetail($slug), 200);
    }

    /**
     * @return JsonResponse
     */
    public function blogSearch(Request $request)
    {
        return response()->json($this->front_service->blogSearch($request), 200);
    }

    public function blogFilter(Request $request): JsonResponse
    {
        return response()->json($this->front_service->blogFilter($request->all()), 200);
    }

    /**
     * @return JsonResponse
     */
    public function blogByCategory(Request $request)
    {
        return response()->json($this->front_service->blogByCategory($request), 200);
    }

    /**
     * @return JsonResponse
     */
    public function blogByTag(Request $request)
    {
        return response()->json($this->front_service->blogByTag($request), 200);
    }

    /**
     * @param  Request  $request
     * @return JsonResponse
     */
    public function couponStore(Request $request): JsonResponse
    {
        return response()->json($this->front_service->couponStore($request), 200);
    }

    public function subscribe(Request $request): JsonResponse
    {
        if (Newsletter::whereEmail($request->email) !== null) {
            return response()->json($this->front_service->newsletter($request->all()), 200);
        }

        return response()->json('Email already present in the database ', 200);
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

            return response()->json('Your email is successfully deleted ', 200);
        }

        return response()->json('token mismatch  ', 500);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function messageStore(Store $request): ?string
    {
        return $this->front_service->messageStore($request);
    }
}
