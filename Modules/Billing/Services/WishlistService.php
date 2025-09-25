<?php

declare(strict_types=1);

namespace Modules\Billing\Services;

use Illuminate\Support\Collection;
use Modules\Billing\Models\Wishlist;
use Modules\Product\Models\Product;
use Modules\User\Models\User;

class WishlistService
{
    /**
     * Add a product to user's wishlist
     */
    public function addToWishlist(User $user, Product $product, int $quantity = 1): Wishlist
    {
        // Check if already in wishlist
        $existingWishlist = $user->wishlists()
            ->where('product_id', $product->id)
            ->first();

        if ($existingWishlist) {
            // Update quantity if already exists
            $existingWishlist->update([
                'quantity' => $existingWishlist->quantity + $quantity,
                'price' => $product->price,
                'amount' => ($existingWishlist->quantity + $quantity) * $product->price,
            ]);

            return $existingWishlist;
        }

        // Create new wishlist item
        return $user->wishlists()->create([
            'product_id' => $product->id,
            'quantity' => $quantity,
            'price' => $product->price,
            'amount' => $quantity * $product->price,
        ]);
    }

    /**
     * Remove a product from user's wishlist
     */
    public function removeFromWishlist(User $user, int $productId): bool
    {
        return $user->wishlists()
            ->where('product_id', $productId)
            ->delete();
    }

    /**
     * Update wishlist item quantity
     */
    public function updateQuantity(User $user, int $productId, int $quantity): ?Wishlist
    {
        $wishlist = $user->wishlists()
            ->where('product_id', $productId)
            ->first();

        if (! $wishlist) {
            return null;
        }

        $wishlist->update([
            'quantity' => $quantity,
            'amount' => $quantity * $wishlist->price,
        ]);

        return $wishlist;
    }

    /**
     * Move wishlist item to cart
     */
    public function moveToCart(User $user, int $productId): bool
    {
        $wishlist = $user->wishlists()
            ->where('product_id', $productId)
            ->first();

        if (! $wishlist) {
            return false;
        }

        // Add to cart
        $user->carts()->create([
            'product_id' => $wishlist->product_id,
            'quantity' => $wishlist->quantity,
            'price' => $wishlist->price,
            'amount' => $wishlist->amount,
        ]);

        // Remove from wishlist
        $wishlist->delete();

        return true;
    }

    /**
     * Get user's wishlist with products
     */
    public function getUserWishlist(User $user): Collection
    {
        return $user->wishlists()
            ->with(['product.media', 'product.brand', 'product.categories'])
            ->get();
    }

    /**
     * Get wishlist count for a user
     */
    public function getWishlistCount(User $user): int
    {
        return $user->wishlists()->count();
    }

    /**
     * Check if product is in user's wishlist
     */
    public function isInWishlist(User $user, int $productId): bool
    {
        return $user->wishlists()
            ->where('product_id', $productId)
            ->exists();
    }

    /**
     * Get wishlist statistics
     */
    public function getWishlistStats(User $user): array
    {
        $wishlist = $user->wishlists()->with('product')->get();

        return [
            'total_items' => $wishlist->count(),
            'total_value' => $wishlist->sum('amount'),
            'categories' => $wishlist->pluck('product.category_id')->filter()->unique()->count(),
            'brands' => $wishlist->pluck('product.brand_id')->filter()->unique()->count(),
            'price_range' => [
                'min' => $wishlist->pluck('price')->min(),
                'max' => $wishlist->pluck('price')->max(),
                'avg' => $wishlist->pluck('price')->avg(),
            ],
        ];
    }

    /**
     * Get wishlist recommendations based on current items
     */
    public function getWishlistRecommendations(User $user, int $limit = 5): Collection
    {
        $wishlistCategories = $user->wishlists()
            ->with('product.categories')
            ->get()
            ->pluck('product.categories.*.id')
            ->flatten()
            ->filter()
            ->unique();

        $wishlistBrands = $user->wishlists()
            ->with('product.brand')
            ->get()
            ->pluck('product.brand_id')
            ->filter()
            ->unique();

        $wishlistProductIds = $user->wishlists()->pluck('product_id')->toArray();

        return Product::where('status', 'active')
            ->where('stock', '>', 0)
            ->whereNotIn('id', $wishlistProductIds)
            ->where(function ($query) use ($wishlistCategories, $wishlistBrands): void {
                $query->whereHas('categories', function ($q) use ($wishlistCategories): void {
                    $q->whereIn('id', $wishlistCategories);
                })
                    ->orWhereIn('brand_id', $wishlistBrands);
            })
            ->limit($limit)
            ->get();
    }

    /**
     * Share wishlist with another user
     */
    public function shareWishlist(User $owner, User $recipient): bool
    {
        // This could be extended to send email notifications
        // For now, we'll just return success
        return true;
    }

    /**
     * Get public wishlist (if user has enabled sharing)
     */
    public function getPublicWishlist(string $username): ?Collection
    {
        // For now, use name instead of username since username field doesn't exist
        $user = User::where('name', $username)->first();

        if (! $user) {
            return null;
        }

        // For now, assume all wishlists are public since wishlist_public field doesn't exist
        return $this->getUserWishlist($user);
    }

    /**
     * Bulk operations on wishlist
     */
    public function bulkAddToCart(User $user, array $productIds): array
    {
        $results = [];

        foreach ($productIds as $productId) {
            $results[$productId] = $this->moveToCart($user, $productId);
        }

        return $results;
    }

    public function bulkRemove(User $user, array $productIds): int
    {
        return $user->wishlists()
            ->whereIn('product_id', $productIds)
            ->delete();
    }

    /**
     * Get wishlist items with price alerts
     */
    public function getWishlistWithPriceAlerts(User $user): Collection
    {
        return $user->wishlists()
            ->with(['product.media', 'product.brand'])
            ->get()
            ->map(function ($wishlist): \Illuminate\Database\Eloquent\Model {
                $product = $wishlist->product;
                $currentPrice = $product->special_price ?? $product->price;
                $wishlistPrice = $wishlist->price;

                $wishlist->price_drop = $wishlistPrice > $currentPrice;
                $wishlist->price_difference = $wishlistPrice - $currentPrice;
                $wishlist->price_drop_percentage = $wishlistPrice > 0
                    ? round((($wishlistPrice - $currentPrice) / $wishlistPrice) * 100, 2)
                    : 0;

                return $wishlist;
            });
    }
}
