<?php

namespace Modules\Core\Helpers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use LaravelIdea\Helper\Modules\Core\Models\_IH_Core_C;
use LaravelIdea\Helper\Modules\Tag\Models\_IH_Tag_C;
use Modules\Billing\Models\Wishlist;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Message\Models\Message;
use Modules\Order\Models\Order;
use Modules\Shipping\Models\Shipping;
use Modules\Tag\Models\Tag;
use ReflectionClass;
use ReflectionException;

class Helper
{
    /**
     * @return Collection
     */
    public static function messageList(): Collection
    {
        return Message::whereNull('read_at')->orderBy('created_at', 'desc')->get();
    }
    /**
     * @param  string  $user_id
     *
     * @return int|mixed
     */
    // Cart Count
    public static function cartCount(string $user_id = ''): mixed
    {
        if (Auth::check()) {
            if ($user_id == "") {
                $user_id = auth()->user()->id;
            }
            
            return Cart::whereUserId($user_id)->whereOrderId(null)->sum('quantity');
        } else {
            return 0;
        }
    }
    
    /**
     * @param  string  $user_id
     *
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|int
     */
    public static function getAllProductFromWishlist(string $user_id = ''
    ): \Illuminate\Database\Eloquent\Collection|int|array {
        if (Auth::check()) {
            if ($user_id == "") {
                $user_id = auth()->user()->id;
            }
            
            return Wishlist::with('product')->where('user_id', $user_id)->where('cart_id', null)->get();
        } else {
            return 0;
        }
    }
    
    /**
     * @param  string  $user_id
     *
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|int
     */
    public static function getAllProductFromCart(string $user_id = ''
    ): \Illuminate\Database\Eloquent\Collection|int|array {
        if (Auth::check()) {
            if ($user_id == "") {
                $user_id = auth()->user()->id;
            }
            
            return Cart::with('product')->where('user_id', $user_id)->where('order_id', null)->get();
        } else {
            return 0;
        }
    }
    /**
     * @param  string  $user_id
     *
     * @return int|mixed
     */
    // Total amount cart
    public static function totalCartPrice(string $user_id = ''): mixed
    {
        if (Auth::check()) {
            if ($user_id == "") {
                $user_id = auth()->user()->id;
            }
            
            return Cart::whereUserId($user_id)->where('order_id', null)->sum('amount');
        } else {
            return 0;
        }
    }
    /**
     * @param  string  $user_id
     *
     * @return int|mixed
     */
    // Wishlist Count
    public static function wishlistCount(string $user_id = ''): mixed
    {
        if (Auth::check()) {
            if ($user_id == "") {
                $user_id = auth()->user()->id;
            }
            
            return Wishlist::whereUserId($user_id)->where('cart_id', null)->sum('quantity');
        } else {
            return 0;
        }
    }
    
    /**
     * @param  string  $user_id
     *
     * @return int|mixed
     */
    public static function totalWishlistPrice(string $user_id = ''): mixed
    {
        if (Auth::check()) {
            if ($user_id == "") {
                $user_id = auth()->user()->id;
            }
            
            return Wishlist::whereUserId($user_id)->where('cart_id', null)->sum('amount');
        } else {
            return 0;
        }
    }
    /**
     * @param $id
     * @param $user_id
     *
     * @return int|string
     */
    // Total price with shipping and coupon
    public static function grandPrice($id, $user_id): int|string
    {
        $order = Order::find($id);
        if ($order) {
            $shipping_price = (float)$order->shipping->price;
            $order_price    = self::orderPrice($id, $user_id);
            
            return number_format((float)($order_price + $shipping_price), 2, '.', '');
        } else {
            return 0;
        }
    }
    
    /**
     * @return Collection
     */
    public static function shipping(): Collection
    {
        return Shipping::orderBy('id', 'DESC')->get();
    }
    
    /**
     * @param  string  $option
     *
     * @return array
     */
    public static function productCategoryList(string $option = 'all'): array
    {
        if ($option = 'all') {
            return Category::orderBy('id', 'DESC')->get();
        }
        
        return Category::has('products')->orderBy('id', 'DESC')->get();
    }
    
    /**
     *
     * @return array
     */
    public static function postCategoryList(string $option = "all")
    {
        if ($option = 'all') {
            return Category::orderBy('id', 'DESC')->get();
        }
        
        return Category::has('posts')->orderBy('id', 'DESC')->get();
    }
    
    /**
     * @param $class
     *
     * @return string
     */
    public static function getResourceName($class): string
    {
        try {
            $reflectionClass = new ReflectionClass($class);
            
            return $reflectionClass->getShortName();
        } catch (ReflectionException $exception) {
            return $exception->getMessage();
        }
    }
    
    /**
     * @return \Illuminate\Database\Eloquent\Collection|_IH_Tag_C|array|_IH_Core_C
     */
    public static function postTagList(): \Illuminate\Database\Eloquent\Collection|_IH_Tag_C|array|_IH_Core_C
    {
        if ($option = 'all') {
            return Tag::orderBy('id', 'desc')->get();
        }
        
        return Tag::has('posts')->orderBy('id', 'desc')->get();
    }
    
}
