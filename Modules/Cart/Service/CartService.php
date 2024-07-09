<?php

namespace Modules\Cart\Service;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
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
     * Get all attributes.
     *
     * @return Collection
     */
    public function getAll(): Collection
    {
        return $this->cart_repository->findAll();
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Cart|Core
     */
    public function apiAddToCart(array $data): Cart|Core
    {
        $product = Product::whereSlug($data['slug'])->firstOrFail();

        $cart = Cart::create([
            'user_id' => (int)Auth::id(),
            'product_id' => $product->id,
            'price' => (int)($product->price - ($product->price * $product->discount) / 100),
            'quantity' => (int)$data['quantity'],
            'amount' => (int)(($product->price - ($product->price * $product->discount) / 100) * $data['quantity']),
        ]);

        Wishlist::whereUserId(Auth::id())->whereCartId(null)->update(['cart_id' => $cart->id]);

        return $cart;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return bool|int
     */
    public function apiUpdateCart(array $data): bool|int
    {
        $product = Product::whereSlug($data['slug'])->firstOrFail();

        return Cart::whereUserId(Auth::id())
            ->whereProductId($product->id)
            ->update([
                'price' => (int)($product->price - ($product->price * $product->discount) / 100),
                'quantity' => (int)$data['quantity'],
                'amount' => (int)(($product->price - ($product->price * $product->discount) / 100) * $data['quantity']),
            ]);
    }

    /**
     * @param  object  $data
     * @return RedirectResponse|void
     */
    public function addToCart(object $data)
    {
        $already_cart = Cart::whereUserId(Auth::id())->where('order_id', null)->whereHas(
            'product',
            function (Builder $query) use ($data) {
                $query->where('slug', $data['slug']);
            }
        )->first();

        if ($already_cart) {
            $already_cart->quantity += 1;
            $already_cart->amount += $already_cart->price;

            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                return back()->with('error', 'Stock not sufficient!');
            }

            $already_cart->save();
        } else {
            $cart = new Cart();
            $cart->user_id = (int)Auth::id();
            $cart->product_id = $data->id;
            $cart->price = (int)($data->price - ($data->price * $data->discount) / 100);
            $cart->quantity = 1;
            $cart->amount = (int)($cart->price * $cart->quantity);

            if ($cart->product->stock < $cart->quantity || $cart->product->stock <= 0) {
                return back()->with('error', 'Stock not sufficient!');
            }

            $cart->save();
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return RedirectResponse|void
     */
    public function singleAddToCart(array $data)
    {
        $product = Product::whereSlug($data['slug'])->firstOrFail();

        $already_cart = Cart::where('user_id', Auth::id())->where('order_id', null)->where(
            'product_id',
            $product->id
        )->first();

        if ($already_cart) {
            $already_cart->quantity += (int)$data['quantity'];
            $already_cart->amount += (int)($product->price * $data['quantity']);

            if ($already_cart->product->stock < $already_cart->quantity || $already_cart->product->stock <= 0) {
                return back()->with('error', 'Stock not sufficient!');
            }

            $already_cart->save();
        } else {
            $cart = new Cart();
            $cart->user_id = (int)Auth::id();
            $cart->product_id = $product->id;
            $cart->price = (int)($product->price - ($product->price * $product->discount) / 100);
            $cart->quantity = (int)$data['quantity'];
            $cart->amount = (int)($product->price * $data['quantity']);

            $cart->save();
        }
    }

    /**
     * @return Collection<int, Cart>
     */
    public function checkout(): Collection
    {
        return Cart::whereUserId(Auth::id())->whereOrderId(null)->get();
    }

    /**
     * Delete an attribute.
     *
     * @param  int  $id  The attribute ID.
     */
    public function destroy(int $id): void
    {
        $this->cart_repository->delete($id);
    }

    /**
     * @param  object  $data
     * @return mixed
     */
    public function cartUpdate(object $data): mixed
    {
        if ($data->quantity) {
            $error = [];
            $success = '';
            foreach ($data->quantity as $k => $quantities) {
                $id = $data->qty_id[$k];
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
                    $after_price = (int)($cart->product->price - ($cart->product->price * $cart->product->discount) / 100);
                    $cart->amount = (int)($after_price * $quantities);
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
        return $this->cart_repository->show();
    }
}
