<?php

declare(strict_types=1);

namespace Modules\Coupon\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Coupon\Actions\Coupon\CreateCouponAction;
use Modules\Coupon\Actions\Coupon\DeleteCouponAction;
use Modules\Coupon\Actions\Coupon\UpdateCouponAction;
use Modules\Coupon\DTOs\CouponDTO;
use Modules\Coupon\Http\Requests\Store;
use Modules\Coupon\Http\Requests\Update;
use Modules\Coupon\Models\Coupon;

class CouponController extends CoreController
{
    private CreateCouponAction $createAction;

    private UpdateCouponAction $updateAction;

    private DeleteCouponAction $deleteAction;

    public function __construct(
        CreateCouponAction $createAction,
        UpdateCouponAction $updateAction,
        DeleteCouponAction $deleteAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->authorizeResource(Coupon::class, 'coupon');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View|Application
    {
        return view('coupon::index', ['coupons' => Coupon::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): RedirectResponse
    {
        $this->createAction->execute(CouponDTO::fromRequest($request));
        return redirect()->route('coupons.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('coupon::create', ['coupon' => new Coupon]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Coupon $coupon): View|Factory|Application
    {
        return view('coupon::edit', ['coupon' => $coupon]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, Coupon $coupon): RedirectResponse
    {
        $this->updateAction->execute(CouponDTO::fromRequest($request, $coupon->id));
        return redirect()->route('coupon.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $this->deleteAction->execute($coupon->id);

        return redirect()->back();
    }
}
