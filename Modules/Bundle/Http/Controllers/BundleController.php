<?php

declare(strict_types=1);

namespace Modules\Bundle\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Modules\Bundle\Actions\CreateBundleAction;
use Modules\Bundle\Actions\DeleteBundleAction;
use Modules\Bundle\Actions\DeleteBundleMediaAction;
use Modules\Bundle\Actions\FindBundleAction;
use Modules\Bundle\Actions\GetAllBundlesAction;
use Modules\Bundle\Actions\GetAllProductsAction;
use Modules\Bundle\Actions\UpdateBundleAction;
use Modules\Bundle\DTOs\BundleDTO;
use Modules\Bundle\Http\Requests\Store;
use Modules\Bundle\Http\Requests\Update;
use Modules\Bundle\Models\Bundle;
use Throwable;

class BundleController extends Controller
{
    protected CreateBundleAction $createAction;

    protected UpdateBundleAction $updateAction;

    protected DeleteBundleAction $deleteAction;

    protected GetAllBundlesAction $getAllBundlesAction;

    protected FindBundleAction $findBundleAction;

    protected GetAllProductsAction $getAllProductsAction;

    protected DeleteBundleMediaAction $deleteBundleMediaAction;

    public function __construct(
        CreateBundleAction $createAction,
        UpdateBundleAction $updateAction,
        DeleteBundleAction $deleteAction,
        GetAllBundlesAction $getAllBundlesAction,
        FindBundleAction $findBundleAction,
        GetAllProductsAction $getAllProductsAction,
        DeleteBundleMediaAction $deleteBundleMediaAction
    ) {
        $this->createAction = $createAction;
        $this->updateAction = $updateAction;
        $this->deleteAction = $deleteAction;
        $this->getAllBundlesAction = $getAllBundlesAction;
        $this->findBundleAction = $findBundleAction;
        $this->getAllProductsAction = $getAllProductsAction;
        $this->deleteBundleMediaAction = $deleteBundleMediaAction;
        $this->authorizeResource(Bundle::class, 'bundle');
    }

    public function index(): View
    {
        $bundles = $this->getAllBundlesAction->execute();

        return view('bundle::index', ['bundles' => $bundles]);
    }

    public function create(): View
    {
        $products = $this->getAllProductsAction->execute();
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
        $products = $this->getAllProductsAction->execute();

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
        $this->deleteBundleMediaAction->execute($modelId, $mediaId);

        return back()->with('success', 'Media deleted successfully.');
    }
}
