<?php

namespace Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Cart\Http\Requests\AddToCartSingle;
use Modules\Cart\Service\CartService;
use Modules\Product\Models\Product;
use mysql_xdevapi\Exception;

class CartController extends Controller
{
    
    private CartService $cart_service;
    
    public function __construct(CartService $cart_service)
    {
        $this->cart_service = $cart_service;
        $this->middleware('login');
    }
    
    /**
     * @param $data
     *
     * @return RedirectResponse
     */
    public function addToCart($data): RedirectResponse
    {
        try {
            if (empty($data || Product::whereSlug($data)->first())) {
                request()->session()->flash('error', 'Product successfully added to cart');
                
                return back();
            }
            $this->cart_service->addToCart(Product::whereSlug($data)->first());
            session()->flash('message', 'Product successfully added to cart');
        } catch (Exception $e) {
            throw new Exception($e);
        }
        
        return redirect()->back();
    }
    
    /**
     * @param  AddToCartSingle  $request
     *
     * @return RedirectResponse
     */
    public function singleAddToCart(AddToCartSingle $request): RedirectResponse
    {
        try {
            $this->cart_service->singleAddToCart($request->validated());
            session()->flash('message', 'Product successfully added to cart');
        } catch (Exception $e) {
            throw new Exception($e);
        }
        
        return redirect()->back();
    }
    
    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function cartDelete(Request $request): RedirectResponse
    {
        try {
            $this->cart_service->destroy($request->id);
        } catch (Exception $e) {
            throw new Exception($e);
        }
        
        return redirect()->back();
    }
    
    public function cartUpdate(Request $request): RedirectResponse
    {
        try {
            $this->cart_service->cartUpdate($request);
        } catch (Exception $e) {
            throw new Exception($e);
        }
        
        return redirect()->back();
    }
    
    /**
     * @return Application|Factory|View|RedirectResponse
     */
    public function checkout(): View|Factory|RedirectResponse|Application
    {
        try {
            $this->cart_service->checkout();
        } catch (Exception $e) {
            throw new Exception($e);
        }
        
        return view('front::pages.checkout');
    }
}
