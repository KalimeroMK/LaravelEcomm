<?php

namespace Modules\Core\Helpers;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
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
    private static function getUserId(int|string $user_id = ''): string|int
    {
        if (Auth::check()) {
            return $user_id ?: Auth::id();
        }

        return 0;
    }

    public static function messageList(): Collection
    {
        return Message::whereNull('read_at')->orderBy('created_at', 'desc')->get();
    }

    public static function cartCount(string $user_id = ''): int
    {
        $user_id = self::getUserId($user_id);

        return $user_id !== 0 ? Cart::whereUserId($user_id)->whereOrderId(null)->sum('quantity') : 0;
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
        return Shipping::orderBy('id', 'DESC')->get();
    }

    public static function postTagList(): Collection
    {
        return Tag::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(20)
            ->get();
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
        return Category::withCount('posts')
            ->orderBy('posts_count', 'desc')
            ->take(10)
            ->get();
    }
}
