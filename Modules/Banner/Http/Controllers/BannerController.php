<?php

    namespace Modules\Banner\Http\Controllers;

    use App\Http\Controllers\Controller;
    use App\Traits\ImageUpload;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Modules\Banner\Http\Requests\Store;
    use Modules\Banner\Http\Requests\Update;
    use Modules\Banner\Models\Banner;

    class BannerController extends Controller
    {
        public function __construct()
        {
            $this->middleware('permission:banner-list');
            $this->middleware('permission:banner-create', ['only' => ['create', 'store']]);
            $this->middleware('permission:banner-edit', ['only' => ['edit', 'update']]);
            $this->middleware('permission:banner-delete', ['only' => ['destroy']]);
        }

        use ImageUpload;

        /**
         * Display a listing of the resource.
         *
         * @return Application|Factory|View
         */
        public function index()
        {
            $banners = Banner::orderBy('id', 'DESC')->paginate(10);

            return view('banner::index', compact('banners'));
        }

        /**
         * Show the form for creating a new resource.
         *
         * @return Application|Factory|View
         */
        public function create()
        {
            return view('banner::create', ['banner' => new Banner()]);
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
            $banner = Banner::create(
                $request->except('photo') + [
                    'photo' => $this->verifyAndStoreImage($request),
                ]
            );
            if ($banner) {
                request()->session()->flash('success', 'Banner successfully added');
            } else {
                request()->session()->flash('error', 'Error occurred while adding banner');
            }

            return redirect()->route('banners.edit', $banner);
        }

        /**
         * Show the form for editing the specified resource.
         *
         * @param  Banner  $banner
         *
         * @return Application|Factory|View
         */
        public function edit(Banner $banner)
        {
            return view('banner::edit', compact('banner'));
        }

        /**
         * Update the specified resource in storage.
         *
         * @param  Update  $request
         * @param  Banner  $banner
         *
         * @return RedirectResponse
         */
        public function update(Update $request, Banner $banner): RedirectResponse
        {
            $banner = $banner->update($request->all());
            if ($banner) {
                request()->session()->flash('success', 'Banner successfully updated');
            } else {
                request()->session()->flash('error', 'Error occurred while updating banner');
            }

            return redirect()->route('banners.edit', $banner);
        }

        /**
         * Remove the specified resource from storage.
         *
         * @param  Banner  $banner
         *
         * @return RedirectResponse
         */
        public function destroy(Banner $banner): RedirectResponse
        {
            $status = $banner->delete();
            if ($status) {
                request()->session()->flash('success', 'Banner successfully deleted');
            } else {
                request()->session()->flash('error', 'Error occurred while deleting banner');
            }

            return redirect()->route('banners.index');
        }

        /**
         * Make paths for storing images.
         *
         * @return object
         */
        public function makePaths(): object
        {
            $original  = public_path().'/uploads/images/banner/';
            $thumbnail = public_path().'/uploads/images/banner/thumbnails/';
            $medium    = public_path().'/uploads/images/banner/medium/';

            return (object)compact('original', 'thumbnail', 'medium');
        }
    }
