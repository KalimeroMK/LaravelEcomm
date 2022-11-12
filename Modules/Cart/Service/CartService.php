<?php

namespace Modules\Cart\Service;

use Exception;
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