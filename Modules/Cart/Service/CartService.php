<?php

namespace Modules\Cart\Service;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Models\Wishlist;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repository\CartRepository;
use Modules\Core\Models\Core;
use Modules\Product\Models\Product;

class CartService
{
    public CartRepository $cart_repository;
    
    public function __construct(CartRepository $cart_repository)
    {
        $this->cart_repository = $cart_repository;
    }
    
    /**
     * @return mixed|string
     */
    public function getAll(): mixed
    {
        try {
            return $this->cart_repository->findAll();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $data
     *
     * @return Cart|Core
     */
    public function apiAddToCart($data): Cart|Core
    {
        $product = Product::whereSlug($data['slug'])->firstOrFail();
        
        $cart = Cart::Create(
            
            [
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
                'price'      => ($product->price - ($product->price * $product->discount) / 100),
                'quantity'   => $data['quantity'],
                'amount'     => ($product->price - ($product->price * $product->discount) / 100) * $data['quantity'],
            ]
        );
        Wishlist::whereUserId(Auth::id())->whereCartId(null)->update(['cart_id' => $cart->id]);
        
        return $cart;
    }
    
    /**
     * @param $data
     *
     * @return bool|int
     */
    public function apiAUpdateCart($data): bool|int
    {
        $product = Product::whereSlug($data['slug'])->firstOrFail();
        
        return Cart::whereUserId(Auth::id())
                   ->whereProductId($product->id)
                   ->update(
            
                       [
                           'price'    => ($product->price - ($product->price * $product->discount) / 100),
                           'quantity' => $data['quantity'],
                           'amount'   => ($product->price - ($product->price * $product->discount) / 100) * $data['quantity'],
                       ]
                   );
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
            $already_cart->quantity = $already_cart->quantity + $data['quantity'][1];
            $already_cart->amount   = ($product->price * $data['quantity'][1]) + $already_cart->amount;
            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                return back()->with('error', 'Stock not sufficient!.');
            }
            $already_cart->save();
        } else {
            $cart             = new Cart();
            $cart->user_id    = Auth::id();
            $cart->product_id = $product->id;
            $cart->price      = ($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity   = $data['quantity'][1];
            $cart->amount     = ($product->price * $data['quantity'][1]);
            $cart->save();
        }
    }
    
    /**
     * @return Builder[]|Collection
     */
    public function checkout(): Collection|array
    {
        return Cart::whereUserId(Auth::id())->whereOrderId(null)->get();
    }
    
    /**
     * @param $id
     *
     * @return string|void
     */
    
    public function destroy($id)
    {
        try {
            $this->cart_repository->delete($id);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    public function cartUpdate($data): RedirectResponse
    {
        if ($data->quantity) {
            $error   = [];
            $success = '';
            foreach ($data->quantity as $k => $quantities) {
                $id   = $data->qty_id[$k];
                $cart = Cart::findOrFail($id);
                if ($quantities > 0 && $cart) {
                    if ($cart->product->stock < $quantities) {
                        request()->session()->flash('error', 'Out of stock');
                        
                        return back();
                    }
                    $cart->quantity = ($cart->product->stock > $quantities) ? $quantities : $cart->product->stock;
                    
                    if ($cart->product->stock <= 0) {
                        continue;
                    }
                    $after_price  = ($cart->product->price - ($cart->product->price * $cart->product->discount) / 100);
                    $cart->amount = $after_price * $quantities;
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
    
    /**
     * @return mixed|string
     */
    public function show(): mixed
    {
        try {
            return $this->cart_repository->show();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
}