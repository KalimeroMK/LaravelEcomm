<?php

namespace Modules\Cart\Service;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Models\Wishlist;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repository\CartRepository;
use Modules\Product\Models\Product;

class CartService
{
    private CartRepository $cart_repository;
    
    public function __construct(CartRepository $cart_repository)
    {
        $this->cart_repository = $cart_repository;
    }
    
    /**
     * @param $data
     *
     * @return RedirectResponse|void
     */
    public function addToCart($data)
    {
        $already_cart = Cart::whereUserId(Auth::id())->where('order_id', null)->whereHas(
            'product',
            function (Builder $query) use ($data) {
                $query->where('slug', $data->slug);
            }
        )->first();
        if ($already_cart) {
            $already_cart->quantity = $already_cart->quantity + 1;
            $already_cart->amount   = $data->price + $already_cart->amount;
            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                return back()->with('error', 'Stock not sufficient!.');
            }
            $already_cart->save();
        } else {
            $cart             = new Cart();
            $cart->user_id    = Auth::id();
            $cart->product_id = $data->id;
            $cart->price      = ($data->price - ($data->price * $data->discount) / 100);
            $cart->quantity   = 1;
            $cart->amount     = $cart->price * $cart->quantity;
            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) {
                return back()->with('error', 'Stock not sufficient!.');
            }
            $cart->save();
            Wishlist::whereUserId(Auth::id())->whereCartId(null)->update(['cart_id' => $cart->id]);
        }
    }
    
    /**
     * @param $data
     *
     * @return RedirectResponse|void
     */
    public function singleAddToCart($data)
    {
        $product = Product::whereSlug($data['slug'])->firstOrFail();
        
        $already_cart = Cart::where('user_id', auth()->user()->id)->where('order_id', null)->where('product_id', $product->id)->first();
        
        if ($already_cart) {
            $already_cart->quantity = $already_cart->quantity + $data['quant'][1];
            $already_cart->amount   = ($product->price * $data['quant'][1]) + $already_cart->amount;
            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                return back()->with('error', 'Stock not sufficient!.');
            }
            $already_cart->save();
        } else {
            $cart             = new Cart();
            $cart->user_id    = Auth::id();
            $cart->product_id = $product->id;
            $cart->price      = ($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity   = $data['quant'][1];
            $cart->amount     = ($product->price * $data['quant'][1]);
            $cart->save();
        }
    }
    
    /**
     * @return RedirectResponse|void
     */
    public function checkout()
    {
        $cart = Cart::whereUserId(Auth::id())->whereOrderId(null)->get();
        if (empty($cart)) {
            request()->session()->flash('error', 'Cart is empty');
            
            return redirect()->back();
        }
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param $id
     *
     * @return void
     */
    public function destroy($id): void
    {
        $this->cart_repository->delete($id);
    }
    
    public function cartUpdate($data): RedirectResponse
    {
        if ($data->quant) {
            $error   = [];
            $success = '';
            foreach ($data->quant as $k => $quant) {
                $id   = $data->qty_id[$k];
                $cart = Cart::findOrFail($id);
                if ($quant > 0 && $cart) {
                    if ($cart->product->stock < $quant) {
                        request()->session()->flash('error', 'Out of stock');
                        
                        return back();
                    }
                    $cart->quantity = ($cart->product->stock > $quant) ? $quant : $cart->product->stock;
                    
                    if ($cart->product->stock <= 0) {
                        continue;
                    }
                    $after_price  = ($cart->product->price - ($cart->product->price * $cart->product->discount) / 100);
                    $cart->amount = $after_price * $quant;
                    $cart->save();
                    session()->put('cart', $cart);
                    $success = 'Cart successfully updated!';
                } else {
                    $error[] = 'Cart Invalid!';
                }
            }
            
            return back()->with($error)->with('success', $success);
        } else {
            return back()->with('Cart Invalid!');
        }
    }
}