<?php

namespace Modules\Front\Service;

use App\Events\MessageSent;
use Couchbase\QueryException;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
use Modules\Banner\Models\Banner;
use Modules\Brand\Models\Brand;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Core\Helpers\Payment;
use Modules\Coupon\Models\Coupon;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use Modules\Post\Models\Post;
use Modules\Product\Models\Product;

class FrontService
{

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
        try {
            $products = Product::query();
            $cat_ids = [];
            $brand_ids = [];

            if (!empty($_GET['category'])) {
                $cat_slugs = explode(',', $_GET['category']);
                $categories = Category::whereIn('slug', $cat_slugs)->whereStatus('active')->get();
                $cat_ids = $categories->pluck('id')->toArray();
            }

            if (!empty($_GET['brand'])) {
                $brand_slugs = explode(',', $_GET['brand']);
                $brands = Brand::whereIn('slug', $brand_slugs)->whereStatus('active')->get();
                $brand_ids = $brands->pluck('id')->toArray();
            }

            if (!empty($_GET['price'])) {
                $price_range = explode('-', $_GET['price']);
                $min_price = $price_range[0] ?? 0;
                $max_price = $price_range[1] ?? PHP_INT_MAX;
                $products->whereBetween('price', [$min_price, $max_price]);
            }

            if (!empty($_GET['sortBy'])) {
                $sort_by = $_GET['sortBy'];

                if ($sort_by === 'title') {
                    $products->orderBy('title', 'ASC');
                }

                if ($sort_by === 'price') {
                    $products->orderBy('price', 'ASC');
                }
            }

            $products->whereIn('cat_id', $cat_ids)
                ->whereIn('brand_id', $brand_ids)
                ->whereStatus('active');

            $per_page = isset($_GET['show']) ? (int)$_GET['show'] : 9;

            $products = $products->paginate($per_page);
            $brands = Brand::whereStatus('active')->orderBy('title', 'ASC')->get();
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->take(3)->get();

            return compact('brands', 'recent_products', 'products');
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
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
     * Get recent products, search products, and brands for search page.
     *
     * @param $request The search request.
     *
     * @return array|string The recent products, searched products, and brands.
     */
    public function productSearch($request): array|string
    {
        try {
            // Get recent products.
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Search products by name, description, and brand name.
            $products = Product::whereLike(Arr::get($request, 'search'), Product::likeRows)
                ->orderBy('id', 'DESC')
                ->paginate('9');

            return [
                "recent_products" => $recent_products,
                "products"        => $products,
                "brands"          => Brand::with('products')->get(),
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Get recent products, deal products, and brands for deal page.
     *
     * @return array|string The recent products, deal products, and brands.
     */
    public function productDeal(): array|string
    {
        try {
            // Get recent products.
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Get deal products.
            $products = Product::whereDDeal(true)
                ->orderBy('id', 'DESC')
                ->paginate('9');

            return [
                "recent_products" => $recent_products,
                "products"        => $products,
                "brands"          => Brand::with('products')->get(),
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Get product detail and related products by product slug.
     *
     * @param $slug The product slug.
     *
     * @return array|string The product detail and related products.
     */
    public function productDetail($slug): array|string
    {
        try {
            // Get the product detail by slug.
            $product_detail = Product::getProductBySlug($slug);

            // Get related products by category.
            $related = Product::whereHas('categories', static function ($q) use ($product_detail) {
                return $q->whereIn('title', $product_detail->categories->pluck('title'));
            })->where('id', '!=', $product_detail->id) // So you won't fetch same product
            ->take(8)->get();

            return [
                "product_detail" => $product_detail,
                "related"        => $related,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }


    /**
     * Get data for the homepage.
     *
     * @return array|string
     */
    public function index(): array|string
    {
        try {
            // Get featured products
            $featured_products = Product::with('categories')->orderBy('price', 'DESC')->limit(4)->get();

            // Get latest posts
            $posts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Get active banners
            $banners = Banner::whereStatus('active')->limit(3)->orderBy('id', 'DESC')->get();

            // Get lists of active products
            $product_lists = Product::with('categories', 'condition')->whereStatus('active')->orderBy('id', 'DESC')->limit(9)->get();

            // Get hot products
            $product_hot = Product::with('categories', 'condition')->whereStatus('active')->orderBy('id', 'DESC')->limit(9)->get();

            // Return all data
            return [
                "featured_products" => $featured_products,
                "posts"             => $posts,
                "banners"           => $banners,
                "product_lists"     => $product_lists,
                "product_hot"       => $product_hot,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
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
        try {
            // Get post data
            $post = Post::getPostBySlug($slug);

            // Get recent posts
            $recentPosts = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();

            // Return data
            return [
                "post"        => $post,
                "recantPosts" => $recentPosts,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
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
        try {
            // Get products for brand
            $products = Brand::getProductByBrand($request->slug);

            // Get recent products
            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            // Return data
            return [
                "products"        => $products,
                "recent_products" => $recent_products,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }


    /**
     * Processes the product filter and redirects to either product-grids or product-lists route with the filtered parameters.
     *
     * @param array $request The filter parameters passed through the HTTP request.
     * @return RedirectResponse|string A redirect response to either product-grids or product-lists route with the filtered parameters, or an error message.
     */
    public function productFilter(array $request): RedirectResponse|string
    {
        try {
            // Initialize empty strings for each parameter's URL
            $showURL = "";
            $sortByURL = "";
            $catURL = "";
            $brandURL = "";
            $priceRangeURL = "";

            // If 'show' parameter is set, add to $showURL
            if (isset($request['show']) && !empty($request['show'])) {
                $showURL .= '&show=' . $request['show'];
            }

            // If 'sortBy' parameter is set, add to $sortByURL
            if (isset($request['sortBy']) && !empty($request['sortBy'])) {
                $sortByURL .= '&sortBy=' . $request['sortBy'];
            }

            // If 'category' parameter is set, loop through the values and add to $catURL
            if (isset($request['category']) && !empty($request['category'])) {
                foreach ($request['category'] as $category) {
                    if (empty($catURL)) {
                        $catURL .= '&category=' . $category;
                    } else {
                        $catURL .= ',' . $category;
                    }
                }
            }

            // If 'brand' parameter is set, loop through the values and add to $brandURL
            if (isset($request['brand']) && !empty($request['brand'])) {
                foreach ($request['brand'] as $brand) {
                    if (empty($brandURL)) {
                        $brandURL .= '&brand=' . $brand;
                    } else {
                        $brandURL .= ',' . $brand;
                    }
                }
            }

            // If 'price_range' parameter is set, add to $priceRangeURL
            if (isset($request['price_range']) && !empty($request['price_range'])) {
                $priceRangeURL .= '&price=' . $request['price_range'];
            }

            // Redirect to either product-grids or product-lists route with the filtered parameters
            if (request()->is('e-shop.loc/product-grids')) {
                return redirect()->route('product-grids', $catURL . $brandURL . $priceRangeURL . $showURL . $sortByURL);
            } else {
                return redirect()->route('product-lists', $catURL . $brandURL . $priceRangeURL . $showURL . $sortByURL);
            }
        } catch (Exception $exception) {
            // Return an error message if an exception occurs
            return $exception->getMessage();
        }
    }

    /**
     * Retrieve a list of products based on search criteria
     *
     * @return array|string
     */
    public function productLists(): array|string
    {
        try {
            // Start with all products
            $products = Product::query();

            // If category is specified, filter by category
            if (!empty($_GET['category'])) {
                $cat_ids = Category::select('id')->whereIn('slug', explode(',', $_GET['category']))->pluck('id')->toArray();
                $products->whereIn('cat_id', $cat_ids)->paginate;
            }

            // If brand is specified, filter by brand
            if (!empty($_GET['brand'])) {
                return Brand::select('id')->whereIn('slug', explode(',', $_GET['brand']))->pluck('id')->toArray();
            }

            // If sort by is specified, sort by title or price
            if (!empty($_GET['sortBy'])) {
                if ($_GET['sortBy'] == 'title') {
                    $products = $products->whereStatus('active')->orderBy('title', 'ASC');
                }
                if ($_GET['sortBy'] == 'price') {
                    $products = $products->orderBy('price', 'ASC');
                }
            }

            // If price range is specified, filter by price range
            if (!empty($_GET['price'])) {
                $products->whereBetween('price', explode('-', $_GET['price']));
            }

            // Retrieve recent products
            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();

            // If show is specified, paginate by show amount, else paginate by default amount of 6
            if (!empty($_GET['show'])) {
                $products = $products->whereStatus('active')->paginate($_GET['show']);
            } else {
                $products = $products->whereStatus('active')->paginate(6);
            }

            // Return recent products, filtered products, and brands with their associated products
            return [
                "recent_products" => $recent_products,
                "products"        => $products,
                "brands"          => Brand::with('products')->get(),
            ];
        } catch (Exception $exception) {
            // If an exception occurs, return the error message
            return $exception->getMessage();
        }
    }

    /**
     * Retrieve a list of blog posts
     *
     * @return array|string
     */
    public function blog(): array|string
    {
        try {
            // Retrieve blog posts with their associated categories and author information, paginate by default amount of 9
            return [
                "posts"       => Post::with(['categories', 'author_info'])->whereStatus('active')->orderBy('id', 'DESC')->paginate(9),
                "recantPosts" => Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get(),
            ];
        } catch (Exception $exception) {
            // If an exception occurs, return the error message
            return $exception->getMessage();
        }
    }

    /**
     * Create a new newsletter entry and send a verification email to the provided email address.
     *
     * @param array $data The data for the new newsletter entry.
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function newsletter(array $data): string {
        try {
            Newsletter::create([
                'email' => $data['email'],
                'token' => $token = Str::random(64),
            ]);

            Mail::send('front::emails.news-letter', ['token' => $token], function ($message) use ($data) {
                $message->to($data['email']);
                $message->subject('Email Verification Mail');
            });
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Validate a newsletter entry by ID.
     *
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function validation(int $id): string {
        try {
           Newsletter::whereId($id)->update(['is_validated' => 1]);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }

    /**
     * Delete a newsletter entry by ID.
     *
     * @return string Returns an error message if an exception occurs, otherwise returns nothing.
     */
    public function deleteNewsletter(int $id): string {
        try {
            Newsletter::whereId($id)->delete();

        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }


}
