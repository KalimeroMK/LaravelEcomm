<?php

    namespace App\Http\Controllers\Admin;

    use App\Http\Controllers\Controller;
    use App\Http\Requests\Product\Store;
    use App\Models\Brand;
    use App\Models\Category;
    use App\Models\Product;
    use App\Traits\ImageUpload;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;

    class ProductController extends Controller
    {
        public function __construct()
        {
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
            return view('backend.product.index', compact('products'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return Application|Factory|View
         */
        public function create()
        {
            $brands = Brand::get();
            $categories = Category::get();
            return view('backend.product.create', compact('brands', 'categories'));
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  Store  $request
         * @return RedirectResponse
         */
        public function store(Store $request): RedirectResponse
        {
            $data = $request->all();
            $data['is_featured'] = $request->input('is_featured', 0);
            $size = $request->input('size');
            $color = $request->input('color');
            if ($size) {
                $data['size'] = implode(',', $size);
            } else {
                $data['size'] = '';
            }
            if ($color) {
                $color = preg_replace('/\s+/', '', $color);
                $data['color'] = implode(',', $color);
            } else {
                $data['color'] = '';
            }
            $data['photo'] = $this->verifyAndStoreImage($request);

            $product = Product::create($data);
            $product->categories()->attach($request['category']);

            request()->session()->flash('success', 'Product Successfully added');

            return redirect()->route('products.index');
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  Product  $product
         * @return Application|Factory|View
         */
        public function edit(Product $product)
        {
            $brands = Brand::get();
            $categories = Category::all();
            $items = Product::whereId($product->id)->get();
            return view('backend.product.edit', compact('brands', 'categories', 'items', 'product'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  Request  $request
         * @param  Product  $product
         * @return RedirectResponse
         */
        public function update(Request $request, Product $product): RedirectResponse
        {
            $data = $request->all();
            $data['is_featured'] = $request->input('is_featured', 0);
            $size = $request->input('size');
            $color = $request->input('color');
            if ($size) {
                $data['size'] = implode(',', $size);
            } else {
                $data['size'] = '';
            }
            if ($color) {
                $data['color'] = implode(',', $color);
            } else {
                $data['color'] = '';
            }
            if (!empty($data['photo'])) {
                $data['photo'] = $this->verifyAndStoreImage($request);
            }
            $status = $product->update($data);
            $product->categories()->sync($request->category, true);

            if ($status) {
                request()->session()->flash('success', 'Product Successfully updated');
            } else {
                request()->session()->flash('error', 'Please try again!!');
            }
            return redirect()->route('product.index');
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param  Product  $product
         * @return RedirectResponse
         */
        public function destroy(Product $product): RedirectResponse
        {
            $status = $product->delete();

            if ($status) {
                request()->session()->flash('success', 'Product successfully deleted');
            } else {
                request()->session()->flash('error', 'Error while deleting product');
            }
            return redirect()->route('product.index');
        }


        /**
         * Make paths for storing images.
         *
         * @return object
         */
        public function makePaths(): object
        {
            $original = public_path().'/uploads/images/products/';
            $thumbnail = public_path().'/uploads/images/products/thumbnails/';
            $medium = public_path().'/uploads/images/products/medium/';
            return (object)compact('original', 'thumbnail', 'medium');
        }
    }
