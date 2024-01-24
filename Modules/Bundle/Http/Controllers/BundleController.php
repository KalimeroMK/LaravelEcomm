<?php

namespace Modules\Bundle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Service\BundleService;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class BundleController extends Controller
{
    protected BundleService $bundleService;

    public function __construct(BundleService $bundleService)
    {
        $this->bundleService = $bundleService;
//        $this->authorizeResource(Bundle::class, 'bundles');
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
        $bundle = $this->bundleService->store($request->all());
        if (request()->hasFile('images')) {
            $bundle->addMultipleMediaFromRequest(['images'])->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('item_images');
            });
        }
        return redirect()->route('bundles.index')->with('status', 'Brand created successfully.');
    }

    public function edit(Bundle $bundle): View
    {
        return view('bundle::edit')->with($this->bundleService->edit($bundle->id));
    }

    /**
     * @throws FileDoesNotExist
     * @throws FileIsTooBig
     */
    public function update(Store $request, Bundle $bundle): RedirectResponse
    {
        $this->bundleService->update($bundle->id, $request->all());
        if (request()->hasFile('images')) {
            $bundle->addMultipleMediaFromRequest(['images'])->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('bundle');
            });
        }
        return redirect()->route('bundles.edit', $bundle)->with('status', 'Brand updated successfully.');
    }

    public function destroy(Bundle $bundle): RedirectResponse
    {
        $this->bundleService->destroy($bundle->id);

        return redirect()->route('bundles.index')->with('status', 'Brand deleted successfully.');
    }

    public function deleteMedia($modelId, $mediaId)
    {
        $model = Bundle::findOrFail($modelId);
        $model->media()->where('id', $mediaId)->first()->delete();

        return back()->with('success', 'Media deleted successfully.');
    }
}
