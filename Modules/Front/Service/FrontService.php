<?php

namespace Modules\Front\Service;

use App\Events\MessageSent;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Banner\Models\Banner;
use Modules\Brand\Models\Brand;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Coupon\Models\Coupon;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class FrontService
{
    public $model = Product::class;
    /**
     * Get data for the homepage.
     *
     * @return array
     */
    public function index(): array
    {
        // Get featured products
        $featured_products = Product::with('categories')
            ->orderBy('price', 'DESC')
            ->limit(4)
            ->get();

        // Get latest posts
        $posts = Post::where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(3)
            ->get();

        // Get active banners
        $banners = Banner::where('status', 'active')
            ->limit(3)
            ->orderBy('id', 'DESC')
            ->get();

        // Get latest and hot products
        $latest_hot_products = Product::with('categories', 'condition')
            ->where('status', 'active')
            ->orderBy('id', 'DESC')
            ->limit(9)
            ->get();

        // Split latest and hot products
        $latest_products = $latest_hot_products->take(4);
        $hot_products = $latest_hot_products->skip(4);

        // Return all data
        return [
            "featured_products" => $featured_products,
            "posts" => $posts,
            "banners" => $banners,
            "latest_products" => $latest_products,
            "hot_products" => $hot_products,
        ];
    }

    /**
     * @param $slug
     *
     * @return array|string
     */
    public function productCat($slug): array|string
    {
        $category = Category::whereSlug($slug)->first();

        if (!$category) {
            return 'Category not found';
        }

        $categoryTitle = $category->title;

        $products = Product::whereHas('categories', function ($query) use ($categoryTitle) {
            $query->where('title', $categoryTitle);
        })
            ->paginate(10);

        $recentProducts = Product::where('status', 'active')
            ->orderByDesc('id')
            ->take(3)
            ->get();

        $brands = Brand::where('status', 'active')
            ->orderBy('title')
            ->get();

        return [
            'brands'          => $brands,
            'recent_products' => $recentProducts,
            'products'        => $products,
        ];
    }


    /**
     * @param $request
     *
     * @return array|string
     */
    public function blogSearch(Request $request): array|string
    {
            $recentPosts = Post::where('status', 'active')->latest()->limit(3)->get();
            $posts = Post::whereLike(Post::likeRows, $request->input('search'))
                ->latest()
                ->paginate(8);

            return [
                "posts" => $posts,
                "recent_posts" => $recentPosts,
            ];

    }


    /**
     * @param $request
     *
     * @return string|void
     */
    public function messageStore($request)
    {
        try {
            $message = Message::create($request->validated());

            $data            = [];
            $data['url']     = route('message.show', $message->id);
            $data['date']    = $message->created_at->format('F d, Y h:i A');
            $data['name']    = $message->name;
            $data['email']   = $message->email;
            $data['phone']   = $message->phone;
            $data['message'] = $message->message;
            $data['subject'] = $message->subject;
            $data['photo']   = Auth()->user()->photo ?? '';

            event(new MessageSent($data));

            return;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }


    /**
     * Returns an array of products, brands, and recent products based on the provided parameters.
     *
     * The function accepts the following parameters via the $_GET superglobal:
     * - category: A comma-separated list of category slugs to filter the products by.
     * - brand: A comma-separated list of brand slugs to filter the products by.
     * - sortBy: A string representing the field to sort the products by ("title" or "price").
     * - price: A string representing the price range to filter the products by (e.g. "10-50").
     * - show: An integer representing the number of products to show per page.
     *
     * The function returns an array containing the following keys:
     * - brands: An array of all active brands in alphabetical order.
     * - recent_products: An array of the three most recent active products.
     * - products: A paginated array of all active products that match the provided parameters, sorted as specified.
     *
     * If an exception occurs during execution, the function returns an error message as a string.
     * @return array|string
     */
    public function productGrids(): array|string
    {
        // Retrieve query parameters from the request
        $queryParams = request()->only(['category', 'brand', 'price', 'show', 'sortBy']);
        $perPage = (int) ($queryParams['show'] ?? 9);

        // Retrieve category and brand IDs from slugs
        $categoryIds = Category::whereIn('slug', explode(',', $queryParams['category'] ?? ''))->pluck('id')->toArray();
        $brandIds = Brand::whereIn('slug', explode(',', $queryParams['brand'] ?? ''))->pluck('id')->toArray();

        // Retrieve min and max price from price range string
        [$minPrice, $maxPrice] = array_map('intval', explode('-', $queryParams['price'] ?? '0-'.PHP_INT_MAX));

        // Determine sort column and order
        $sortColumn = $queryParams['sortBy'] ?? 'created_at';
        $sortOrder = ($sortColumn === 'title') ? 'asc' : 'desc';
        if ($sortColumn === 'price') {
            $sortColumn = 'price';
            $sortOrder = 'asc';
        }

        // Query products with filters and pagination
        $products = $this->model::query()
            ->when($categoryIds, fn($query, $categoryIds) => $query->whereIn('cat_id', $categoryIds))
            ->when($brandIds, fn($query, $brandIds) => $query->whereIn('brand_id', $brandIds))
            ->when($minPrice || $maxPrice, fn($query) => $query->whereBetween('price', [$minPrice, $maxPrice]))
            ->orderBy($sortColumn, $sortOrder)
            ->with(['categories', 'brand', 'condition', 'tags', 'sizes'])
            ->paginate($perPage);

        // Retrieve brands and recent products for display
        $brands = Brand::whereStatus('active')->orderBy('title', 'ASC')->get();
        $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->take(3)->get();

        // Return view data
        return compact('brands', 'recent_products', 'products');
    }



    /**
     * @param $request
     *
     * @return array|string
     */
    public function blogByTag($request): array|string
    {
        try {
            $posts        = Post::getBlogByTag($request->slug);
            $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            return [
                "posts"        => $posts,
                "recent_posts" => $recent_posts,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Store coupon and apply it to user's cart.
     *
     * @param $request
     *
     * @return RedirectResponse|string
     */
    public function couponStore($request): RedirectResponse|string
    {
        try {
            $coupon = Coupon::whereCode($request->code)->first();
            if (!$coupon) {
                // Invalid coupon code, redirect back with error message.
                request()->session()->flash('error', 'Invalid coupon code, Please try again');
                return back();
            }

            // Get total price of user's cart.
            $total_price = Cart::whereUserId(Auth::id())->where('order_id', null)->sum('price');

            // Store coupon details in session.
            session()->put('coupon', [
                'id'    => $coupon->id,
                'code'  => $coupon->code,
                'value' => $coupon->discount($total_price),
            ]);

            // Redirect back with success message.
            request()->session()->flash('success', 'Coupon successfully applied');
            return redirect()->back();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Filter blog posts based on selected categories and tags.
     *
     * @param $request
     *
     * @return RedirectResponse|string
     */
    public function blogFilter($request): RedirectResponse|string
    {
        try {
            $catURL = "";
            if (!empty($request['category'])) {
                foreach ($request['category'] as $category) {
                    if (empty($catURL)) {
                        $catURL .= '&category=' . $category;
                    } else {
                        $catURL .= ',' . $category;
                    }
                }
            }

            $tagURL = "";
            if (!empty($request['tag'])) {
                foreach ($request['tag'] as $tag) {
                    if (empty($tagURL)) {
                        $tagURL .= '&tag=' . $tag;
                    } else {
                        $tagURL .= ',' . $tag;
                    }
                }
            }

            // Redirect to blog page with filtered categories and tags.
            return redirect()->route('blog', $catURL . $tagURL);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Get blog posts by category slug.
     *
     * @param $request
     *
     * @return array|string
     */
    public function blogByCategory($request): array|string
    {
        try {
            $posts       = Post::with('author_info')->whereHas('categories', static function ($q) use ($request) {
                $q->whereSlug($request->slug);
            })->paginate(10);
            $recantPosts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Return posts and recent posts for specified category.
            return [
                "posts"       => $posts,
                "recantPosts" => $recantPosts,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * @param $data
     * @return array|string
     */
    public function productSearch($data)
    {

            // Get recent products.
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Search products by name, description, and brand name.
            $products = Product::whereLike(Product::likeRows, Arr::get($data, 'search'))
                ->orderBy('id', 'DESC')
                ->paginate('9');

            return [
                "recent_products" => $recent_products,
                "products"        => $products,
                "brands"          => Brand::with('products')->get(),
            ];

    }

    /**
     * Get recent products, deal products, and brands for deal page.
     *
     * @return array|string The recent products, deal products, and brands.
     */
    public function productDeal(): array|string
    {
            // Get recent products.
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Get deal products.
            $products = Product::where('d_deal', true)
                ->orderBy('id', 'DESC')
                ->paginate('9');

            return [
                "recent_products" => $recent_products,
                "products"        => $products,
                "brands"          => Brand::with('products')->get(),
            ];

    }

    public function productDetail($slug): array
    {
        // Get the product detail by slug.
        $product_detail = Product::getProductBySlug($slug);

        $related = Product::with('categories')
            ->whereHas('categories', function ($q) use ($product_detail) {
                $q->whereIn('title', $product_detail->categories->pluck('title'));
            })
            ->where('id', '!=', $product_detail->id)
            ->limit(8)
            ->get();

        return [
            'product_detail' => $product_detail,
            'related' => $related,
        ];
    }


    /**
     * Get data for a blog post detail page.
     *
     * @param $slug
     *
     * @return array|string
     */
    public function blogDetail($slug): array|string
    {
            // Get post data
            $post = Post::getPostBySlug($slug);

            // Get recent posts
            $recentPosts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Return data
            return [
                "post"        => $post,
                "recantPosts" => $recentPosts,
            ];

    }

    /**
     * Get data for a product brand page.
     *
     * @param $request
     *
     * @return array|string
     */
    public function productBrand($request): array|string
    {
            // Get products for brand
            $products = Brand::getProductByBrand($request->slug);

            // Get recent products
            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            // Return data
            return [
                "products"        => $products,
                "recent_products" => $recent_products,
            ];
    }


    /**
     * Processes the product filter and redirects to either product-grids or product-lists route with the filtered parameters.
     *
     * @param $data
     * @return RedirectResponse|string A redirect response to either product-grids or product-lists route with the filtered parameters, or an error message.
     */
    public function productFilter($data): RedirectResponse|string
    {
        $query = [];

        if (filled($data['show'])) {
            $query['show'] = $data['show'];
        }

        if (filled($data['sortBy'])) {
            $query['sortBy'] = $data['sortBy'];
        }

        if (filled($data['category'])) {
            $query['category'] = implode(',', $data['category']);
        }

        if (filled($data['brand'])) {
            $query['brand'] = implode(',', $data['brand']);
        }

        if (filled($data['price_range'])) {
            $query['price'] = $data['price_range'];
        }

        $routeName = request()->is('e-shop.loc/product-grids') ? 'product-grids' : 'product-lists';
        $routeParameters = http_build_query($query);

        return redirect()->route($routeName, $routeParameters);
    }


    /**
     * Retrieve a list of products based on search criteria
     *
     * @return array
     */
    public function productLists(): array
    {
        // Retrieve all products
        $query = $this->model::query()->with(['categories', 'brand', 'condition', 'tags', 'sizes']);

        // If category is specified, filter by category
        if (!empty($_GET['category'])) {
            $catSlugs = explode(',', $_GET['category']);
            $catIds = Category::whereIn('slug', $catSlugs)->pluck('id')->toArray();
            $query->whereIn('cat_id', $catIds);
        }

        // If brand is specified, filter by brand
        if (!empty($_GET['brand'])) {
            $brandSlugs = explode(',', $_GET['brand']);
            $brandIds = Brand::whereIn('slug', $brandSlugs)->pluck('id')->toArray();
            $query->whereIn('brand_id', $brandIds);
        }

        // If sort by is specified, sort by title or price
        if (!empty($_GET['sortBy'])) {
            $sortBy = $_GET['sortBy'];

            if ($sortBy === 'title') {
                $query->orderBy('title', 'ASC');
            }

            if ($sortBy === 'price') {
                $query->orderBy('price', 'ASC');
            }
        }

        // If price range is specified, filter by price range
        if (!empty($_GET['price'])) {
            $priceRange = explode('-', $_GET['price']);
            $minPrice = $priceRange[0] ?? 0;
            $maxPrice = $priceRange[1] ?? PHP_INT_MAX;
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }

        // Retrieve recent products
        $recentProducts = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        // If show is specified, paginate by show amount, else paginate by default amount of 6
        $perPage = isset($_GET['show']) ? (int) $_GET['show'] : 6;
        $products = $query->whereStatus('active')->paginate($perPage);

        // Return recent products, filtered products, and brands with their associated products
        return [
            'recent_products' => $recentProducts,
            'products' => $products,
            'brands' => Brand::whereStatus('active')->withCount('products')->get(),
        ];
    }


    /**
     * Retrieve a list of blog posts
     *
     * @return array|string
     */
    public function blog(): array|string
    {

            return [
                "posts"       => Post::with(['categories', 'author_info'])->whereStatus('active')->orderBy('id', 'DESC')->paginate(9),
                "recantPosts" => Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get(),
            ];
    }

    /**
     * Create a new newsletter entry and send a verification email to the provided email address.
     *
     * @param  array  $data  The data for the new newsletter entry.
     * @return void Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function newsletter(array $data):void
    {
        Newsletter::create([
            'email' => $data['email'],
            'token' => $token = Str::random(64),
        ]);

        dispatch(function () use ($data, $token) {
            Mail::send('front::emails.news-letter', ['token' => $token], function ($message) use ($data) {
                $message->to($data['email']);
                $message->subject('Email Verification Mail');
            });
        });
    }
    /**
     * Validate a newsletter entry by ID.
     *
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function validation(int $id): string {
         return  Newsletter::whereId($id)->update(['is_validated' => 1]);
    }

    /**
     * Delete a newsletter entry by ID.
     *
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function deleteNewsletter(int $id): string {
          return Newsletter::whereId($id)->delete();
    }


}
