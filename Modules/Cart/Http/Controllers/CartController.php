<?php

namespace Modules\Cart\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cart\Http\Requests\AddToCartSingle;
use Modules\Cart\Models\Cart;
use Modules\Cart\Service\CartService;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Product\Models\Product;

class CartController extends CoreController
{
    private CartService $cart_service;

    public function __construct(CartService $cart_service)
    {
        $this->cart_service = $cart_service;
        $this->authorizeResource(Cart::class);
    }

    public function addToCart(string $slug): RedirectResponse
    {
        if (empty($slug || Product::whereSlug($slug)->first())) {
            request()->session()->flash('error', 'Product not added to cart');

            return back();
        }
        $this->cart_service->addToCart(Product::whereSlug($slug)->first());
        session()->flash('success', 'Product successfully added to cart');

        return redirect()->back();
    }

    /**
     * @throws Exception
     */
    public function singleAddToCart(AddToCartSingle $request): RedirectResponse
    {
        $this->cart_service->singleAddToCart($request->validated());
        session()->flash('success', 'Product successfully added to cart');

        return redirect()->back();
    }

    /**
     * @throws Exception
     */
    public function cartDelete(Request $request): RedirectResponse
    {
        $this->cart_service->destroy($request->id);

        return redirect()->back();
    }

    /**
     * Updates cart information.
     */
    public function cartUpdate(Request $request): RedirectResponse
    {
        $this->cart_service->cartUpdate($request);

        return redirect()->back();
    }

    /**
     * @throws Exception
     */
    public function checkout(): View|Factory|RedirectResponse|Application
    {
        if ($this->cart_service->checkout() == null) {
            session()->flash('error', 'Cart is empty');
            return back();
        }

        return view('front::pages.checkout');
    }
}
