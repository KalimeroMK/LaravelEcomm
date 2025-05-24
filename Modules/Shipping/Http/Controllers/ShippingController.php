<?php

declare(strict_types=1);

namespace Modules\Shipping\Http\Controllers;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Shipping\Actions\DeleteShippingAction;
use Modules\Shipping\Actions\FindShippingAction;
use Modules\Shipping\Actions\GetAllShippingAction;
use Modules\Shipping\Actions\StoreShippingAction;
use Modules\Shipping\Actions\UpdateShippingAction;
use Modules\Shipping\Http\Requests\Store;
use Modules\Shipping\Http\Requests\Update;
use Modules\Shipping\Models\Shipping;

class ShippingController extends CoreController
{
    public function __construct()
    {
        $this->authorizeResource(Shipping::class, 'shipping');
    }

    public function index(): Application|Factory|View
    {
        $shippingsDto = (new GetAllShippingAction())->execute();

        return view('shipping::index', ['shippings' => $shippingsDto->shippings]);
    }

    public function store(Store $request): RedirectResponse
    {
        (new StoreShippingAction())->execute($request->validated());

        return redirect()->route('shippings.index');
    }

    public function create(): Application|Factory|View
    {
        return view('shipping::create');
    }

    public function edit(Shipping $shipping): Application|Factory|View
    {
        $shippingDto = (new FindShippingAction())->execute($shipping->id);

        return view('shipping::edit')->with(['shipping' => $shippingDto->shipping]);
    }

    public function update(Update $request, Shipping $shipping): RedirectResponse
    {
        (new UpdateShippingAction())->execute($shipping->id, $request->validated());

        return redirect()->route('shippings.index');
    }

    public function destroy(Shipping $shipping): RedirectResponse
    {
        (new DeleteShippingAction())->execute($shipping->id);

        return redirect()->back();
    }
}
