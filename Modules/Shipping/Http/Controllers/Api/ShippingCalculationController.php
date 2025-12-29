<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Shipping\Actions\CalculateShippingAction;

class ShippingCalculationController extends CoreController
{
    public function __construct(
        private readonly CalculateShippingAction $calculateAction
    ) {}

    public function calculate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'country' => 'nullable|string|max:2',
            'region' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:20',
            'order_total' => 'nullable|numeric|min:0',
        ]);

        $result = $this->calculateAction->execute(
            country: $validated['country'] ?? null,
            region: $validated['region'] ?? null,
            postalCode: $validated['postal_code'] ?? null,
            orderTotal: (float) ($validated['order_total'] ?? 0)
        );

        return $this
            ->setMessage('Shipping methods calculated successfully')
            ->respond($result);
    }
}
