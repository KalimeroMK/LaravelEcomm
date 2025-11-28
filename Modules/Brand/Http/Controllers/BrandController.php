<?php

declare(strict_types=1);

namespace Modules\Brand\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Brand\Actions\CreateBrandAction;
use Modules\Brand\Actions\DeleteBrandAction;
use Modules\Brand\Actions\UpdateBrandAction;
use Modules\Brand\DTOs\BrandDTO;
use Modules\Brand\Http\Requests\Store;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Core\Support\Media\MediaUploader;

class BrandController extends CoreController
{
    public function __construct(
        private readonly BrandRepository $repository,
        private readonly CreateBrandAction $createAction,
        private readonly UpdateBrandAction $updateAction,
        private readonly DeleteBrandAction $deleteAction
    ) {
        $this->authorizeResource(Brand::class, 'brand');
    }

    public function index(): View
    {
        return view('brand::index', [
            'brands' => $this->repository->findAll(),
        ]);
    }

    public function create(): View
    {
        return view('brand::create', [
            'brand' => new Brand,
        ]);
    }

    public function store(Store $request): RedirectResponse
    {
        $brand = $this->createAction->execute(BrandDTO::fromRequest($request));
        /**
         * @var Brand $brand
         */
        MediaUploader::uploadMultiple($brand, ['images'], 'brand');

        return redirect()->route('brands.index')
            ->with('success', __('Brand created successfully.'));
    }

    public function edit(Brand $brand): View
    {
        return view('brand::edit', [
            'brand' => $brand,
        ]);
    }

    public function update(Store $request, Brand $brand): RedirectResponse
    {
        $dto = BrandDTO::fromRequest($request, $brand->id, $brand);
        $brand = $this->updateAction->execute($dto);
        MediaUploader::uploadMultiple($brand, ['images'], 'brand');

        return redirect()->route('brands.edit', $brand)
            ->with('success', __('Brand updated successfully.'));
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $this->deleteAction->execute($brand->id);

        return redirect()->route('brands.index')
            ->with('success', __('Brand deleted successfully.'));
    }
}
