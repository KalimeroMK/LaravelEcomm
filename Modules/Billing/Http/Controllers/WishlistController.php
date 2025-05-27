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
use Modules\Core\Http\Controllers\CoreController;
use Throwable;

class WishlistController extends CoreController
{
    public function __construct(
        private readonly CreateWishlistAction $createAction,
        private readonly DeleteWishlistAction $deleteAction
    ) {}

    public function wishlist(string $slug, Store $request): RedirectResponse
    {
        if (! Auth::check()) {
            return back()->with('error', __('Please login first.'));
        }

        try {
            $request->merge(['slug' => $slug]);

            $request->validate($request->rules());

            $dto = WishlistDTO::fromRequest($request);
            $this->createAction->execute($dto);

            return back()->with('success', __('Product successfully added to wishlist.'));
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function wishlistDelete(Request $request): RedirectResponse
    {
        try {
            $this->deleteAction->execute((int) $request->id);

            return back()->with('success', __('Wishlist successfully removed.'));
        } catch (Throwable $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
