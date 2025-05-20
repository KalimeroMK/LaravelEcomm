<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Bundle\Actions\CreateBundleAction;
use Modules\Bundle\Actions\DeleteBundleAction;
use Modules\Bundle\Actions\UpdateBundleAction;
use Modules\Bundle\DTO\BundleDTO;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Http\Requests\Update;
use Modules\Bundle\Models\Bundle;
use Modules\Product\Models\Product;
use Throwable;

class BundleController extends Controller
{
    protected CreateBundleAction $createAction;
    protected UpdateBundleAction $updateAction;
    protected DeleteBundleAction $deleteAction;

    public function __construct(
        CreateBundleAction $createAction,
        UpdateBundleAction $updateAction,
        DeleteBundleAction $deleteAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->authorizeResource(Bundle::class, 'bundle');
    }

    public function index(): View
    {
        $bundles = Bundle::all();

        return view('bundle::index', ['bundles' => $bundles]);
    }

    public function create(): View
    {
        $products = Product::all();
        $bundle = new Bundle;

        return view('bundle::create', ['products' => $products, 'bundle' => $bundle]);
    }

    /**
     * @throws Throwable
     */
    public function store(Store $request): RedirectResponse
    {
        $dto = BundleDTO::fromRequest($request);
        $this->createAction->execute($dto);

        return redirect()->route('bundles.index')->with('status', 'Bundle created successfully.');
    }

    public function edit(Bundle $bundle): View
    {
        $products = Product::all();

        return view('bundle::edit', ['bundle' => $bundle, 'products' => $products]);
    }

    /**
     * @throws Throwable
     */
    public function update(Update $request, Bundle $bundle): RedirectResponse
    {
        $dto = BundleDTO::fromRequest($request)->withId($bundle->id);
        $this->updateAction->execute($dto);

        return redirect()->route('bundles.edit', $bundle)->with('status', 'Bundle updated successfully.');
    }

    public function destroy(Bundle $bundle): RedirectResponse
    {
        $this->deleteAction->execute($bundle->id);

        return redirect()->route('bundles.index')->with('status', 'Bundle deleted successfully.');
    }

    /**
     * Deletes a media item associated with a bundle.
     *
     * @param  int  $modelId  The ID of the bundle.
     * @param  int  $mediaId  The ID of the media to be deleted.
     */
    public function deleteMedia(int $modelId, int $mediaId): RedirectResponse
    {
        $model = Bundle::findOrFail($modelId);
        $media = $model->media()->where('id', $mediaId)->firstOrFail();
        $media->delete();

        return redirect()->route('bundles.index')->with('status', 'Media deleted successfully.');
    }
}
