<?php

namespace Modules\Product\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Exporter;
use Modules\Core\Http\Controllers\CoreController;
use Modules\Post\Http\Requests\ImportRequest;
use Modules\Product\Http\Requests\Api\Search;
use Modules\Product\Http\Requests\Store;
use Modules\Product\Http\Requests\Update;
use Modules\Product\Import\Products;
use Modules\Product\Import\Products as ProductImport;
use Modules\Product\Models\Product;
use Modules\Product\Service\ProductService;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends CoreController
{
    private ProductService $product_service;
    private Exporter $excel;

    public function __construct(ProductService $product_service, Exporter $excel)
    {
        $this->product_service = $product_service;
        $this->excel = $excel;
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
     * @throws \Exception
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
     * @throws \Exception
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

    /**
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(): BinaryFileResponse
    {
        return $this->excel->download(new Products, 'Products.xlsx');
    }

    public function import(ImportRequest $request): RedirectResponse
    {
        $this->excel->import(new ProductImport(), $request->file('file'));
        return redirect()->back();
    }

    public function deleteMedia($modelId, $mediaId)
    {
        $model = Product::findOrFail($modelId);
        $model->media()->where('id', $mediaId)->first()->delete();

        return back()->with('success', 'Media deleted successfully.');
    }

}
