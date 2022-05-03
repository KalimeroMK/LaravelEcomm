<?php

    namespace Modules\Brand\Http\Controllers;

    use App\Http\Controllers\Controller;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Modules\Brand\Http\Requests\Store;
    use Modules\Brand\Models\Brand;
    use Modules\Brand\Repository\BrandRepository;

    class BrandController extends Controller
    {
        private BrandRepository $brand;

        public function __construct(BrandRepository $brand)
        {
            $this->brand = $brand;
            $this->middleware('permission:brand-list');
            $this->middleware('permission:brand-create', ['only' => ['create', 'store']]);
            $this->middleware('permission:brand-edit', ['only' => ['edit', 'update']]);
            $this->middleware('permission:brand-delete', ['only' => ['destroy']]);
        }

        /**
         * Display a listing of the resource.
         *
         * @return Application|Factory|View
         */
        public function index()
        {
            $brands = $this->brand->getAll();

            return view('brand::index', compact('brands'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return Application|Factory|View
         */
        public function create()
        {
            return view('brand::create');
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
            $data  = $request->all();
            $brand = $this->brand->storeBrand($data);

            return redirect()->route('brands.edit', $brand);
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  Brand  $brand
         *
         * @return Application|Factory|View
         */
        public function edit(Brand $brand)
        {
            return view('brand::edit', compact('brand'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  Store  $request
         * @param  Brand  $brand
         *
         * @return RedirectResponse
         */
        public function update(Store $request, Brand $brand): RedirectResponse
        {
            $data  = $request->all();
            $brand = $this->brand->updateBrand($data, $brand->id);

            return redirect()->route('brands.edit', $brand);
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param  Brand  $brand
         *
         * @return RedirectResponse
         */
        public function destroy(Brand $brand): RedirectResponse
        {
            $status = $brand->delete();
            if ($status) {
                request()->session()->flash('success', 'Brand successfully deleted');
            } else {
                request()->session()->flash('error', 'Error, Please try again');
            }

            return redirect()->route('brands.index');
        }
    }
