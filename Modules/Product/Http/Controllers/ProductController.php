<?php

    namespace Modules\Product\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Traits\ImageUpload;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Modules\Brand\Models\Brand;
    use Modules\Category\Models\Category;
    use Modules\Product\Http\Requests\ProductReviewStore;
    use Modules\Product\Models\Product;
    use Modules\Product\Repository\ProductRepository;

    class ProductController extends Controller
    {
        private ProductRepository $product;

        public function __construct(ProductRepository $product)
        {
            $this->product = $product;
            $this->middleware('permission:product-list');
            $this->middleware('permission:product-create', ['only' => ['create', 'store']]);
            $this->middleware('permission:product-edit', ['only' => ['edit', 'update']]);
            $this->middleware('permission:product-delete', ['only' => ['destroy']]);
        }

        use ImageUpload;

        /**
         * Display a listing of the resource.
         *
         * @return Application|Factory|View
         */
        public function index()
        {
            $products = Product::with(['brand', 'categories'])->orderBy('id', 'desc')->paginate(10);

            return view('product::index', compact('products'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return Application|Factory|View
         */
        public function create()
        {
            $brands     = Brand::get();
            $categories = Category::get();
            $product    = new Product();

            return view('product::create', compact('brands', 'categories', 'product'));
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  ProductReviewStore  $request
         *
         * @return RedirectResponse
         */
        public function store(ProductReviewStore $request): RedirectResponse
        {
            $data = $request->all();
            $this->product->storeProduct($data);

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
            $brands     = Brand::get();
            $categories = Category::all();
            $items      = Product::whereId($product->id)->get();

            return view('product::edit', compact('brands', 'categories', 'items', 'product'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  Request  $request
         * @param  Product  $product
         *
         * @return RedirectResponse
         */
        public function update(Request $request, Product $product): RedirectResponse
        {
            $data  = $request->except('photo');
            $image = $request['photo'];
            $this->product->updateProduct($data, $image, $product->id);

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
            $status = $this->product->deleteProduct($product->id);

            if ($status) {
                request()->session()->flash('success', 'Product successfully deleted');
            } else {
                request()->session()->flash('error', 'Error while deleting product');
            }

            return redirect()->route('products.index');
        }
    }
