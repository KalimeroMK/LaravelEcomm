<?php

namespace Modules\Coupon\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Coupon\Http\Requests\Store;
use Modules\Coupon\Http\Requests\Update;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Service\CouponService;

class CouponController extends Controller
{
    private CouponService $coupon_service;
    
    public function __construct(CouponService $coupon_service)
    {
        $this->middleware('permission:coupon-list');
        $this->middleware('permission:coupon-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:coupon-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:coupon-delete', ['only' => ['destroy']]);
        $this->coupon_service = $coupon_service;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        return $this->coupon_service->index();
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
        return $this->coupon_service->store($request);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return $this->coupon_service->create();
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
        return $this->coupon_service->edit($coupon);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Coupon  $coupon
     *
     * @return RedirectResponse
     */
    public function update(Update $request, Coupon $coupon): RedirectResponse
    {
        return $this->coupon_service->update($request, $coupon);
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
        return $this->coupon_service->destroy($coupon);
    }
}
