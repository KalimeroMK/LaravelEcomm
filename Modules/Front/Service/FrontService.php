<?php

namespace Modules\Front\Service;

use App\Events\MessageSent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Banner\Repository\BannerRepository;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Coupon\Models\Coupon;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;

class FrontService
{
    public $model = Product::class;

    protected ProductRepository $productRepository;
    protected BrandRepository $brandRepository;
    private PostRepository $postRepository;
    private BannerRepository $bannerRepository;

    public function __construct(
        ProductRepository $productRepository,
        BrandRepository $brandRepository,
        PostRepository $postRepository,
        BannerRepository $bannerRepository
    ) {
        $this->productRepository = $productRepository;
        $this->brandRepository = $brandRepository;
        $this->bannerRepository = $bannerRepository;
        $this->postRepository = $postRepository;
    }

    /**
     * Get data for the homepage.
     *
     * @return array
     */
    public function index(): array
    {
        $featured_products = $this->productRepository->getFeaturedProducts();
        $posts = $this->postRepository->getActivePosts();
        $banners = $this->bannerRepository->getActiveBanners();
        $latest_products = $this->productRepository->getLatestProducts();
        $hot_products = $latest_products->splice(4);
        return compact('featured_products', 'posts', 'banners', 'latest_products', 'hot_products');
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

        $products = $category->products()
            ->paginate(10);

        $recentProducts = Product::where('status', 'active')
            ->orderBy('id', 'desc')
            ->take(3)
            ->get();

        $brands = Brand::where('status', 'active')
            ->orderBy('title')
            ->get();

        return compact('brands', 'recentProducts', 'products');
    }


    /**
     * @param  Request  $request
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

            $data = [];
            $data['url'] = route('message.show', $message->id);
            $data['date'] = $message->created_at->format('F d, Y h:i A');
            $data['name'] = $message->name;
            $data['email'] = $message->email;
            $data['phone'] = $message->phone;
            $data['message'] = $message->message;
            $data['subject'] = $message->subject;
            $data['photo'] = Auth()->user()->photo ?? '';

            event(new MessageSent($data));

            return;
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }


    /**
     * Retrieves the product grids based on the query parameters.
     *
     * @return array|string The product grids.
     */
    public function productGrids(): array|string
    {
        $queryParams = $this->retrieveQueryParameters();

        list($categoryIds, $brandIds) = $this->retrieveIdsFromSlugs($queryParams);

        list($minPrice, $maxPrice) = $this->retrievePriceRange($queryParams);

        list($sortColumn, $sortOrder) = $this->retrieveSortOrder($queryParams);

        $products = $this->retrieveProducts($categoryIds, $brandIds, $minPrice, $maxPrice, $sortColumn, $sortOrder,
            $queryParams);

        return $this->retrieveBrandsAndRecentProducts($products);
    }

    private function retrieveQueryParameters(): array
    {
        return request()->only(['category', 'brand', 'price', 'show', 'sortBy']);
    }

    private function retrieveIdsFromSlugs(array $queryParams): array
    {
        $categoryIds = Category::whereIn('slug', explode(',', $queryParams['category'] ?? ''))->pluck('id')->toArray();
        $brandIds = Brand::whereIn('slug', explode(',', $queryParams['brand'] ?? ''))->pluck('id')->toArray();

        return [$categoryIds, $brandIds];
    }

    private function retrievePriceRange(array $queryParams): array
    {
        return array_map('intval', explode('-', $queryParams['price'] ?? '0-'.PHP_INT_MAX));
    }

    private function retrieveSortOrder(array $queryParams): array
    {
        $sortColumn = $queryParams['sortBy'] ?? 'created_at';
        $sortOrder = ($sortColumn === 'title') ? 'asc' : 'desc';
        if ($sortColumn === 'price') {
            $sortOrder = 'asc';
        }

        return [$sortColumn, $sortOrder];
    }

    private function retrieveProducts(
        $categoryIds,
        $brandIds,
        $minPrice,
        $maxPrice,
        $sortColumn,
        $sortOrder,
        array $queryParams
    ) {
        $perPage = (int)($queryParams['show'] ?? 9);
        return $this->model::query()
            ->when($categoryIds, fn($query) => $query->whereIn('cat_id', $categoryIds))
            ->when($brandIds, fn($query) => $query->whereIn('brand_id', $brandIds))
            ->when($minPrice || $maxPrice, fn($query) => $query->whereBetween('price', [$minPrice, $maxPrice]))
            ->orderBy($sortColumn, $sortOrder)
            ->with(['categories', 'brand', 'condition', 'tags', 'sizes'])
            ->paginate($perPage);
    }

