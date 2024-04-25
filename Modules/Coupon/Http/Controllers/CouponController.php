<?php

namespace Modules\Coupon\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Coupon\Http\Requests\Store;
use Modules\Coupon\Http\Requests\Update;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Service\CouponService;

class CouponController extends CoreController
{
    private CouponService $coupon_service;

    public function __construct(CouponService $coupon_service)
    {
        $this->authorizeResource(Coupon::class, 'coupon');
        $this->coupon_service = $coupon_service;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        return view('coupon::index', ['coupons' => $this->coupon_service->getAll()]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     *
     * @return RedirectResponse
     */
    public function store(Store $request): RedirectResponse
    {
        $this->coupon_service->store($request->validated());

        return redirect()->route('coupons.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('coupon::create', ['coupon' => new Coupon()]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Coupon  $coupon
     *
     * @return Application|Factory|View
     */
    public function edit(Coupon $coupon): View|Factory|Application
    {
        return view('coupon::edit', ['coupon' => $this->coupon_service->edit($coupon->id)]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Coupon  $coupon
     * @return RedirectResponse
     */
    public function update(Update $request, Coupon $coupon): RedirectResponse
    {
        return $this->coupon_service->update($coupon->id, $request->validated());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Coupon  $coupon
     *
     * @return RedirectResponse
     */
    public function destroy(Coupon $coupon): RedirectResponse
    {
        $this->coupon_service->destroy($coupon->id);

        return redirect()->back();
    }
}
