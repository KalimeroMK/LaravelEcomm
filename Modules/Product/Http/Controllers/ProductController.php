<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Post\Http\Requests\ImportRequest;
use Modules\Product\Export\Products;
use Modules\Product\Http\Requests\Store;
use Modules\Product\Http\Requests\Update;
use Modules\Product\Import\Products as ProductImport;
use Modules\Product\Models\Product;
use Modules\Product\Service\ProductService;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    private ProductService $product_service;
    
    public function __construct(ProductService $product_service)
    {
        $this->product_service = $product_service;
        $this->middleware('permission:product-list');
        $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Application|Factory|View
     */
    public function index()
    {
        return view('product::index', ['products' => $this->product_service->index()]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Application|Factory|View
     */
    public function create()
    {
        return view('product::create')->with($this->product_service->create());
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  Store  $request
     *
     * @return RedirectResponse
     */
    public function store(Store $request): RedirectResponse
    {
        $this->product_service->store($request->validated());
        
        return redirect()->route('products.index');
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  Product  $product
     *
     * @return Application|Factory|View
     */
    public function edit(Product $product)
    {
        return view('product::edit')->with($this->product_service->edit($product->id));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  Update  $request
     * @param  Product  $product
     *
     * @return RedirectResponse
     */
    public function update(Update $request, Product $product): RedirectResponse
    {
        $this->product_service->update($request->all(), $product->id);
        
        return redirect()->route('products.index');
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  Product  $product
     *
     * @return RedirectResponse
     */
    public function destroy(Product $product): RedirectResponse
    {
        $this->product_service->destroy($product->id);
        
        return redirect()->route('products.index');
    }
    
    /**
     * @return BinaryFileResponse
     */
    public function export()
    {
        return Excel::download(new Products, 'Products.xlsx');
    }
    
    /**
     * @return RedirectResponse
     */
    public function import(ImportRequest $request)
    {
        Excel::import(new ProductImport(), $request->file('file'));
        
        return redirect()->back();
    }
}
