<?php

declare(strict_types=1);

namespace Modules\Cart\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Modules\Cart\Actions\CreateCartAction;
use Modules\Cart\Actions\DeleteCartAction;
use Modules\Cart\Actions\GetUserCartAction;
use Modules\Cart\Actions\UpdateCartItemsAction;
use Modules\Cart\DTOs\CartDTO;
use Modules\Cart\Http\Requests\AddToCartSingle;
use Modules\Cart\Models\Cart;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Product\Actions\FindProductBySlugAction;

// theme_view() is loaded via composer autoload files

class CartController extends CoreController
{
    public function __construct(
        private readonly FindProductBySlugAction $findProductBySlugAction,
        private readonly CreateCartAction $createAction,
        private readonly UpdateCartItemsAction $updateCartItemsAction,
        private readonly DeleteCartAction $deleteAction,
        private readonly GetUserCartAction $getUserCartAction
    ) {
        $this->authorizeResource(Cart::class, 'cart');
    }

    public function addToCart(string $slug): RedirectResponse
    {
        if (! Auth::check()) {
            request()->session()->flash('error', __('messages.please_login_first'));

            return back();
        }

        $product = $this->findProductBySlugAction->execute($slug);
        if (! $slug || ! $product) {
            request()->session()->flash('error', __('messages.product_not_added_to_cart'));

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
            order_id: null,
        );
        $this->createAction->execute($dto);
        session()->flash('success', __('messages.product_added_to_cart'));

        return redirect()->back();
    }

    public function singleAddToCart(AddToCartSingle $request): RedirectResponse
    {
        if (! Auth::check()) {
            request()->session()->flash('error', __('messages.please_login_first'));

            return redirect()->back();
        }

        $data = $request->validated();
        $product = $this->findProductBySlugAction->execute($data['slug']);

        if (! $product) {
            request()->session()->flash('error', __('messages.product_not_found'));

            return redirect()->back();
        }

        $dto = new CartDTO(
            id: null,
            product_id: $product->id,
            quantity: (int) $data['quantity'],
            user_id: Auth::id(),
            price: $product->price,
            session_id: session()->getId(),
            amount: $product->price * (int) $data['quantity'],
            order_id: null,
        );
        $this->createAction->execute($dto);
        session()->flash('success', __('messages.product_added_to_cart'));

        return redirect()->back();
    }

    public function cartDelete(int $id): RedirectResponse
    {
        $this->deleteAction->execute($id);

        return redirect()->back();
    }

    public function cartUpdate(Request $request): RedirectResponse
    {
        $this->updateCartItemsAction->execute($request);

        return redirect()->back();
    }

    public function checkout(): View|Factory|RedirectResponse|Application
    {
        $cart = $this->getUserCartAction->execute();
        if (! $cart instanceof Collection || $cart->isEmpty()) {
            session()->flash('error', __('messages.cart_is_empty'));

            return back();
        }

        return view(theme_view('pages.checkout'));
    }
}
