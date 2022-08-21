<?php

namespace Modules\Billing\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\Billing\Http\Requests\Store;
use Modules\Billing\Service\WishlistService;

class WishlistController extends Controller
{
    private WishlistService $wishlist_service;
    
    public function __construct(WishlistService $wishlist_service)
    {
        $this->wishlist_service = $wishlist_service;
    }
    
    public function wishlist(Store $request): RedirectResponse
    {
        $this->wishlist_service->store($request->all());
        
        return back();
    }
    
    /**
     * @param  Request  $request
     *
     * @return RedirectResponse
     */
    public function wishlistDelete(Request $request): RedirectResponse
    {
        $this->wishlist_service->destroy($request->id);
        
        request()->session()->flash('success', 'Wishlist successfully removed');
        
        return back();
    }
    
}
