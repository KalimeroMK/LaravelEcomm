<?php

namespace Modules\Front\Service;

use App\Events\MessageSent;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use JetBrains\PhpStorm\NoReturn;
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
    
    /**
     * @param $slug
     *
     * @return array|string
     */
    public function productCat($slug): array|string
    {
        try {
            $category        = Category::whereSlug($slug)->firstOrFail();
            $products        = Product::whereHas('categories', static function ($q) use ($category) {
                $q->where('title', '=', $category->title);
            })->paginate(10);
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $brands          = Brand::orderBy('title', 'ASC')->where('status', 'active')->get();
            
            return [
                "brands"          => $brands,
                "recent_products" => $recent_products,
                "products"        => $products,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $request
     *
     * @return array|string
     */
    public function blogSearch($request): array|string
    {
        try {
            $recent_posts = Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
            $posts        = Post::whereLike(Post::likeRows, Arr::get($request, 'search'))
                                ->orderBy('id', 'DESC')
                                ->paginate(8);
            
            return [
                "posts"        => $posts,
                "recent_posts" => $recent_posts,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $request
     *
     * @return string|void
     */
    #[NoReturn] public function messageStore($request)
    {
        try {
            $message = Message::create($request->validated());
            // return $message;
            $data            = [];
            $data['url']     = route('message.show', $message->id);
            $data['date']    = $message->created_at->format('F d, Y h:i A');
            $data['name']    = $message->name;
            $data['email']   = $message->email;
            $data['phone']   = $message->phone;
            $data['message'] = $message->message;
            $data['subject'] = $message->subject;
            $data['photo']   = Auth()->user()->photo ?? '';
            // return $data;
            event(new MessageSent($data));
            exit();
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return array|string
     */
    public function productGrids(): array|string
    {
        try {
            $products = Product::query();
            
            if ( ! empty($_GET['category'])) {
                $cat_ids = Category::select('id')->whereIn('slug', explode(',', $_GET['category']))->pluck('id')->toArray();
                $products->whereIn('cat_id', $cat_ids);
            }
            if ( ! empty($_GET['brand'])) {
                return Brand::select('id')->whereIn('slug', explode(',', $_GET['brand']))->pluck('id')->toArray();
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
            
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            if ( ! empty($_GET['show'])) {
                $products = $products->whereStatus('active')->paginate($_GET['show']);
            } else {
                $products = $products->whereStatus('active')->paginate(9);
            }
            
            return [
                "brands"          => Brand::orderBy('title', 'ASC')->whereStatus('active')->get(),
                "recent_products" => $recent_products,
                "products"        => $products,
            ];
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
     * @param $request
     *
     * @return RedirectResponse|string
     */
    public function couponStore($request): RedirectResponse|string
    {
        try {
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
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param$request
     *
     * @return RedirectResponse|string
     */
    public function blogFilter($request): RedirectResponse|string
    {
        try {
            $catURL = "";
            if ( ! empty($request['category'])) {
                foreach ($request['category'] as $category) {
                    if (empty($catURL)) {
                        $catURL .= '&category='.$category;
                    } else {
                        $catURL .= ','.$category;
                    }
                }
            }
            
            $tagURL = "";
            if ( ! empty($request['tag'])) {
                foreach ($request['tag'] as $tag) {
                    if (empty($tagURL)) {
                        $tagURL .= '&tag='.$tag;
                    } else {
                        $tagURL .= ','.$tag;
                    }
                }
            }
            
            return redirect()->route('blog', $catURL.$tagURL);
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
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
            
            return [
                "posts"       => $posts,
                "recantPosts" => $recantPosts,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $request
     *
     * @return array |string
     */
    public function productSearch($request): array|string
    {
        try {
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $products        = Product::whereLike(Product::likeRows, Arr::get($request, 'search'))
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
     *
     * @return array |string
     */
    public function productDeal(): array|string
    {
        try {
            $recent_products = Product::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $products        = Product::whereDDeal(true)
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
     * @param $slug
     *
     * @return array|string
     */
    public function productDetail($slug): array|string
    {
        try {
            $product_detail = Product::getProductBySlug($slug);
            $related        = Product::whereHas('categories', static function ($q) use ($product_detail) {
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
     * @return array|string
     */
    public function index(): array|string
    {
        try {
            $featured_products = Product::with('categories')->orderBy('price', 'DESC')->limit(4)->get();
            $posts             = Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get();
            $banners           = Banner::whereStatus('active')->limit(3)->orderBy('id', 'DESC')->get();
            $product_lists     = Product::with('categories', 'condition')->whereStatus('active')->orderBy(
                'id',
                'DESC'
            )->limit(9)->get();
            $product_hot       = Product::with('categories', 'condition')->whereStatus('active')->orderBy(
                'id',
                'DESC'
            )->limit(9)->get();
            
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
     * @param $slug
     *
     * @return array|string
     */
    public function blogDetail($slug): array|string
    {
        try {
            return [
                "post"        => Post::getPostBySlug($slug),
                "recantPosts" => Post::whereStatus('active')->orderBy('id', 'DESC')->limit(3)->get(),
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $request
     *
     * @return array|string
     */
    public function productBrand($request): array|string
    {
        try {
            $products        = Brand::getProductByBrand($request->slug);
            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
            
            return [
                "products"        => $products,
                "recent_products" => $recent_products,
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $request
     *
     * @return RedirectResponse|string
     */
    public function productFilter($request): RedirectResponse|string
    {
        try {
            $showURL = "";
            if ( ! empty($request['show'])) {
                $showURL .= '&show='.$request['show'];
            }
            
            $sortByURL = '';
            if ( ! empty($request['sortBy'])) {
                $sortByURL .= '&sortBy='.$request['sortBy'];
            }
            
            $catURL = "";
            if ( ! empty($request['category'])) {
                foreach ($request['category'] as $category) {
                    if (empty($catURL)) {
                        $catURL .= '&category='.$category;
                    } else {
                        $catURL .= ','.$category;
                    }
                }
            }
            
            $brandURL = "";
            if ( ! empty($request['brand'])) {
                foreach ($request['brand'] as $brand) {
                    if (empty($brandURL)) {
                        $brandURL .= '&brand='.$brand;
                    } else {
                        $brandURL .= ','.$brand;
                    }
                }
            }
            
            $priceRangeURL = "";
            if ( ! empty($request['price_range'])) {
                $priceRangeURL .= '&price='.$request['price_range'];
            }
            if (request()->is('e-shop.loc/product-grids')) {
                return redirect()->route('product-grids', $catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            } else {
                return redirect()->route('product-lists', $catURL.$brandURL.$priceRangeURL.$showURL.$sortByURL);
            }
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return array|string
     */
    public function productLists(): array|string
    {
        try {
            $products = Product::query();
            
            if ( ! empty($_GET['category'])) {
                $cat_ids = Category::select('id')->whereIn('slug', explode(',', $_GET['category']))->pluck('id')->toArray();
                $products->whereIn('cat_id', $cat_ids)->paginate;
            }
            if ( ! empty($_GET['brand'])) {
                return Brand::select('id')->whereIn('slug', explode(',', $_GET['brand']))->pluck('id')->toArray();
            }
            if ( ! empty($_GET['sortBy'])) {
                if ($_GET['sortBy'] == 'title') {
                    $products = $products->whereStatus('active')->orderBy('title', 'ASC');
                }
                if ($_GET['sortBy'] == 'price') {
                    $products = $products->orderBy('price', 'ASC');
                }
            }
            
            if ( ! empty($_GET['price'])) {
                $products->whereBetween('price', explode('-', $_GET['price']));
            }
            
            $recent_products = Product::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get();
            if ( ! empty($_GET['show'])) {
                $products = $products->whereStatus('active')->paginate($_GET['show']);
            } else {
                $products = $products->whereStatus('active')->paginate(6);
            }
            
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
     * @return array|string
     */
    public function blog(): array|string
    {
        try {
            return [
                "posts"       => Post::with(['categories', 'author_info'])->whereStatus('active')->orderBy('id', 'DESC')->paginate(9),
                "recantPosts" => Post::where('status', 'active')->orderBy('id', 'DESC')->limit(3)->get(),
            ];
        } catch (Exception $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @param $data
     *
     * @return void
     */
    public function newsletter($data): void
    {
        Newsletter::create([
            'email' => $data['email'],
            'token' => $token = Str::random(64),
        ]);
        
        Mail::send('front::emails.news-letter', ['token' => $token], function ($message) use ($data) {
            $message->to($data['email']);
            $message->subject('Email Verification Mail');
        });
    }
    
    /**
     * @param $id
     *
     * @return void
     */
    public function validation($id): void
    {
        if ( ! is_null($id)) {
            Newsletter::whereId($id)->update(['is_validated' => 1]);
        }
    }
    
    /**
     * @param $id
     *
     * @return void
     */
    public function deleteNewsletter($id): void
    {
        if ( ! is_null($id)) {
            Newsletter::whereId($id)->delete();
        }
    }
    
}