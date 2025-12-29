<?php

declare(strict_types=1);

namespace Modules\Tenant\Http\Controllers;

use Illuminate\Contracts\View\Factory;
use Illuminate\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Tenant\Actions\CreateTenantAction;
use Modules\Tenant\Actions\DeleteTenantAction;
use Modules\Tenant\Actions\FindTenantAction;
use Modules\Tenant\Actions\GetAllTenantsAction;
use Modules\Tenant\Actions\UpdateTenantAction;
use Modules\Tenant\DTOs\TenantDTO;
use Modules\Tenant\Http\Requests\Store;
use Modules\Tenant\Http\Requests\Update;
use Modules\Tenant\Models\Tenant;

class TenantController extends CoreController
{
    public function __construct(
        private readonly GetAllTenantsAction $getAllAction,
        private readonly FindTenantAction $findAction,
        private readonly CreateTenantAction $createAction,
        private readonly UpdateTenantAction $updateAction,
        private readonly DeleteTenantAction $deleteAction
    ) {
        $this->authorizeResource(Tenant::class, 'tenant');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): Factory|View|Application
    {
        return view('tenant::index', ['tenants' => $this->getAllAction->execute()]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View|Factory|Application
    {
        return view('tenant::create', ['tenant' => new Tenant]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Store $request): RedirectResponse
    {
        $dto = TenantDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('tenant.index')->with('status', __('messages.created_successfully', ['resource' => 'Tenant']));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tenant $tenant): View|Factory|Application
    {
        $tenant = $this->findAction->execute($tenant->id);

        return view('tenant::edit', ['tenant' => $tenant]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Update $request, Tenant $tenant): RedirectResponse
    {
        $dto = TenantDTO::fromRequest($request, $tenant->id, $tenant);
        $this->updateAction->execute($tenant->id, $dto);

        return redirect()->route('tenant.edit', $tenant)->with('status', __('messages.updated_successfully', ['resource' => 'Tenant']));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        $this->deleteAction->execute($tenant->id);

        return redirect()->route('tenant.index')->with('status', __('messages.deleted_successfully', ['resource' => 'Tenant']));
    }
}
