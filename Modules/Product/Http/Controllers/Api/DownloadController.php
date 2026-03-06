<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Order\Models\Order;
use Modules\Product\Models\OrderDownload;
use Modules\Product\Models\ProductDownload;

class DownloadController extends CoreController
{
    /**
     * Get downloadable files for authenticated user.
     */
    public function index(): JsonResponse
    {
        $downloads = OrderDownload::with(['productDownload.product', 'order'])
            ->forUser(auth()->id())
            ->valid()
            ->orderBy('last_downloaded_at', 'desc')
            ->get()
            ->map(function ($orderDownload) {
                return [
                    'id' => $orderDownload->id,
                    'product_id' => $orderDownload->productDownload->product->id,
                    'product_title' => $orderDownload->productDownload->product->title,
                    'file_name' => $orderDownload->productDownload->file_name,
                    'file_size' => $orderDownload->productDownload->formatted_file_size,
                    'order_id' => $orderDownload->order_id,
                    'order_number' => $orderDownload->order->order_number,
                    'downloads_count' => $orderDownload->downloads_count,
                    'max_downloads' => $orderDownload->productDownload->product->max_downloads,
                    'can_download' => $orderDownload->canDownload(),
                    'expires_at' => $orderDownload->expires_at,
                    'download_url' => $orderDownload->canDownload() 
                        ? $orderDownload->productDownload->getDownloadUrl($orderDownload->order_id, auth()->id())
                        : null,
                ];
            });

        return $this
            ->setMessage('Downloads retrieved successfully.')
            ->respond(['downloads' => $downloads]);
    }

    /**
     * Get download history for authenticated user.
     */
    public function history(): JsonResponse
    {
        $history = OrderDownload::with(['productDownload.product'])
            ->forUser(auth()->id())
            ->whereNotNull('last_downloaded_at')
            ->orderBy('last_downloaded_at', 'desc')
            ->paginate(20);

        return $this
            ->setMessage('Download history retrieved successfully.')
            ->respond([
                'data' => $history->map(function ($orderDownload) {
                    return [
                        'id' => $orderDownload->id,
                        'product_title' => $orderDownload->productDownload->product->title,
                        'file_name' => $orderDownload->productDownload->file_name,
                        'downloads_count' => $orderDownload->downloads_count,
                        'last_downloaded_at' => $orderDownload->last_downloaded_at,
                    ];
                }),
                'meta' => [
                    'current_page' => $history->currentPage(),
                    'last_page' => $history->lastPage(),
                    'per_page' => $history->perPage(),
                    'total' => $history->total(),
                ],
            ]);
    }

    /**
     * Get download links for a specific order.
     */
    public function orderDownloads(int $orderId): JsonResponse
    {
        $order = Order::findOrFail($orderId);

        // Check user owns this order
        if ($order->user_id !== auth()->id()) {
            return $this
                ->setMessage('Unauthorized access to this order.')
                ->setStatusCode(403)
                ->respond(null);
        }

        $downloads = [];

        foreach ($order->carts as $cart) {
            $product = $cart->product;

            if (!$product || !$product->isDownloadable()) {
                continue;
            }

            foreach ($product->activeDownloads as $download) {
                $orderDownload = OrderDownload::firstOrCreate(
                    [
                        'order_id' => $order->id,
                        'product_download_id' => $download->id,
                        'user_id' => auth()->id(),
                    ],
                    [
                        'expires_at' => $product->download_expiry_days
                            ? now()->addDays($product->download_expiry_days)
                            : null,
                    ]
                );

                $downloads[] = [
                    'product_id' => $product->id,
                    'product_title' => $product->title,
                    'file_name' => $download->file_name,
                    'file_size' => $download->formatted_file_size,
                    'downloads_count' => $orderDownload->downloads_count,
                    'max_downloads' => $product->max_downloads,
                    'can_download' => $orderDownload->canDownload(),
                    'expires_at' => $orderDownload->expires_at,
                    'download_url' => $orderDownload->canDownload()
                        ? $download->getDownloadUrl($order->id, auth()->id())
                        : null,
                ];
            }
        }

        return $this
            ->setMessage('Order downloads retrieved successfully.')
            ->respond([
                'order_id' => $order->id,
                'payment_status' => $order->payment_status,
                'downloads' => $downloads,
            ]);
    }

    /**
     * Verify download access.
     */
    public function verify(ProductDownload $download, Order $order): JsonResponse
    {
        // Check user owns this order
        if ($order->user_id !== auth()->id()) {
            return $this
                ->setMessage('Unauthorized access.')
                ->setStatusCode(403)
                ->respond(null);
        }

        // Check order is paid
        if ($order->payment_status !== 'paid') {
            return $this
                ->setMessage('Payment required.')
                ->setStatusCode(403)
                ->respond(null);
        }

        $orderDownload = OrderDownload::where([
            'order_id' => $order->id,
            'product_download_id' => $download->id,
            'user_id' => auth()->id(),
        ])->first();

        if (!$orderDownload) {
            return $this
                ->setMessage('Download not found.')
                ->setStatusCode(404)
                ->respond(null);
        }

        return $this
            ->setMessage('Download access verified.')
            ->respond([
                'can_download' => $orderDownload->canDownload(),
                'downloads_count' => $orderDownload->downloads_count,
                'max_downloads' => $download->product->max_downloads,
                'expires_at' => $orderDownload->expires_at,
                'is_expired' => $orderDownload->isExpired(),
                'is_limit_reached' => $orderDownload->isLimitReached(),
            ]);
    }
}