    private function retrieveBrandsAndRecentProducts($products): array
    {
        $brands = Brand::where('status', 'active')->orderBy('title')->get();
        $recent_products = Product::where('status', 'active')->orderByDesc('id')->take(3)->get();

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
            $posts = Post::getBlogByTag($request->slug);
            $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            return [
                "posts" => $posts,
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
                'id' => $coupon->id,
                'code' => $coupon->code,
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
            $category = $request['category'] ?? [];
            $tag = $request['tag'] ?? [];

            $catURL = !empty($category) ? '&category='.implode(',', $category) : '';
            $tagURL = !empty($tag) ? '&tag='.implode(',', $tag) : '';

            // Redirect to blog page with filtered categories and tags.
            return redirect()->route('blog', $catURL.$tagURL);
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
            $posts = Post::with('author_info')->whereHas('categories', static function ($q) use ($request) {
                $q->whereSlug($request->slug);
            })->paginate(10);
            $recantPosts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Return posts and recent posts for specified category.
            return [
                "posts" => $posts,
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
    public function productSearch($data): array|string
    {
        // Get recent products.
        $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

        // Search products by name, description, and brand name.
        $products = Product::whereLike(Product::likeRows, Arr::get($data, 'search'))
            ->orderBy('id', 'DESC')
            ->paginate('9');

        return [
            "recent_products" => $recent_products,
            "products" => $products,
            "brands" => Brand::with('products')->get(),
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
            "products" => $products,
            "brands" => Brand::with('products')->get(),
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
            "post" => $post,
            "recantPosts" => $recentPosts,
        ];
    }

    /**
     * Get data for a product brand page.
     *
     * @param $data
     * @return array|string
     */
    public function productBrand($data): array|string
    {
        // Get products for brand
        $products = Product::whereHas('brand', function (Builder $query) use ($data) {
            $query->where('slug', $data['slug']);
        })->paginate(9);
        $brands = Brand::where('status', 'active')
            ->orderBy('title')
            ->get();
        // Get recent products
        $recentProducts = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

        // Return data
        return [
            "products" => $products,
            "brands" => $brands,
            'recentProducts' => $recentProducts,
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
        $query = array_filter([
            'show' => $data['show'] ?? null,
            'sortBy' => $data['sortBy'] ?? null,
            'category' => $data['category'] ? implode(',', $data['category']) : null,
            'brand' => $data['brand'] ? implode(',', $data['brand']) : null,
            'price' => $data['price_range'] ?? null,
        ]);

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
        $query = $this->makeBaseQuery();
        $this->filterByCategory($query);
        $this->filterByBrand($query);
        $this->sortBy($query);
        $this->filterByPriceRange($query);
        $recentProducts = $this->recentProducts();
        $products = $this->pagination($query);

        return [
            'recent_products' => $recentProducts,
            'products' => $products,
            'brands' => $this->brandsWithProducts(),
        ];
    }

    private function makeBaseQuery(): Builder
    {
        return $this->model::query()
            ->with(['categories', 'brand', 'condition', 'tags', 'sizes'])
            ->where('status', 'active');
    }

    private function filterByCategory(Builder $query): void
    {
        if (!empty($_GET['category'])) {
            $catSlugs = explode(',', $_GET['category']);
            $catIds = Category::whereIn('slug', $catSlugs)->pluck('id')->toArray();
            $query->whereIn('cat_id', $catIds);
        }
    }

    private function filterByBrand(Builder $query): void
    {
        if (!empty($_GET['brand'])) {
            $brandSlugs = explode(',', $_GET['brand']);
            $brandIds = Brand::whereIn('slug', $brandSlugs)->pluck('id')->toArray();
            $query->whereIn('brand_id', $brandIds);
        }
    }

    private function sortBy(Builder $query): void
    {
        if (!empty($_GET['sortBy'])) {
            $sortBy = $_GET['sortBy'];
            if ($sortBy === 'title') {
                $query->orderBy('title', 'ASC');
            } elseif ($sortBy === 'price') {
                $query->orderBy('price', 'ASC');
            }
        }
    }

    private function filterByPriceRange(Builder $query): void
    {
        if (!empty($_GET['price'])) {
            $priceRange = explode('-', $_GET['price']);
            $minPrice = $priceRange[0] ?? 0;
            $maxPrice = $priceRange[1] ?? PHP_INT_MAX;
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }
    }

    private function recentProducts()
    {
        return Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
    }

    private function pagination(Builder $query)
    {
        $perPage = isset($_GET['show']) ? (int)$_GET['show'] : 6;
        return $query->paginate($perPage);
    }

    private function brandsWithProducts()
    {
        return Brand::whereStatus('active')->withCount('products')->get();
    }


    /**
     * Retrieve a list of blog posts
     *
     * @return array|string
     */
    public function blog(): array|string
    {
        return [
            "posts" => Post::with(['categories', 'author_info'])->whereStatus('active')->orderBy('id',
                'DESC')->paginate(9),
            "recantPosts" => Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get(),
        ];
    }

    /**
     * Create a new newsletter entry and send a verification email to the provided email address.
     *
     * @param  array  $data  The data for the new newsletter entry.
     * @return void Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function newsletter(array $data): void
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
    public function validation(int $id): string
    {
        return Newsletter::whereId($id)->update(['is_validated' => 1]);
    }

    /**
     * Delete a newsletter entry by ID.
     *
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function deleteNewsletter(int $id): string
    {
        return Newsletter::whereId($id)->delete();
    }


}
