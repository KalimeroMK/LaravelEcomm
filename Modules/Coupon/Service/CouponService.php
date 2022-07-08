<?php

namespace Modules\Coupon\Service;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Coupon\Http\Requests\Store;
use Modules\Coupon\Http\Requests\Update;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Repository\CouponRepository;

class CouponService
{
    private CouponRepository $coupon_repository;
    
    public function __construct(CouponRepository $coupon_repository)
    {
        $this->coupon_repository = $coupon_repository;
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
        $coupon = Coupon::create($request->validated());
        if ($coupon) {
            request()->session()->flash('success', 'Coupon Successfully added');
        } else {
            request()->session()->flash('error', 'Please try again!!');
        }
        
        return redirect()->route('coupons.edit', $coupon);
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
        return view('coupon::edit', compact('coupon'));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('coupon::create');
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
        $status = $coupon->update($request->validated());
        if ($status) {
            request()->session()->flash('success', 'Coupon Successfully updated');
        } else {
            request()->session()->flash('error', 'Please try again!!');
        }
        
        return redirect()->route('coupons::edit', $coupon);
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
        $status = $coupon->delete();
        if ($status) {
            request()->session()->flash('success', 'Coupon successfully deleted');
        } else {
            request()->session()->flash('error', 'Error, Please try again');
        }
        
        return redirect()->route('coupon.index');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        $coupons = $this->coupon_repository->getAll();
        
        return view('coupon::index', compact('coupons'));
    }
}