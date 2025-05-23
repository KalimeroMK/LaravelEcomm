<?php

declare(strict_types=1);

namespace Modules\Billing\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Billing\Actions\Wishlist\CreateWishlistAction;
use Modules\Billing\Actions\Wishlist\DeleteWishlistAction;
use Modules\Billing\DTOs\WishlistDTO;
use Modules\Billing\Http\Requests\Store;
use Modules\Billing\Repository\WishlistRepository;
use Modules\Core\Http\Controllers\CoreController;

class WishlistController extends CoreController
{
    private CreateWishlistAction $createAction;

    private DeleteWishlistAction $deleteAction;

    private WishlistRepository $repository;

    public function __construct(
        WishlistRepository $repository,
        CreateWishlistAction $createAction,
        DeleteWishlistAction $deleteAction
    ) {
        $this->repository = $repository;
        $this->createAction = $createAction;
        $this->deleteAction = $deleteAction;
    }

    public function wishlist(string $slug, Store $request): RedirectResponse
    {
        if (Auth::check()) {
            $request->merge(['slug' => $slug]); // Add the slug to the request data
            $request->validate($request->rules());
            $dto = WishlistDTO::fromRequest($request);
            $this->createAction->execute($dto);

            request()->session()->flash('success', 'Product successfully added to wishlist');

            return back();
        }
        request()->session()->flash('error', 'Pls login first');

        return back();
    }

    public function wishlistDelete(Request $request): RedirectResponse
    {
        $this->deleteAction->execute($request->id);

        request()->session()->flash('success', 'Wishlist successfully removed');

        return back();
    }
}
