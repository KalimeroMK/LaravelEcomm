<?php

declare(strict_types=1);

namespace Modules\ProductStats\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\ProductStats\Actions\GetAllProductStatsAction;
use Modules\ProductStats\Actions\GetProductDetailStatsAction;

class ProductStatsController extends CoreController
{
    public function __construct(
        private readonly GetAllProductStatsAction $getAllAction,
        private readonly GetProductDetailStatsAction $getDetailAction
    ) {}

    /**
     * Get all product stats with filters
     */
    public function index(Request $request): JsonResponse
    {
        $filters = [
            'from' => $request->input('from'),
            'to' => $request->input('to'),
            'category_id' => $request->input('category_id'),
            'order_by' => $request->input('order_by', 'id'),
            'sort' => $request->input('sort', 'desc'),
        ];

        $stats = $this->getAllAction->execute($filters);

        return $this
            ->setMessage('Product stats retrieved successfully.')
            ->respond($stats);
    }

    /**
     * Get detailed stats for a specific product
     */
    public function detail(int $id, Request $request): JsonResponse
    {
        $from = $request->input('from');
        $to = $request->input('to');

        $data = $this->getDetailAction->execute($id, $from, $to);

        return $this
            ->setMessage('Product detail stats retrieved successfully.')
            ->respond([
                'product' => $data['product'],
                'impressions' => $data['impressions'],
                'clicks' => $data['clicks'],
                'stats' => $data['stats'],
            ]);
    }
}
