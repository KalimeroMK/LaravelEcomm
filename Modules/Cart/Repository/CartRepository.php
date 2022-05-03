<?php

    namespace Modules\Cart\Repository;

    use Modules\Billing\Models\Wishlist;
    use Modules\Cart\Models\Cart;
    use Modules\Product\Models\Product;

    class CartRepository
    {
        public function addToCart($request)
        {
            if (empty($request->slug)) {
                request()->session()->flash('error', 'Invalid Products');

                return back();
            }
            $product = Product::whereSlug($request->slug)->first();
            if (empty($product)) {
                request()->session()->flash('error', 'Invalid Products');

                return back();
            }

            $already_cart = Cart::whereUserId(auth()->user()->id)->where('order_id', null)->where('product_id', $product->id)->first();
            if ($already_cart) {
                $already_cart->quantity = $already_cart->quantity + 1;
                $already_cart->amount   = $product->price + $already_cart->amount;
                if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                    return back()->with('error', 'Stock not sufficient!.');
                }
                $already_cart->save();
            } else {
                $cart             = new Cart();
                $cart->user_id    = auth()->user()->id;
                $cart->product_id = $product->id;
                $cart->price      = ($product->price - ($product->price * $product->discount) / 100);
                $cart->quantity   = 1;
                $cart->amount     = $cart->price * $cart->quantity;
                if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) {
                    return back()->with('error', 'Stock not sufficient!.');
                }
                $cart->save();
                Wishlist::where('user_id', auth()->user()->id)->where('cart_id', null)->update(['cart_id' => $cart->id]);
            }
        }
    }
