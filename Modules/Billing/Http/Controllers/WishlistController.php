<?php

namespace Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Billing\Service\WishlistService;
use Modules\Product\Models\Product;

class WishlistController extends Controller
{
    protected ?Product $product = null;
    private WishlistService $wishlist_service;
    
    public function __construct(Product $product)
    {
        $this->product          = $product;
        $this->wishlist_service = new WishlistService();
    }
    
    public function wishlist(Request $request): RedirectResponse
    {
        return $this->wishlist_service->wishlist($request);
    }
    
    public function wishlistDelete(Request $request): RedirectResponse
    {
        return $this->wishlist_service->wishlistDelete($request);
    }
}
