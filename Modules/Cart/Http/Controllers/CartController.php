<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Actions\CreateCartAction;
use Modules\Cart\Actions\DeleteCartAction;
use Modules\Cart\Actions\UpdateCartAction;
use Modules\Cart\DTOs\CartDTO;
use Modules\Cart\Http\Requests\AddToCartSingle;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repository\CartRepository;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Product\Models\Product;

class CartController extends CoreController
{
    private CreateCartAction $createAction;

    private UpdateCartAction $updateAction;

    private DeleteCartAction $deleteAction;

    public function __construct(
        CartRepository $repository,
        CreateCartAction $createAction,
        UpdateCartAction $updateAction,
        DeleteCartAction $deleteAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
    }

    public function addToCart(string $slug): RedirectResponse
    {
        if (Auth::check()) {
            $product = Product::whereSlug($slug)->first();
            if (! $slug || ! $product) {
                request()->session()->flash('error', 'Product not added to cart');

                return back();
            }
            $dto = new CartDTO(
                id: null,
                product_id: $product->id,
                quantity: 1,
                user_id: Auth::id(),
                price: $product->price,
                session_id: session()->getId(),
                amount: $product->price,
            );
            $this->createAction->execute($dto);
            session()->flash('success', 'Product successfully added to cart');

            return redirect()->back();
        }
        request()->session()->flash('error', 'Pls login first');

        return back();
    }

    /**
     * @throws Exception
     */
    public function singleAddToCart(AddToCartSingle $request): RedirectResponse
    {
        if (Auth::check()) {
            $data = $request->validated();
            $product = Product::whereSlug($data['slug'])->firstOrFail();
            $dto = new CartDTO(
                id: null,
                product_id: $product->id,
                quantity: $data['quantity'],
                user_id: Auth::id(),
                price: $product->price,
                session_id: session()->getId(),
                amount: $product->price * $data['quantity'],
            );
            $this->createAction->execute($dto);
            session()->flash('success', 'Product successfully added to cart');
        }
        request()->session()->flash('error', 'Pls login first');

        return redirect()->back();
    }

    /**
     * @throws Exception
     */
    public function cartDelete(Request $request): RedirectResponse
    {
        $this->deleteAction->execute($request->id);

        return redirect()->back();
    }

    /**
     * Updates cart information.
     */
    public function cartUpdate(Request $request): RedirectResponse
    {
        // Example: expects quantity and qty_id arrays in request
        if ($request->has('quantity') && $request->has('qty_id')) {
            foreach ($request->input('quantity') as $k => $qty) {
                $id = $request->input('qty_id')[$k];
                $cart = Cart::findOrFail($id);
                $product = $cart->product;
                $updateDto = new CartDTO(
                    id: $cart->id,
                    product_id: $cart->product_id,
                    quantity: $qty,
                    user_id: $cart->user_id,
                    price: $cart->price,
                    session_id: session()->getId(),
                    amount: $cart->price * $qty,
                );
                $this->updateAction->execute($updateDto);
            }
        }

        return redirect()->back();
    }

    /**
     * @throws Exception
     */
    public function checkout(): View|Factory|RedirectResponse|Application
    {
        $cart = Cart::whereUserId(Auth::id())->whereOrderId(null)->get();
        if (! $cart instanceof Collection || $cart->isEmpty()) {
            session()->flash('error', 'Cart is empty');

            return back();
        }

        return view('front::pages.checkout');
    }
}
