<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Http\Requests\Store;
use Modules\Billing\Service\WishlistService;
use Modules\Core\Http\Controllers\CoreController;

class WishlistController extends CoreController
{
    private WishlistService $wishlist_service;

    public function __construct(WishlistService $wishlist_service)
    {
        $this->wishlist_service = $wishlist_service;
    }

    public function wishlist(string $slug, Store $request): RedirectResponse
    {
        if (Auth::check()) {
            $request->merge(['slug' => $slug]); // Add the slug to the request data
            $request->validate($request->rules());
            $this->wishlist_service->create($request->all());

            request()->session()->flash('success', 'Product successfully added to wishlist');

            return back();
        }
        request()->session()->flash('error', 'Pls login first');

        return back();
    }

    public function wishlistDelete(Request $request): RedirectResponse
    {
        $this->wishlist_service->delete($request->id);

        request()->session()->flash('success', 'Wishlist successfully removed');

        return back();
    }
}
