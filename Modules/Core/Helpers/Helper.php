<?php

declare(strict_types=1);

namespace Modules\Core\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Modules\Billing\Models\Wishlist;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Message\Models\Message;
use Modules\Shipping\Models\Shipping;
use Modules\Tag\Models\Tag;
use ReflectionClass;
use ReflectionException;

class Helper
{
    public static function messageList()
    {
        return Cache::remember('unread_messages', 30, function () {
            return Message::where('read_at', null)->orderBy('created_at', 'desc')->get();
        });
    }

    public static function cartCount(string $user_id = ''): int
    {
        $user_id = self::getUserId($user_id);

        return $user_id !== 0 ? (int) Cart::whereUserId($user_id)->whereOrderId(null)->sum('quantity') : 0;
    }

    public static function getAllProductFromWishlist(string $user_id = ''): Collection
    {
        $user_id = self::getUserId($user_id);

        return $user_id !== 0
            ? Wishlist::with('product')->where('user_id', $user_id)->where('cart_id', null)->get()
            : collect(); // Return an empty collection instead of 0
    }

    public static function getAllProductFromCart(string $user_id = ''): Collection
    {
        $user_id = self::getUserId($user_id);

        return $user_id !== 0
            ? Cart::with('product')->where('user_id', $user_id)->where('order_id', null)->get()
            : collect(); // Return an empty collection instead of 0
    }

    // Total amount cart
    public static function totalCartPrice(string $user_id = ''): float|int
    {
        $user_id = self::getUserId($user_id);

        return $user_id !== 0 ? Cart::whereUserId($user_id)->where('order_id', null)->sum('amount') : 0;
    }

    public static function wishlistCount(string $user_id = ''): int
    {
        $user_id = self::getUserId($user_id);

        return $user_id !== 0 ? Wishlist::whereUserId($user_id)->where('cart_id', null)->sum('quantity') : 0;
    }

    public static function totalWishlistPrice(string $user_id = ''): float|int
    {
        $user_id = self::getUserId($user_id);

        return $user_id !== 0 ? Wishlist::whereUserId($user_id)->where('cart_id', null)->sum('amount') : 0;
    }

    public static function shipping(): Collection
    {
        return Cache::remember('shipping_list', 3600, function () {
            return Shipping::orderBy('id', 'DESC')->get();
        });
    }

    public static function postTagList(): Collection
    {
        return Cache::remember('post_tag_list', 3600, function () {
            return Tag::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->take(20)
                ->get();
        });
    }

    /**
     * @param  object|string  $class  The class name or object instance.
     * @return string The class short name.
     *
     * @throws ReflectionException
     */
    public static function getResourceName(object|string $class): string
    {
        $reflectionClass = new ReflectionClass($class);

        return $reflectionClass->getShortName();
    }

    public static function postCategoryList(): Collection
    {
        return Cache::remember('post_category_list', 3600, function () {
            return Category::withCount('posts')
                ->orderBy('posts_count', 'desc')
                ->take(10)
                ->get();
        });
    }

    /**
     * Check if cart contains only virtual/downloadable products (no shipping required)
     */
    public static function cartRequiresShipping(string $user_id = ''): bool
    {
        $cartItems = self::getAllProductFromCart($user_id);
        
        if ($cartItems->isEmpty()) {
            return true; // Default to requiring shipping if cart is empty
        }

        foreach ($cartItems as $item) {
            if ($item->product && $item->product->requiresShipping()) {
                return true;
            }
        }

        return false; // All products are virtual/downloadable
    }

    /**
     * Check if cart contains downloadable products
     */
    public static function cartHasDownloadable(string $user_id = ''): bool
    {
        $cartItems = self::getAllProductFromCart($user_id);
        
        foreach ($cartItems as $item) {
            if ($item->product && $item->product->isDownloadable()) {
                return true;
            }
        }

        return false;
    }

    private static function getUserId(int|string $user_id = ''): string|int
    {
        if (Auth::check()) {
            return $user_id ?: Auth::id();
        }

        return 0;
    }
}
