<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Order\Models\Order;
use Modules\Product\Models\OrderDownload;
use Modules\Product\Models\ProductDownload;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends CoreController
{
    /**
     * Download a file for a purchased product.
     */
    public function download(Request $request, ProductDownload $download, Order $order): StreamedResponse|RedirectResponse
    {
        // Verify signature
        $expectedSignature = hash('sha256', $download->id . ':' . $order->id . ':' . auth()->id() . ':' . config('app.key'));
        
        if ($request->get('signature') !== $expectedSignature) {
            return redirect()->route('front.my-orders')
                ->with('error', 'Invalid download link.');
        }

        // Check if user owns this order
        if ($order->user_id !== auth()->id()) {
            return redirect()->route('front.my-orders')
                ->with('error', 'You do not have permission to download this file.');
        }

        // Check if order is paid
        if ($order->payment_status !== 'paid') {
            return redirect()->route('front.my-orders')
                ->with('error', 'Payment required before downloading.');
        }

        // Get or create order download tracking
        $orderDownload = OrderDownload::firstOrCreate(
            [
                'order_id' => $order->id,
                'product_download_id' => $download->id,
                'user_id' => auth()->id(),
            ],
            [
                'expires_at' => $download->product->download_expiry_days 
                    ? now()->addDays($download->product->download_expiry_days)
                    : null,
            ]
        );

        // Check if download is allowed
        if (!$orderDownload->canDownload()) {
            if ($orderDownload->isExpired()) {
                return redirect()->route('front.my-orders')
                    ->with('error', 'Download link has expired.');
            }

            if ($orderDownload->isLimitReached()) {
                return redirect()->route('front.my-orders')
                    ->with('error', 'Maximum number of downloads reached.');
            }
        }

        // Check if file exists
        if (!Storage::exists($download->file_path)) {
            return redirect()->route('front.my-orders')
                ->with('error', 'File not found.');
        }

        // Record the download
        $orderDownload->recordDownload();

        // Stream the file
        return Storage::download($download->file_path, $download->original_name);
    }

    /**
     * Show download history for authenticated user.
     */
    public function history(): \Illuminate\View\View
    {
        $downloads = OrderDownload::with(['productDownload.product', 'order'])
            ->forUser(auth()->id())
            ->orderBy('last_downloaded_at', 'desc')
            ->paginate(20);

        return view('product::downloads.history', compact('downloads'));
    }

    /**
     * Get download links for an order (API).
     */
    public function orderDownloads(Order $order): \Illuminate\Http\JsonResponse
    {
        // Verify user owns this order
        if ($order->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
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
                    'expires_at' => $orderDownload->expires_at?->toIso8601String(),
                    'download_url' => $download->getDownloadUrl($order->id, auth()->id()),
                ];
            }
        }

        return response()->json(['downloads' => $downloads]);
    }
}
