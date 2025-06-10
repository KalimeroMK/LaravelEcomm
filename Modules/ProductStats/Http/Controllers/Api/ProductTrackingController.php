<?php

namespace Modules\ProductStats\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Modules\ProductStats\Events\ProductClicked;
use Modules\ProductStats\Events\ProductImpressionRecorded;
use Modules\ProductStats\Http\Requests\StoreClickRequest;
use Modules\ProductStats\Http\Requests\StoreImpressionsRequest;
use Modules\ProductStats\Models\ProductClick;
use Modules\ProductStats\Models\ProductImpression;

class ProductTrackingController extends Controller
{
    public function storeImpressions(StoreImpressionsRequest $request)
    {
        $userId = auth()->check() ? auth()->id() : null;
        $ip = $request->ip();
        $productIds = $request->input('product_ids');
        $impressions = [];
        foreach ($productIds as $productId) {
            $impression = ProductImpression::create([
                'product_id' => $productId,
                'user_id' => $userId,
                'ip_address' => $ip,
            ]);
            $impressions[] = $impression;
            event(new ProductImpressionRecorded($impression));
        }
        return response()->json(['success' => true, 'count' => count($impressions)]);
    }

    public function storeClick(StoreClickRequest $request)
    {
        $userId = auth()->check() ? auth()->id() : null;
        $ip = $request->ip();
        $productId = $request->input('product_id');
        $click = ProductClick::create([
            'product_id' => $productId,
            'user_id' => $userId,
            'ip_address' => $ip,
        ]);
        event(new ProductClicked($click));
        return response()->json(['success' => true]);
    }
}
