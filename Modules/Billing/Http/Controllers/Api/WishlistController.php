<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Services\WishlistService;
use Modules\Product\Models\Product;

class WishlistController extends Controller
{
    protected WishlistService $wishlistService;

    public function __construct(WishlistService $wishlistService)
    {
        $this->wishlistService = $wishlistService;
        $this->middleware('auth:sanctum')->except(['count', 'check']);
    }

    /**
     * Get user's wishlist
     */
    public function index(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to view your wishlist',
                'login_required' => true,
            ], 401);
        }

        $user = Auth::user();
        $withPriceAlerts = $request->boolean('with_price_alerts', false);

        if ($withPriceAlerts) {
            $wishlist = $this->wishlistService->getWishlistWithPriceAlerts($user);
        } else {
            $wishlist = $this->wishlistService->getUserWishlist($user);
        }

        $stats = $this->wishlistService->getWishlistStats($user);

        return response()->json([
            'success' => true,
            'data' => [
                'wishlist' => $wishlist,
                'statistics' => $stats,
                'count' => $wishlist->count(),
            ],
        ]);
    }

    /**
     * Add product to wishlist
     */
    public function store(Request $request): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Please login to add products to your wishlist',
                'login_required' => true,
            ], 401);
        }

        $request->validate([
            'product_id' => 'required|integer|exists:products,id',
            'quantity' => 'nullable|integer|min:1|max:100',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->input('product_id'));
        $quantity = $request->input('quantity', 1);

        // Check if product is already in wishlist
        if ($this->wishlistService->isInWishlist($user, $product->id)) {
            return response()->json([
                'success' => false,
                'message' => 'Product is already in your wishlist',
            ], 400);
        }

        $wishlistItem = $this->wishlistService->addToWishlist($user, $product, $quantity);

        return response()->json([
            'success' => true,
            'message' => 'Product added to wishlist successfully',
            'data' => $wishlistItem->load('product'),
        ], 201);
    }

    /**
     * Update wishlist item quantity
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1|max:100',
        ]);

        $user = Auth::user();
        $quantity = $request->input('quantity');

        $wishlistItem = $this->wishlistService->updateQuantity($user, $id, $quantity);

        if (! $wishlistItem instanceof \Modules\Billing\Models\Wishlist) {
            return response()->json([
                'success' => false,
                'message' => 'Wishlist item not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Wishlist item updated successfully',
            'data' => $wishlistItem->load('product'),
        ]);
    }

    /**
     * Remove product from wishlist
     */
    public function destroy(int $id): JsonResponse
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        $removed = $this->wishlistService->removeFromWishlist($user, $product->id);

        if (! $removed) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found in wishlist',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product removed from wishlist successfully',
        ]);
    }

    /**
     * Move wishlist item to cart
     */
    public function moveToCart(int $id): JsonResponse
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        $moved = $this->wishlistService->moveToCart($user, $product->id);

        if (! $moved) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to move product to cart',
            ], 400);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product moved to cart successfully',
        ]);
    }

    /**
     * Get wishlist recommendations
     */
    public function recommendations(Request $request): JsonResponse
    {
        $user = Auth::user();
        $limit = min((int) $request->input('limit', 5), 20);

        $recommendations = $this->wishlistService->getWishlistRecommendations($user, $limit);

        return response()->json([
            'success' => true,
            'data' => [
                'recommendations' => $recommendations,
                'count' => $recommendations->count(),
            ],
        ]);
    }

    /**
     * Check if product is in wishlist
     */
    public function check(int $id): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'in_wishlist' => false,
                    'product_id' => $id,
                ],
            ]);
        }

        $user = Auth::user();
        $product = Product::findOrFail($id);

        $isInWishlist = $this->wishlistService->isInWishlist($user, $product->id);

        return response()->json([
            'success' => true,
            'data' => [
                'in_wishlist' => $isInWishlist,
                'product_id' => $product->id,
            ],
        ]);
    }

    /**
     * Get wishlist count
     */
    public function count(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'data' => [
                    'count' => 0,
                ],
            ]);
        }

        $user = Auth::user();
        $count = $this->wishlistService->getWishlistCount($user);

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count,
            ],
        ]);
    }

    /**
     * Bulk operations
     */
    public function bulkOperations(Request $request): JsonResponse
    {
        $request->validate([
            'action' => 'required|string|in:add_to_cart,remove',
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'integer|exists:products,id',
        ]);

        $user = Auth::user();
        $action = $request->input('action');
        $productIds = $request->input('product_ids');

        $results = match ($action) {
            'add_to_cart' => $this->wishlistService->bulkAddToCart($user, $productIds),
            'remove' => ['removed_count' => $this->wishlistService->bulkRemove($user, $productIds)],
            default => []
        };

        return response()->json([
            'success' => true,
            'message' => 'Bulk operation completed successfully',
            'data' => $results,
        ]);
    }

    /**
     * Share wishlist
     */
    public function share(Request $request): JsonResponse
    {
        $request->validate([
            'recipient_email' => 'required|email|exists:users,email',
        ]);

        $user = Auth::user();
        $recipient = \Modules\User\Models\User::where('email', $request->input('recipient_email'))->first();

        if (! $recipient) {
            return response()->json([
                'success' => false,
                'message' => 'Recipient user not found',
            ], 404);
        }

        if ($recipient->id === $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot share wishlist with yourself',
            ], 400);
        }

        $this->wishlistService->shareWishlist($user, $recipient);

        return response()->json([
            'success' => true,
            'message' => 'Wishlist shared successfully',
            'data' => [
                'recipient' => [
                    'id' => $recipient->id,
                    'name' => $recipient->name,
                    'email' => $recipient->email,
                ],
            ],
        ]);
    }

    /**
     * Get public wishlist
     */
    public function publicWishlist(string $username): JsonResponse
    {
        $wishlist = $this->wishlistService->getPublicWishlist($username);

        if (! $wishlist instanceof \Illuminate\Support\Collection) {
            return response()->json([
                'success' => false,
                'message' => 'Public wishlist not found or not accessible',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'wishlist' => $wishlist,
                'owner' => $username,
                'count' => $wishlist->count(),
            ],
        ]);
    }

    /**
     * Get wishlist with price alerts
     */
    public function priceAlerts(): JsonResponse
    {
        $user = Auth::user();
        $wishlistWithAlerts = $this->wishlistService->getWishlistWithPriceAlerts($user);

        $priceDrops = $wishlistWithAlerts->where('price_drop', true);
        $noPriceDrops = $wishlistWithAlerts->where('price_drop', false);

        return response()->json([
            'success' => true,
            'data' => [
                'alerts' => [
                    'price_drops' => $priceDrops,
                    'no_price_changes' => $noPriceDrops,
                ],
                'total_price_drops' => $priceDrops->count(),
                'total_items' => $wishlistWithAlerts->count(),
            ],
        ]);
    }
}
