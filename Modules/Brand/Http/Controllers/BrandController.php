<?php

namespace Modules\Brand\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Brand\Http\Requests\Api\Search;
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

    public function index(Search $request): View
    {
        $brands = $this->brand_service->getAll($request->validated());

        return view('brand::index', compact('brands'));
    }

    public function create(): View
    {
        return view('brand::create', ['brand' => new Brand()]);
    }

    public function store(Store $request): RedirectResponse
    {
        $this->brand_service->store($request->validated());

        return redirect()->route('brands.index')->with('status', 'Brand created successfully.');
    }

    public function edit(Brand $brand): View
    {
        return view('brand::edit', compact('brand'));
    }

    public function update(Store $request, Brand $brand): RedirectResponse
    {
        $this->brand_service->update($brand->id, $request->validated());

        return redirect()->route('brands.edit', $brand)->with('status', 'Brand updated successfully.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->brand_service->destroy($brand->id);

        return redirect()->route('brands.index')->with('status', 'Brand deleted successfully.');
    }
}
