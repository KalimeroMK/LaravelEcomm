<?php

namespace Modules\Front\Service;

use App\Events\MessageSent;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Modules\Banner\Repository\BannerRepository;
use Modules\Brand\Models\Brand;
use Modules\Brand\Repository\BrandRepository;
use Modules\Bundle\Models\Bundle;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Coupon\Models\Coupon;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use Modules\Post\Models\Post;
use Modules\Post\Repository\PostRepository;
use Modules\Product\Models\Product;
use Modules\Product\Repository\ProductRepository;
use Modules\Tag\Models\Tag;

class FrontService
{
    /**
     * @var string
     */
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
     * @return array<string, mixed>
     */
    public function index(): array
    {
        return [
            'featured_products' => $this->productRepository->getFeaturedProducts(),
            'posts' => $this->postRepository->getActivePosts(),
            'banners' => $this->bannerRepository->getActiveBanners(),
            'latest_products' => $this->productRepository->getLatestProducts(),
            'hot_products' => $this->productRepository->getLatestProducts()->splice(4),
        ];
    }

    /**
     * Retrieves product categories based on a slug.
     *
     * @param  string  $slug  The slug of the category.
     * @return array<string, mixed>|string The products under the category.
     */
    public function productCat(string $slug): array|string
    {
        $cacheKey = 'productCat_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug) {
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

            return [
                'category' => $category,
                'products' => $products,
                'recentProducts' => $recentProducts,
                'brands' => $brands,
            ];
        });
    }

    /**
     * Search blog posts.
     *
     * @param  Request  $request
     * @return array<string, mixed>|string
     */
    public function blogSearch(Request $request): array|string
    {
        $cacheKey = 'blogSearch_'.$request->input('search');

        return Cache::remember($cacheKey, 24 * 60, function () use ($request) {
            $recentPosts = Post::where('status', 'active')->latest()->limit(3)->get();
            $posts = Post::whereLike(Post::likeRows, $request->input('search'))
                ->latest()
                ->paginate(8);

            return [
                'posts' => $posts,
                'recent_posts' => $recentPosts,
            ];
        });
    }

    /**
     * Store a message.
     *
     * @param  Request  $request
     * @return string
     */
    public function messageStore(Request $request): string
    {
        try {
            $message = Message::create($request->all());

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

            return 'message sent';
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Retrieves the product grids based on the query parameters.
     *
     * @return array<string, mixed>|string The product grids.
     */
    public function productGrids(): array|string
    {
        $queryParams = $this->retrieveQueryParameters();

        [$categoryIds, $brandIds] = $this->retrieveIdsFromSlugs($queryParams);

        [$minPrice, $maxPrice] = $this->retrievePriceRange($queryParams);

        ['sortColumn' => $sortColumn, 'sortOrder' => $sortOrder] = $this->retrieveSortOrder($queryParams);

        $products = $this->retrieveProducts(
            $categoryIds,
            $brandIds,
            $minPrice,
            $maxPrice,
            $sortColumn,
            $sortOrder,
            $queryParams
        );

        return $this->retrieveBrandsAndRecentProducts($products);
    }

    /**
     * Retrieve query parameters.
     *
     * @return array<string, mixed>
     */
    private function retrieveQueryParameters(): array
    {
        return request()->only(['category', 'brand', 'price', 'show', 'sortBy']);
    }


    /**
     * Retrieve category and brand IDs from slugs.
     *
     * @param  array<string, mixed>  $queryParams
     * @return array{0: int[], 1: int[]}
     */
    private function retrieveIdsFromSlugs(array $queryParams): array
    {
        $categorySlugs = explode(',', $queryParams['category'] ?? '');
        $brandSlugs = explode(',', $queryParams['brand'] ?? '');

        $categoryCacheKey = 'category_ids_'.md5(json_encode($categorySlugs));
        $brandCacheKey = 'brand_ids_'.md5(json_encode($brandSlugs));

        $categoryIds = Cache::remember($categoryCacheKey, 86400, function () use ($categorySlugs) {
            return Category::whereIn('slug', $categorySlugs)->pluck('id')->toArray();
        });

        $brandIds = Cache::remember($brandCacheKey, 86400, function () use ($brandSlugs) {
            return Brand::whereIn('slug', $brandSlugs)->pluck('id')->toArray();
        });

        return [$categoryIds, $brandIds];
    }


    /**
     * Retrieve price range.
     *
     * @param  array<string, mixed>  $queryParams
     * @return array<int, int>
     */
    private function retrievePriceRange(array $queryParams): array
    {
        return array_map('intval', explode('-', $queryParams['price'] ?? '0-'.PHP_INT_MAX));
    }

    /**
     * Retrieve sort order.
     *
     * @param  array<string, mixed>  $queryParams
     * @return array<string, string>
     */
    private function retrieveSortOrder(array $queryParams): array
    {
        $sortColumn = $queryParams['sortBy'] ?? 'created_at';
        $sortOrder = ($sortColumn === 'title') ? 'asc' : 'desc';
        if ($sortColumn === 'price') {
            $sortOrder = 'asc';
        }

        return [
            'sortColumn' => $sortColumn,
            'sortOrder' => $sortOrder,
        ];
    }

    /**
     * Retrieve products based on various filters.
     *
     * @param  int[]                 $categoryIds
     * @param  int[]                 $brandIds
     * @param  int                   $minPrice
     * @param  int                   $maxPrice
     * @param  string                $sortColumn
     * @param  string                $sortOrder
     * @param  array<string, mixed>  $queryParams
     * @return LengthAwarePaginator
     */
    private function retrieveProducts(
        array $categoryIds,
        array $brandIds,
        int $minPrice,
        int $maxPrice,
        string $sortColumn,
        string $sortOrder,
        array $queryParams
    ): LengthAwarePaginator {
        $perPage = (int)($queryParams['show'] ?? 9);

// Generate a unique cache key
        $cacheKey = 'products_'.md5(
                json_encode(
                    compact(
                        'categoryIds',
                        'brandIds',
                        'minPrice',
                        'maxPrice',
                        'sortColumn',
                        'sortOrder',
                        'perPage'
                    )
                )
            );

// Cache for 24 hours (86400 seconds)
        return Cache::remember($cacheKey, 86400, function () use (
            $categoryIds,
            $brandIds,
            $minPrice,
            $maxPrice,
            $sortColumn,
            $sortOrder,
            $perPage
        ) {
            return $this->model::query()
                ->when($categoryIds, fn($query) => $query->whereIn('cat_id', $categoryIds))
                ->when($brandIds, fn($query) => $query->whereIn('brand_id', $brandIds))
                ->when($minPrice || $maxPrice, fn($query) => $query->whereBetween('price', [$minPrice, $maxPrice]))
                ->orderBy($sortColumn, $sortOrder)
                ->with(['categories', 'brand', 'condition', 'tags', 'sizes'])
                ->paginate($perPage);
        });
    }

    /**
     * Retrieve brands and recent products.
     *
     * @param  LengthAwarePaginator  $products
     * @return array<string, mixed>
     */
    private function retrieveBrandsAndRecentProducts($products): array
    {
        $brands = Brand::where('status', 'active')->orderBy('title')->get();
        $recent_products = Product::where('status', 'active')->orderByDesc('id')->take(3)->get();

        return compact('brands', 'recent_products', 'products');
    }

    /**
     * Get blog posts by tag slug.
     *
     * @param  string  $slug
     * @return array<string, mixed>|string
     */
    public function blogByTag(string $slug): array|string
    {
        $cacheKey = 'blogByTag_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug) {
            $posts = Post::with(['author', 'tags'])
                ->whereHas('tags', function ($q) use ($slug) {
                    $q->where('slug', $slug);
                })
                ->paginate(10);
            $recent_posts = Post::with('author')->where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            return [
                'posts' => $posts,
                'recent_posts' => $recent_posts,
            ];
        });
    }

    /**
     * Store coupon and apply it to user's cart.
     *
     * @param  Request  $request
     * @return RedirectResponse|string
     */
    public function couponStore(Request $request): RedirectResponse|string
    {
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
    }

    /**
     * Filter blog posts based on selected categories and tags.
     *
     * @param  array<string, array<int, string>>  $data
     * @return array<string, string>
     */
    public function blogFilter(array $data): array
    {
        $category = $data['category'] ?? [];
        $tag = $data['tag'] ?? [];

        $catURL = !empty($category) ? implode(',', $category) : '';
        $tagURL = !empty($tag) ? implode(',', $tag) : '';

        // Return an array with the filtered categories and tags.
        return [
            'category' => $catURL,
            'tag' => $tagURL,
        ];
    }

    /**
     * Get blog posts by category slug.
     *
     * @param  string  $slug
     * @return array<string, mixed>
     */
    public function blogByCategory(string $slug): array
    {
        $posts = Post::with('author')->whereHas('categories', static function ($q) use ($slug) {
            $q->whereSlug($slug);
        })->paginate(10);
        $recantPosts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

        return [
            'posts' => $posts,
            'recantPosts' => $recantPosts,
        ];
    }

    /**
     * Search for products.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|string
     */
    public function productSearch(array $data): array|string
    {
        $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

// Perform search using Elasticsearch
        $searchTerm = Arr::get($data, 'search', '');
// Use the search method from Scout to perform a full-text search
        $products = Product::search($searchTerm)
            ->where('status', 'active')
            ->orderBy('id', 'desc')
            ->paginate(9);

// you might need to adjust this to work with your Elasticsearch setup.
        $brands = Brand::search($searchTerm)->get();

        return [
            'recent_products' => $recent_products,
            'products' => $products,
            'brands' => $brands,
        ];
    }

    /**
     * Get recent products, deal products, and brands for deal page.
     *
     * @return array<string, mixed> The recent products, deal products, and brands.
     */
    public function productDeal(): array
    {
        return Cache::remember('productDeal', 24 * 60, function () {
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            $products = Product::with('categories')->where('d_deal', true)
                ->orderBy('id', 'DESC')
                ->paginate(9); // Corrected to pass an integer

            $brands = Brand::with('products')->get();

            return [
                'recent_products' => $recent_products,
                'products' => $products,
                'brands' => $brands,
            ];
        });
    }


    /**
     * Get product details by slug.
     *
     * @param  string  $slug
     * @return array<string, mixed>
     */
    public function productDetail(string $slug): array
    {
        $cacheKey = 'productDetail_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug) {
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
        });
    }

    /**
     * Get data for a blog post detail page.
     *
     * @param  string  $slug
     * @return array<string, mixed>|string
     */
    public function blogDetail(string $slug): array|string
    {
        $cacheKey = 'blogDetail_'.$slug;

        return Cache::remember($cacheKey, 24 * 60, function () use ($slug) {
            $post = Post::with('author', 'categories')->whereSlug($slug)->FirstOrFail();
            $recentPosts = Post::with('author')->whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $tags = Tag::whereHas('posts')->take(50)->get();

            return [
                'post' => $post,
                'recantPosts' => $recentPosts,
                'tags' => $tags,
            ];
        });
    }

    /**
     * Get data for a product brand page.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>|string
     */
    public function productBrand(array $data): array|string
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
            'products' => $products,
            'brands' => $brands,
            'recentProducts' => $recentProducts,
        ];
    }

    /**
     * Processes the product filter and redirects to either product-grids or product-lists route with the filtered parameters.
     *
     * @param  array<string, mixed>  $data
     * @return RedirectResponse|string A redirect response to either product-grids or product-lists route with the filtered parameters, or an error message.
     */
    public function productFilter(array $data): RedirectResponse|string
    {
        $query = array_filter([
            'show' => $data['show'] ?? null,
            'sortBy' => $data['sortBy'] ?? null,
            'category' => $data['category'] ? implode(',', $data['category']) : null,
            'brand' => $data['brand'] ? implode(',', $data['brand']) : null,
            'price' => $data['price_range'] ?? null,
        ]);
        $appUrl = config('app.url');

// Determine the correct route name based on the current request
        $routeSuffix = request()->is($appUrl.'/product-grids') ? 'product-grids' : 'product-lists';

// Prefix the route name with 'front.'
        $routeName = 'front.'.$routeSuffix;

// Build the route parameters string
        $routeParameters = http_build_query($query);

// Use the config app url as the base for the redirect

// Return a redirect to the constructed URL
        return redirect()->route($routeName, $routeParameters);
    }

    /**
     * Retrieve a list of products based on search criteria
     *
     * @return array<string, mixed>
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

    /**
     * Create a base query for products.
     *
     * @return Builder
     */
    private function makeBaseQuery(): Builder
    {
        return $this->model::query()
            ->with(['categories', 'brand', 'condition', 'tags', 'sizes'])
            ->where('status', 'active');
    }

    /**
     * Filter query by category.
     *
     * @param  Builder  $query
     * @return void
     */
    private function filterByCategory(Builder $query): void
    {
        if (!empty($_GET['category'])) {
            $catSlugs = explode(',', $_GET['category']);
            $catIds = Category::whereIn('slug', $catSlugs)->pluck('id')->toArray();
            $query->whereIn('cat_id', $catIds);
        }
    }

    /**
     * Filter query by brand.
     *
     * @param  Builder  $query
     * @return void
     */
    private function filterByBrand(Builder $query): void
    {
        if (!empty($_GET['brand'])) {
            $brandSlugs = explode(',', $_GET['brand']);
            $brandIds = Brand::whereIn('slug', $brandSlugs)->pluck('id')->toArray();
            $query->whereIn('brand_id', $brandIds);
        }
    }

    /**
     * Sort query by specific column.
     *
     * @param  Builder  $query
     * @return void
     */
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

    /**
     * Filter query by price range.
     *
     * @param  Builder  $query
     * @return void
     */
    private function filterByPriceRange(Builder $query): void
    {
        if (!empty($_GET['price'])) {
            $priceRange = explode('-', $_GET['price']);
            $minPrice = $priceRange[0] ?? 0;
            $maxPrice = $priceRange[1] ?? PHP_INT_MAX;
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        }
    }

    /**
     * Get brands with product counts.
     *
     * @return Collection<int, Product>
     */
    private function recentProducts(): Collection
    {
        return Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
    }

    /**
     * Paginate query results.
     *
     * @param  Builder  $query
     * @return LengthAwarePaginator
     */
    private function pagination(Builder $query): LengthAwarePaginator
    {
        $perPage = isset($_GET['show']) ? (int)$_GET['show'] : 6;

        return $query->paginate($perPage);
    }

    /**
     * Get brands with product counts.
     *
     * @return Collection<int, Brand>
     */
    private function brandsWithProducts(): Collection
    {
        return Brand::whereStatus('active')->withCount('products')->get();
    }


    /**
     * Retrieve a list of blog posts
     *
     * @return array<string, mixed>|string
     */
    public function blog(): array|string
    {
        return Cache::remember('blog', 24 * 60, function () {
            return [
                'posts' => Post::with(['author'])->whereStatus('active')->orderBy(
                    'id',
                    'DESC'
                )->paginate(9),
                'recantPosts' => Post::with(['author'])->whereStatus('active')->orderBy(
                    'id',
                    'DESC'
                )->limit(3)->get(),
            ];
        });
    }

    /**
     * Create a new newsletter entry and send a verification email to the provided email address.
     *
     * @param  array<string, mixed>  $data  The data for the new newsletter entry.
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function newsletter(array $data): string
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

        return 'Email successfully add to the database ';
    }

    /**
     * Validate a newsletter entry by ID.
     *
     * @param  string  $token
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function validation(string $token): string
    {
        Newsletter::where('token', $token)->update(['is_validated' => 1]);

        return 'Your email is successfully validated';
    }

    /**
     * Delete a newsletter entry by ID.
     *
     * @param  string  $token
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function deleteNewsletter(string $token): string
    {
        Newsletter::where('token', $token)->first()->delete();

        return 'Your email is successfully deleted';
    }

    /**
     * Retrieve a list of product bundles.
     *
     * @return array<string, mixed>
     */
    public function productBundles(): array
    {
        $query = Bundle::query();
        $recentProducts = $this->recentProducts();
        $products = $this->pagination($query);

        return [
            'recent_products' => $recentProducts,
            'products' => $products,
        ];
    }
}
