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
    private GetAllShippingAction $getAllShippingAction;
    private StoreShippingAction $storeShippingAction;
    private FindShippingAction $findShippingAction;
    private UpdateShippingAction $updateShippingAction;
    private DeleteShippingAction $deleteShippingAction;

    public function __construct(
        GetAllShippingAction $getAllShippingAction,
        StoreShippingAction $storeShippingAction,
        FindShippingAction $findShippingAction,
        UpdateShippingAction $updateShippingAction,
        DeleteShippingAction $deleteShippingAction
    ) {
        $this->authorizeResource(Shipping::class, 'shipping');
        $this->getAllShippingAction = $getAllShippingAction;
        $this->storeShippingAction = $storeShippingAction;
        $this->findShippingAction = $findShippingAction;
        $this->updateShippingAction = $updateShippingAction;
        $this->deleteShippingAction = $deleteShippingAction;
    }

    public function index(): Application|Factory|View
    {
        $shippingDto = $this->getAllShippingAction->execute();

        return view('shipping::index', ['shippings' => $shippingDto->shippings]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->storeShippingAction->execute($request->validated());

        return redirect()->route('shippings.index');
    }

    public function create(): Application|Factory|View
    {
        return view('shipping::create');
    }

    public function edit(Shipping $shipping): Application|Factory|View
    {
        $shippingDto = $this->findShippingAction->execute($shipping->id);

        return view('shipping::edit')->with(['shipping' => $shippingDto->shipping]);
    }

    public function update(Update $request, Shipping $shipping): RedirectResponse
    {
        $this->updateShippingAction->execute($shipping->id, $request->validated());

        return redirect()->route('shippings.index');
    }

    public function destroy(Shipping $shipping): RedirectResponse
    {
        $this->deleteShippingAction->execute($shipping->id);

        return redirect()->back();
    }
}
