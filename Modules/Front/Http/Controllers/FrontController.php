<?php

    namespace Modules\Front\Http\Controllers;

    use App\Events\MessageSent;
    use Illuminate\Contracts\Foundation\Application;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Contracts\View\View;
    use Illuminate\Http\RedirectResponse;
    use Illuminate\Http\Request;
    use Illuminate\Http\Response;
    use Illuminate\Routing\Controller;
    use JetBrains\PhpStorm\NoReturn;
    use Modules\Banner\Models\Banner;
    use Modules\Cart\Models\Cart;
    use Modules\Category\Models\Category;
    use Modules\Coupon\Models\Coupon;
    use Modules\Message\Http\Requests\Store;
    use Modules\Message\Models\Message;
    use Modules\Post\Models\Post;
    use Modules\Product\Models\Product;
    use Spatie\Newsletter\Newsletter;
    use Spatie\SchemaOrg\Brand;

    class FrontController extends Controller
    {
        /**
         * @return Application|Factory|View
         */
        public function index()
        {
            $featured_products = Product::with('categories')->orderBy('price', 'DESC')->limit(4)->get();
            $posts             = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $banners           = Banner::whereStatus('active')->limit(3)->orderBy('id', 'DESC')->get();
            $product_lists     = Product::with('categories')->where('condition', '!=', 'hot')->whereStatus('active')->orderBy('id',
                'DESC')->limit(9)->get();
            $product_hot       = Product::with('categories')->whereCondition('hot')->whereStatus('active')->orderBy('id',
                'DESC')->limit(9)->get();

            return view('front::index',
                compact('featured_products', 'posts', 'banners', 'product_lists', 'product_hot'));
        }

        /**
         * @return Application|Factory|View
         */
        public function aboutUs()
        {
            return view('front::pages.about-us');
        }

        /**
         * @return Application|Factory|View
         */
        public function contact()
        {
            return view('front::pages.contact');
        }

        /**
         * @param $slug
         *
         * @return Application|Factory|View
         */
        public function productDetail($slug)
        {
            $product_detail = Product::getProductBySlug($slug);
            $related        = Product::whereHas('categories', static function ($q) use ($product_detail) {
                return $q->whereIn('title', $product_detail->categories->pluck('title'));
            })->where('id', '!=', $product_detail->id) // So you won't fetch same product
                                     ->take(8)->get();

            return view('front::pages.product_detail', compact('product_detail', 'related'));
        }

        /**
         * @return array|Application|Factory|View
         */
        public function productGrids()
        {
            $products = Product::query();

            if ( ! empty($_GET['category'])) {
                $slug    = explode(',', $_GET['category']);
                $cat_ids = Category::select('id')->whereIn('slug', $slug)->pluck('id')->toArray();
                $products->whereIn('cat_id', $cat_ids);
            }
            if ( ! empty($_GET['brand'])) {
                $slugs     = explode(',', $_GET['brand']);
                $brand_ids = Brand::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();

                return $brand_ids;
            }
            if ( ! empty($_GET['sortBy'])) {
                if ($_GET['sortBy'] == 'title') {
                    $products = $products->where('status', 'active')->orderBy('title', 'ASC');
                }
                if ($_GET['sortBy'] == 'price') {
                    $products = $products->orderBy('price', 'ASC');
                }
            }

            if ( ! empty($_GET['price'])) {
                $price = explode('-', $_GET['price']);
                $products->whereBetween('price', $price);
            }

            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
            if ( ! empty($_GET['show'])) {
                $products = $products->where('status', 'active')->paginate($_GET['show']);
            } else {
                $products = $products->where('status', 'active')->paginate(9);
            }

            $max    = Product::max('price');
            $brands = Brand::orderBy('title', 'ASC')->whereStatus('active')->get();

            return view('front::pages.product-grids', compact('recent_products', 'products', 'max', 'brands'));
        }

        /**
         * @return array|Application|Factory|View
         */
        public function productLists()
        {
            $products = Product::query();

            if ( ! empty($_GET['category'])) {
                $slug    = explode(',', $_GET['category']);
                $cat_ids = Category::select('id')->whereIn('slug', $slug)->pluck('id')->toArray();
                $products->whereIn('cat_id', $cat_ids)->paginate;
            }
            if ( ! empty($_GET['brand'])) {
                $slugs = explode(',', $_GET['brand']);

                return Brand::select('id')->whereIn('slug', $slugs)->pluck('id')->toArray();
            }
            if ( ! empty($_GET['sortBy'])) {
                if ($_GET['sortBy'] == 'title') {
                    $products = $products->where('status', 'active')->orderBy('title', 'ASC');
                }
                if ($_GET['sortBy'] == 'price') {
                    $products = $products->orderBy('price', 'ASC');
                }
            }

            if ( ! empty($_GET['price'])) {
                $price = explode('-', $_GET['price']);

                $products->whereBetween('price', $price);
            }

            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
            if ( ! empty($_GET['show'])) {
                $products = $products->where('status', 'active')->paginate($_GET['show']);
            } else {
                $products = $products->where('status', 'active')->paginate(6);
            }

            return view('front::pages.product-lists', compact('products', 'recent_products'));
        }

        /**
         * @param  Request  $request
         *
         * @return RedirectResponse
         */
        public function productFilter(Request $request): RedirectResponse
        {
            $data    = $request->all();
            $showURL = "";
            if ( ! empty($data['show'])) {
                $showURL .= '&show='.$data['show'];
            }

            $sortByURL = '';
            if ( ! empty($data['sortBy'])) {
                $sortByURL .= '&sortBy='.$data['sortBy'];
            }

            $catURL = "";
            if ( ! empty($data['category'])) {
                foreach ($data['category'] as $category) {
                    if (empty($catURL)) {
                        $catURL .= '&category='.$category;
                    } else {
                        $catURL .= ','.$category;
                    }
                }
            }

            $brandURL = "";
            if ( ! empty($data['brand'])) {
                foreach ($data['brand'] as $brand) {
                    if (empty($brandURL)) {
                        $brandURL .= '&brand='.$brand;
                    } else {
                        $brandURL .= ','.$brand;
                    }
                }
            }
            // return $brandURL;

            $priceRangeURL = "";
            if ( ! empty($data['price_range'])) {
                $priceRangeURL .= '&price='.$data['price_range'];
            }
            if (request()->is('e-shop.loc/product-grids')) {
                return redirect()->route('product-grids', $catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            } else {
                return redirect()->route('product-lists', $catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
        }

        /**
         * @param  Request  $request
         *
         * @return Application|Factory|View
         */
        public function productSearch(Request $request)
        {
            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
            $products        = Product::orwhere('title', 'like', '%'.$request->search.'%')
                                      ->orwhere('slug', 'like', '%'.$request->search.'%')
                                      ->orwhere('description', 'like', '%'.$request->search.'%')
                                      ->orwhere('summary', 'like', '%'.$request->search.'%')
                                      ->orwhere('price', 'like', '%'.$request->search.'%')
                                      ->orderBy('id', 'DESC')
                                      ->paginate('9');

            return view('front::pages.product-grids', compact('products', $recent_products));
        }

        /**
         * @param  Request  $request
         *
         * @return Application|Factory|View
         */
        public function productBrand(Request $request)
        {
            $products        = Brand::getProductByBrand($request->slug);
            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
            if (request()->is('e-shop.loc/product-grids')) {
                return view('front::pages.product-grids', compact('products', 'recent_products'));
            } else {
                return view('front::pages.product-lists', compact('products', 'recent_products'));
            }
        }

        /**
         * @param $slug
         *
         * @return Application|Factory|View
         */
        public function productCat($slug)
        {
            $category        = Category::whereSlug($slug)->firstOrFail();
            $products        = Product::whereHas('categories', static function ($q) use ($category) {
                $q->where('title', '=', $category->title);
            })->paginate(10);
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $brands          = Brand::orderBy('title', 'ASC')->where('status', 'active')->get();
            if (request()->is('e-shop.loc/product-grids')) {
                return view('front::pages.product-grids', compact('products', 'recent_products', 'brands'));
            } else {
                return view('front::pages.product-lists', compact('products', 'recent_products', 'brands'));
            }
        }

        /**
         * @return Application|Factory|View
         */
        public function blog()
        {
            $posts       = Post::with(['categories', 'author_info'])->whereStatus('active')->orderBy('id', 'DESC')->paginate(9);
            $recantPosts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            return view('front::pages.blog', compact('posts', 'recantPosts'));
        }

        /**
         * @param $slug
         *
         * @return Application|Factory|View
         */
        public function blogDetail($slug)
        {
            $post        = Post::getPostBySlug($slug);
            $recantPosts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            return view('front::pages.blog-detail', compact('post', 'recantPosts'));
        }

        /**
         * @param  Request  $request
         *
         * @return Application|Factory|View
         */
        public function blogSearch(Request $request)
        {
            $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
            $posts        = Post::orwhere('title', 'like', '%'.$request->search.'%')
                                ->orwhere('quote', 'like', '%'.$request->search.'%')
                                ->orwhere('summary', 'like', '%'.$request->search.'%')
                                ->orwhere('description', 'like', '%'.$request->search.'%')
                                ->orwhere('slug', 'like', '%'.$request->search.'%')
                                ->orderBy('id', 'DESC')
                                ->paginate(8);

            return view('front::pages.blog', compact('recent_posts', 'posts'));
        }

        /**
         * @param  Request  $request
         *
         * @return RedirectResponse
         */
        public function blogFilter(Request $request): RedirectResponse
        {
            $data = $request->all();
            // return $data;
            $catURL = "";
            if ( ! empty($data['category'])) {
                foreach ($data['category'] as $category) {
                    if (empty($catURL)) {
                        $catURL .= '&category='.$category;
                    } else {
                        $catURL .= ','.$category;
                    }
                }
            }

            $tagURL = "";
            if ( ! empty($data['tag'])) {
                foreach ($data['tag'] as $tag) {
                    if (empty($tagURL)) {
                        $tagURL .= '&tag='.$tag;
                    } else {
                        $tagURL .= ','.$tag;
                    }
                }
            }

            return redirect()->route('blog', $catURL.$tagURL);
        }

        /**
         * @param  Request  $request
         *
         * @return Application|Factory|View
         */
        public function blogByCategory(Request $request)
        {
            $category    = Category::whereSlug($request->slug)->firstOrFail();
            $posts       = Post::with('author_info')->whereHas('categories', static function ($q) use ($category) {
                $q->where('title', '=', $category->title);
            })->paginate(10);
            $recantPosts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            return view('front::pages.blog', compact('posts', 'recantPosts'));
        }

        /**
         * @param  Request  $request
         *
         * @return Application|Factory|View
         */
        public function blogByTag(Request $request)
        {
            $posts        = Post::getBlogByTag($request->slug);
            $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            return view('front::pages.blog', compact('posts', 'recent_posts'));
        }

        /**
         * @return Application|Factory|View
         */

        /**
         * @param  Request  $request
         *
         * @return RedirectResponse
         */
        public function couponStore(Request $request): RedirectResponse
        {
            $coupon = Coupon::whereCode($request->code)->first();
            if ( ! $coupon) {
                request()->session()->flash('error', 'Invalid coupon code, Please try again');

                return back();
            }
            $total_price = Cart::whereUserId(auth()->user()->id)->where('order_id', null)->sum('price');
            session()->put('coupon', [
                'id'    => $coupon->id,
                'code'  => $coupon->code,
                'value' => $coupon->discount($total_price),
            ]);
            request()->session()->flash('success', 'Coupon successfully applied');

            return redirect()->back();
        }

        public function subscribe(Request $request): RedirectResponse
        {
            if ( ! Newsletter::isSubscribed($request->email)) {
                Newsletter::subscribePending($request->email);
                if (Newsletter::lastActionSucceeded()) {
                    request()->session()->flash('success', 'Subscribed! Please check your email');

                    return redirect()->route('home');
                } else {
                    Newsletter::getLastError();

                    return back()->with('error', 'Something went wrong! please try again');
                }
            } else {
                request()->session()->flash('error', 'Already Subscribed');

                return back();
            }
        }

        /**
         * Store a newly created resource in storage.
         *
         * @param  Store  $request
         *
         * @return Response
         */
        #[NoReturn] public function messageStore(Store $request): Response
        {
            $message = Message::create($request->all());
            // return $message;
            $data            = [];
            $data['url']     = route('message.show', $message->id);
            $data['date']    = $message->created_at->format('F d, Y h:i A');
            $data['name']    = $message->name;
            $data['email']   = $message->email;
            $data['phone']   = $message->phone;
            $data['message'] = $message->message;
            $data['subject'] = $message->subject;
            $data['photo']   = Auth()->user()->photo;
            // return $data;
            event(new MessageSent($data));
            exit();
        }
    }