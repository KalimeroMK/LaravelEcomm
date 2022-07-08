<?php

namespace Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cart\Http\Requests\AddToCartSingle;
use Modules\Cart\Repository\CartRepository;

class CartController extends Controller
{
    
    private CartRepository $cart_repository;
    
    public function __construct()
    {
        $this->middleware('login');
        $this->cart_repository = new CartRepository();
    }
    
    public function addToCart($request): RedirectResponse
    {
        return $this->cart_repository->addToCart($request);
    }
    
    /**
     * @param  AddToCartSingle  $request
     *
     * @return RedirectResponse
     */
    public function singleAddToCart(AddToCartSingle $request): RedirectResponse
    {
        return $this->cart_repository->singleAddToCart($request);
    }
    
    public function cartDelete(Request $request): RedirectResponse
    {
        return $this->cart_repository->cartDelete($request);
    }
    
    public function cartUpdate(Request $request): RedirectResponse
    {
        return $this->cart_repository->cartUpdate($request);
    }
    
    /**
     * @return Application|Factory|View|RedirectResponse
     */
    public function checkout(): View|Factory|RedirectResponse|Application
    {
        return $this->cart_repository->checkout();
    }
}
