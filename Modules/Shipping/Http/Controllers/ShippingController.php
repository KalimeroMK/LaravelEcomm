<?php

namespace Modules\Shipping\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Shipping\Http\Requests\Store;
use Modules\Shipping\Http\Requests\Update;
use Modules\Shipping\Models\Shipping;
use Modules\Shipping\Service\ShippingService;

class ShippingController extends CoreController
{
    private ShippingService $shipping_service;

    public function __construct(ShippingService $shipping_service)
    {
        $this->shipping_service = $shipping_service;
        $this->authorizeResource(Shipping::class, 'shipping');
    }

    public function index(): Application|Factory|View
    {
        return view('shipping::index', ['shippings' => $this->shipping_service->getAll()]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->shipping_service->create($request->validated());
        return redirect()->route('shippings.index');
    }

    public function create(): Application|Factory|View
    {
        return view('shipping::create');
    }

    public function edit(Shipping $shipping): Application|Factory|View
    {
        return view('shipping::edit')->with(['shipping' => $this->shipping_service->findById($shipping->id)]);
    }

    public function update(Update $request, Shipping $shipping): RedirectResponse
    {
        $this->shipping_service->update($shipping->id, $request->validated());
        return redirect()->route('shippings.index');
    }

    public function destroy(Shipping $shipping): RedirectResponse
    {
        $this->shipping_service->delete($shipping->id);
        return redirect()->back();
    }
}
