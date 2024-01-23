<?php

namespace Modules\Bundle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Brand\Models\Brand;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Service\BundleService;

class BundleController extends Controller
{
    protected BundleService $bundleService;

    public function __construct(BundleService $bundleService)
    {
        $this->bundleService = $bundleService;
        $this->authorizeResource(Bundle::class, 'bundles');
    }

    public function index(): View
    {
        $bundles = $this->bundleService->getAll();

        return view('bundle::index', compact('bundles'));
    }

    public function create(): View
    {
        return view('bundle::create')->with($this->bundleService->create());
    }

    public function store(Store $request): RedirectResponse
    {
        $this->bundleService->store($request->validated());

        return redirect()->route('bundles.index')->with('status', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('bundle::edit', compact('brand'));
    }

    public function update(Store $request, Brand $brand): RedirectResponse
    {
        $this->bundleService->update($brand->id, $request->validated());

        return redirect()->route('bundles.edit', $brand)->with('status', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->bundleService->destroy($brand->id);

        return redirect()->route('bundles.index')->with('status', 'Brand deleted successfully.');
    }
}
