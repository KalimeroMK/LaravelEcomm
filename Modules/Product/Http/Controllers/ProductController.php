<?php

namespace Modules\Product\Http\Controllers;

use Exception;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Product\Http\Requests\Api\Search;
use Modules\Product\Http\Requests\Store;
use Modules\Product\Http\Requests\Update;
use Modules\Product\Models\Product;
use Modules\Product\Service\ProductService;

class ProductController extends CoreController
{
    private ProductService $product_service;

    public function __construct(ProductService $product_service)
    {
        $this->product_service = $product_service;
        $this->authorizeResource(Product::class, 'product');
    }

    public function index(Search $request): Renderable
    {
        return view('product::index', ['products' => $this->product_service->getAll($request->validated())]);
    }

    public function create(): Renderable
    {
        return view('product::create')->with($this->product_service->create());
    }

    /**
     * @throws Exception
     */
    public function store(Store $request): RedirectResponse
    {
        $product = $this->product_service->store($request->all());
        if (request()->hasFile('images')) {
            $product->addMultipleMediaFromRequest(['images'])->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('product');
            });
        }
        return redirect()->route('product.index');
    }

    public function edit(Product $product): Renderable
    {
        return view('product::edit')->with($this->product_service->edit($product->id));
    }

    /**
     * @throws Exception
     */
    public function update(Update $request, Product $product): RedirectResponse
    {
        $this->product_service->update($product->id, $request->all());
        if (request()->hasFile('images')) {
            $product->addMultipleMediaFromRequest(['images'])->each(function ($fileAdder) {
                $fileAdder->preservingOriginal()->toMediaCollection('product');
            });
        }
        return redirect()->route('product.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->product_service->destroy($product->id);
        return redirect()->route('product.index');
    }

    public function deleteMedia(int $modelId, int $mediaId): RedirectResponse
    {
        $model = Product::findOrFail($modelId);
        $model->media()->where('id', $mediaId)->first()->delete();

        return back()->with('success', 'Media deleted successfully.');
    }

}
