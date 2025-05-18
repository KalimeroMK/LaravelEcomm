<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Brand\Http\Requests\Store;
use Modules\Brand\Models\Brand;
use Modules\Brand\Service\BrandService;
use Modules\Core\Http\Controllers\CoreController;

class BrandController extends CoreController
{
    protected BrandService $brand_service;

    public function __construct(BrandService $brand_service)
    {
        $this->brand_service = $brand_service;
        $this->authorizeResource(Brand::class, 'brand');
    }

    public function index(): View
    {
        $brands = $this->brand_service->getAll();

        return view('brand::index', ['brands' => $brands]);
    }

    public function create(): View
    {
        return view('brand::create', ['brand' => new Brand]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->brand_service->createWithMedia($request->validated());

        return redirect()->route('brands.index')->with('status', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('brand::edit', ['brand' => $brand]);
    }

    public function update(Store $request, Brand $brand): RedirectResponse
    {
        $this->brand_service->updateWithMedia($brand->id, $request->validated());

        return redirect()->route('brands.edit', $brand)->with('status', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->brand_service->delete($brand->id);

        return redirect()->route('brands.index')->with('status', 'Brand deleted successfully.');
    }
}
