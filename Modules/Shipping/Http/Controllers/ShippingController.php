<?php

namespace Modules\Shipping\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Shipping\Http\Requests\Store;
use Modules\Shipping\Http\Requests\Update;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Service\ShippingService;

class ShippingController extends Controller
{
    private ShippingService $shipping_service;
    
    public function __construct(ShippingService $shipping_service)
    {
        $this->shipping_service = $shipping_service;
        $this->middleware('permission:shipping-list', ['only' => ['index']]);
        $this->middleware('permission:shipping-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:shipping-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:shipping-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('shipping::index', ['shippings' => $this->shipping_service->getAll()]);
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
        $this->shipping_service->store($request->validated());
        
        return redirect()->route('shippings.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('shipping::create');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Shipping  $shipping
     *
     * @return Application|Factory|View
     */
    public function edit(Shipping $shipping)
    {
        return view('shipping::edit')->with(['shipping' => $this->shipping_service->edit($shipping->id)]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Shipping  $shipping
     *
     * @return RedirectResponse
     */
    public function update(Update $request, Shipping $shipping): RedirectResponse
    {
        $this->shipping_service->update($shipping->id, $request->validated());
        
        return redirect()->route('shippings.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return RedirectResponse
     */
    public function destroy(int $id): RedirectResponse
    {
        $this->shipping_service->destroy($id);
        
        return redirect()->back();
    }
}
