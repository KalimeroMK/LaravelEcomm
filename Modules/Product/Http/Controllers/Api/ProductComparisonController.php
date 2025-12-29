<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers\Api;

use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Product\Actions\AddProductToComparisonAction;
use Modules\Product\Actions\ClearProductComparisonAction;
use Modules\Product\Actions\GetProductComparisonAction;
use Modules\Product\Actions\RemoveProductFromComparisonAction;
use Modules\Product\Http\Resources\ProductResource;

class ProductComparisonController extends CoreController
{
    /**
     * Add a product to comparison list.
     */
    public function addToCompare(int $productId, Request $request): JsonResponse
    {
        $action = new AddProductToComparisonAction(
            $this->getStorageClosure($request),
            $this->putStorageClosure($request)
        );

        $result = $action->execute($productId);

        return $this
            ->setMessage('Product added to comparison list.')
            ->respond([
                'product_id' => $result['product_id'],
                'comparison_count' => $result['comparison_count'],
            ]);
    }

    /**
     * Remove a product from comparison list.
     */
    public function removeFromCompare(int $productId, Request $request): JsonResponse
    {
        $action = new RemoveProductFromComparisonAction(
            $this->getStorageClosure($request),
            $this->putStorageClosure($request)
        );

        $result = $action->execute($productId);

        return $this
            ->setMessage('Product removed from comparison list.')
            ->respond([
                'product_id' => $result['product_id'],
                'comparison_count' => $result['comparison_count'],
            ]);
    }

    /**
     * Show comparison list.
     */
    public function showComparison(Request $request): JsonResponse
    {
        $action = new GetProductComparisonAction($this->getStorageClosure($request));
        $products = $action->execute();

        return $this
            ->setMessage('Comparison list retrieved successfully.')
            ->respond([
                'products' => ProductResource::collection($products),
                'count' => $products->count(),
                'max_allowed' => 4,
            ]);
    }

    /**
     * Clear comparison list.
     */
    public function clearComparison(Request $request): JsonResponse
    {
        $action = new ClearProductComparisonAction($this->clearStorageClosure($request));
        $action->execute();

        return $this
            ->setMessage('Comparison list cleared successfully.')
            ->respond(null);
    }

    /**
     * Get comparison key for user.
     */
    private function getComparisonKey(Request $request): string
    {
        return 'product_comparison_'.($request->user()?->id ?? $request->ip());
    }

    /**
     * Get storage closure for Cache.
     */
    private function getStorageClosure(Request $request): Closure
    {
        $key = $this->getComparisonKey($request);

        return fn () => Cache::get($key, []);
    }

    /**
     * Put storage closure for Cache.
     */
    private function putStorageClosure(Request $request): Closure
    {
        $key = $this->getComparisonKey($request);

        return function (array $compare): void {
            Cache::put($key, $compare, now()->addDays(30));
        };
    }

    /**
     * Clear storage closure for Cache.
     */
    private function clearStorageClosure(Request $request): Closure
    {
        $key = $this->getComparisonKey($request);

        return fn () => Cache::forget($key);
    }
}
