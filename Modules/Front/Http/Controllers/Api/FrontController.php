<?php

declare(strict_types=1);

namespace Modules\Front\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Front\Actions\BlogAction;
use Modules\Front\Actions\BlogByCategoryAction;
use Modules\Front\Actions\BlogByTagAction;
use Modules\Front\Actions\BlogDetailAction;
use Modules\Front\Actions\BlogFilterAction;
use Modules\Front\Actions\BlogSearchAction;
use Modules\Front\Actions\CouponStoreAction;
use Modules\Front\Actions\IndexAction;
use Modules\Front\Actions\MessageStoreAction;
use Modules\Front\Actions\ProductBrandAction;
use Modules\Front\Actions\ProductCatAction;
use Modules\Front\Actions\ProductDealAction;
use Modules\Front\Actions\ProductDetailAction;
use Modules\Front\Actions\ProductFilterAction;
use Modules\Front\Actions\ProductGridsAction;
use Modules\Front\Actions\ProductListsAction;
use Modules\Front\Actions\ProductSearchAction;
use Modules\Message\Http\Requests\Api\Store;
use Modules\Newsletter\Http\Requests\Store as NewsletterStoreRequest;
use Modules\Newsletter\Models\Newsletter;

class FrontController extends CoreController
{
    public function index(IndexAction $indexAction): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $indexAction(),
        ]);
    }

    public function productDetail(string $slug, ProductDetailAction $productDetailAction): JsonResponse
    {
        return response()->json($productDetailAction($slug));
    }

    public function productGrids(ProductGridsAction $productGridsAction): JsonResponse
    {
        return response()->json($productGridsAction());
    }

    public function productLists(ProductListsAction $productListsAction): JsonResponse
    {
        return response()->json($productListsAction());
    }

    public function productFilter(Request $request, ProductFilterAction $productFilterAction): JsonResponse
    {
        return response()->json($productFilterAction($request->all()));
    }

    public function productSearch(Request $request, ProductSearchAction $productSearchAction): JsonResponse
    {
        return response()->json($productSearchAction($request->all()));
    }

    public function productDeal(ProductDealAction $productDealAction): JsonResponse
    {
        return response()->json($productDealAction());
    }

    public function productBrand(string $slug, ProductBrandAction $productBrandAction): JsonResponse
    {
        return response()->json($productBrandAction($slug));
    }

    public function productCat(string $slug, ProductCatAction $productCatAction): JsonResponse
    {
        return response()->json($productCatAction($slug));
    }

    public function blog(BlogAction $blogAction): JsonResponse
    {
        return response()->json($blogAction());
    }

    public function blogDetail(string $slug, BlogDetailAction $blogDetailAction): JsonResponse
    {
        return response()->json($blogDetailAction($slug));
    }

    public function blogSearch(Request $request, BlogSearchAction $blogSearchAction): JsonResponse
    {
        return response()->json($blogSearchAction($request));
    }

    public function blogFilter(Request $request, BlogFilterAction $blogFilterAction): JsonResponse
    {
        return response()->json($blogFilterAction($request->all()));
    }

    public function blogByCategory(string $slug, BlogByCategoryAction $blogByCategoryAction): JsonResponse
    {
        return response()->json($blogByCategoryAction($slug));
    }

    public function blogByTag(string $slug, BlogByTagAction $blogByTagAction): JsonResponse
    {
        return response()->json($blogByTagAction($slug));
    }

    public function couponStore(Request $request, CouponStoreAction $couponStoreAction): JsonResponse
    {
        return response()->json($couponStoreAction($request));
    }

    public function subscribe(NewsletterStoreRequest $request): JsonResponse
    {
        if (Newsletter::whereEmail($request->email)->exists()) {
            return response()->json('Email already present in the database', 409);
        }

        // Dummy action – заменете со NewsletterSubscribeAction ако имате
        return response()->json('Subscribed successfully', 200);
    }

    public function verifyNewsletter(string $token): JsonResponse
    {
        // TODO: создади NewsletterVerifyAction
        return response()->json(['message' => 'Verified'], 200);
    }

    public function deleteNewsletter(string $token): JsonResponse
    {
        // TODO: создади NewsletterDeleteAction
        return response()->json(['message' => 'Deleted'], 200);
    }

    public function messageStore(Store $request, MessageStoreAction $messageStoreAction): JsonResponse
    {
        return response()->json($messageStoreAction($request));
    }
}
