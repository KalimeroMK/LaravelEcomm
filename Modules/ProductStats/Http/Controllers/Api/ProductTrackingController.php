<?php

declare(strict_types=1);

namespace Modules\ProductStats\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\ProductStats\Actions\StoreProductClickAction;
use Modules\ProductStats\Actions\StoreProductImpressionAction;
use Modules\ProductStats\Http\Requests\StoreClickRequest;
use Modules\ProductStats\Http\Requests\StoreImpressionsRequest;

class ProductTrackingController extends CoreController
{
    public function __construct(
        private readonly StoreProductImpressionAction $storeImpressionAction,
        private readonly StoreProductClickAction $storeClickAction
    ) {
        // Intentionally no auth middleware: impression/click tracking must work
        // for anonymous storefront visitors (user_id is nullable, IP is stored,
        // and the `api` group's throttle applies). The old auth:sanctum here
        // 401'd every tracking call, so no stats were ever recorded.
    }

    public function storeImpressions(StoreImpressionsRequest $request): JsonResponse
    {
        $userId = auth()->check() ? auth()->id() : null;
        $ip = $request->ip();
        $productIds = $request->input('product_ids');

        $impressions = $this->storeImpressionAction->execute($productIds, $userId, $ip);

        return $this
            ->setMessage('Impressions recorded successfully.')
            ->respond(['count' => $impressions->count()]);
    }

    public function storeClick(StoreClickRequest $request): JsonResponse
    {
        $userId = auth()->check() ? auth()->id() : null;
        $ip = $request->ip();
        $productId = $request->input('product_id');

        $this->storeClickAction->execute($productId, $userId, $ip);

        return $this
            ->setMessage('Click recorded successfully.')
            ->respond(null);
    }
}
