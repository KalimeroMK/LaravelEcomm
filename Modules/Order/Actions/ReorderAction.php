<?php

declare(strict_types=1);

namespace Modules\Order\Actions;

use Illuminate\Http\RedirectResponse;
use Modules\Cart\Models\Cart;
use Modules\Order\Models\Order;

class ReorderAction
{
    /**
     * Reorder a previous order by adding its items to the cart.
     */
    public function execute(int $orderId, int $userId): array
    {
        $order = Order::with('carts.product')->findOrFail($orderId);
        
        // Verify the order belongs to the user
        if ($order->user_id !== $userId) {
            return [
                'success' => false,
                'message' => 'You do not have permission to reorder this order.',
            ];
        }
        
        $addedItems = 0;
        $skippedItems = 0;
        
        foreach ($order->carts as $cartItem) {
            $product = $cartItem->product;
            
            // Check if product is still active
            if (!$product || $product->status !== 'active') {
                $skippedItems++;
                continue;
            }
            
            // Check if product is in stock
            if ($product->stock !== null && $product->stock < $cartItem->quantity) {
                $skippedItems++;
                continue;
            }
            
            // Check if product already exists in cart
            $existingCart = Cart::where('user_id', $userId)
                ->where('product_id', $product->id)
                ->whereNull('order_id')
                ->first();
            
            if ($existingCart) {
                // Update quantity
                $newQuantity = $existingCart->quantity + $cartItem->quantity;
                $existingCart->update([
                    'quantity' => $newQuantity,
                    'amount' => $newQuantity * $product->price,
                ]);
            } else {
                // Create new cart item
                Cart::create([
                    'user_id' => $userId,
                    'product_id' => $product->id,
                    'quantity' => $cartItem->quantity,
                    'price' => $product->price,
                    'amount' => $cartItem->quantity * $product->price,
                    'status' => 'new',
                ]);
            }
            
            $addedItems++;
        }
        
        return [
            'success' => true,
            'added_items' => $addedItems,
            'skipped_items' => $skippedItems,
            'message' => $addedItems > 0 
                ? "Added {$addedItems} item(s) to your cart." 
                : 'No items could be added to your cart.',
        ];
    }
}
