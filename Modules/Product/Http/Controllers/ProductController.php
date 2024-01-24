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
        $this->product_service->store($request->all());
        return redirect()->route('products.index');
    }

    public function edit(Product $product): Renderable
    {
        return view('product::edit')->with($this->product_service->edit($product->id));
    }

    public function update(Update $request, Product $product): RedirectResponse
    {
        $this->product_service->update($product->id, $request->all());
        return redirect()->route('products.index');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $this->product_service->destroy($product->id);
        return redirect()->route('products.index');
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

}
