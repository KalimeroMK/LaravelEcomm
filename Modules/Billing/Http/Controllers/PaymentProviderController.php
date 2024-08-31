<?php

namespace Modules\Billing\Http\Controllers;

use Exception;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Billing\Http\Requests\PaymentProvider\Store;
use Modules\Billing\Http\Requests\PaymentProvider\Update;
use Modules\Billing\Models\PaymentProvider;
use Modules\Billing\Service\PaymentProviderService;
use Modules\Core\Http\Controllers\CoreController;

class PaymentProviderController extends CoreController
{
    public PaymentProviderService $paymentProviderService;

    public function __construct(PaymentProviderService $paymentProviderService)
    {
        $this->authorizeResource(PaymentProvider::class, 'paymentProvider');
        $this->paymentProviderService = $paymentProviderService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View|Application
    {
        return view('billing::index', ['paymentProviders' => $this->paymentProviderService->getAll()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('billing::create', ['paymentProvider' => new PaymentProvider]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @throws Exception
     */
    public function store(Store $request): RedirectResponse
    {
        $this->paymentProviderService->create($request->validated());

        return redirect()->route('payment_provider.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(PaymentProvider $paymentProvider): View|Factory|Application
    {
        $paymentProvider = $this->paymentProviderService->findById($paymentProvider->id);

        return view('billing::edit', compact('paymentProvider'));
    }

    /**}
     * @param  Update           $request
     * @param  PaymentProvider  $paymentProvider
     * @return RedirectResponse
     */
    public function update(Update $request, PaymentProvider $paymentProvider): RedirectResponse
    {
        $this->paymentProviderService->update($paymentProvider->id, $request->validated());

        return redirect()->route('payment_provider.edit', $paymentProvider);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PaymentProvider $paymentProvider): RedirectResponse
    {
        $this->paymentProviderService->delete($paymentProvider->id);

        return redirect()->route('payment_provider.index');
    }
}
