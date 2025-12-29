<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Barryvdh\DomPDF\Facade\Pdf;
use Modules\Order\Models\Order;

readonly class GenerateOrderPdfAction
{
    public function execute(int $orderId): \Barryvdh\DomPDF\PDF
    {
        $order = Order::with(['user', 'carts.product', 'shipping'])->findOrFail($orderId);

        return Pdf::loadView('order::pdf', ['order' => $order]);
    }
}
