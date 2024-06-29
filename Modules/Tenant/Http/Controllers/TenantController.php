<?php

namespace Modules\Tenant\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Tenant\Http\Requests\Store;
use Modules\Tenant\Http\Requests\Update;
use Modules\Tenant\Models\Tenant;
use Modules\Tenant\Service\TenantService;

class TenantController extends Controller
{

    protected TenantService $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Display a listing of the resource.
     *
     *
     * @return Factory|View|Application
     */
    public function index(): Factory|View|Application
    {
        return view('tenant::index', ['tenants' => $this->tenantService->getAll()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create(): View|Factory|Application
    {
        return view('tenant::create', ['tenant' => new Tenant()]);
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
        $this->tenantService->create($request->validated());

        return redirect()->route('banners.index');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Tenant  $tenant
     * @return Application|Factory|View
     */
    public function edit(Tenant $tenant): View|Factory|Application
    {
        $banner = $this->tenantService->findById($tenant->id);

        return view('tenant::edit', compact('tenant'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Tenant  $tenant
     * @return RedirectResponse
     */
    public function update(Update $request, Tenant $tenant): RedirectResponse
    {
        $banner = $this->tenantService->update($tenant->id, $request->validated());

        return redirect()->route('tenant.edit', $tenant);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Tenant  $tenant
     * @return RedirectResponse
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->tenantService->delete($tenant->id);

        return redirect()->route('tenant.index');
    }
}
