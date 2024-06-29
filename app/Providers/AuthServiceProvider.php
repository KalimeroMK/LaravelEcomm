<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\Policies\AttributePolicy;
use Modules\Banner\Models\Banner;
use Modules\Banner\Models\Policies\BannerPolicy;
use Modules\Brand\Models\Brand;
use Modules\Brand\Models\Policies\BrandPolicy;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Models\Policy\BundlePolicy;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\Policies\CartPolicy;
use Modules\Category\Models\Category;
use Modules\Category\Models\Policies\CategoryPolicy;
use Modules\Coupon\Models\Policies\CouponPolicy;
use Modules\Message\Models\Message;
use Modules\Message\Models\Policies\MessagePolicy;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Models\Policies\NewsletterPolicy;
use Modules\Order\Models\Order;
use Modules\Order\Models\Policies\OrderPolicy;
use Modules\Permission\Models\Polices\PermissionPolicy;
use Modules\Post\Models\Policies\PostCommentPolicy;
use Modules\Post\Models\Policies\PostPolicy;
use Modules\Post\Models\Post;
use Modules\Post\Models\PostComment;
use Modules\Product\Models\Policies\ProductPolicy;
use Modules\Product\Models\Product;
use Modules\Role\Models\Polices\RolePolicy;
use Modules\Role\Models\Role;
use Modules\Settings\Models\Polices\SettingsPolicy;
use Modules\Settings\Models\Setting;
use Modules\Size\Models\Policies\SizePolicy;
use Modules\Size\Models\Size;
use Modules\Tag\Models\Policies\TagPolicy;
use Modules\Tag\Models\Tag;
use Modules\Tenant\Models\Policy\TenantPolicy;
use Modules\Tenant\Models\Tenant;
use Modules\User\Models\Policies\UserPolicy;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;
use Stripe\Coupon;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Attribute::class => AttributePolicy::class,
        Bundle::class => BundlePolicy::class,
        Banner::class => BannerPolicy::class,
        Brand::class => BrandPolicy::class,
        Cart::class => CartPolicy::class,
        Category::class => CategoryPolicy::class,
        Coupon::class => CouponPolicy::class,
        Message::class => MessagePolicy::class,
        Newsletter::class => NewsletterPolicy::class,
        Order::class => OrderPolicy::class,
        Permission::class => PermissionPolicy::class,
        Post::class => PostPolicy::class,
        PostComment::class => PostCommentPolicy::class,
        Product::class => ProductPolicy::class,
        Role::class => RolePolicy::class,
        Setting::class => SettingsPolicy::class,
        Size::class => SizePolicy::class,
        Tag::class => TagPolicy::class,
        User::class => UserPolicy::class,


        Tenant::class => TenantPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();
//        Gate::before(function ($user, $ability) {
//            return $user->hasRole('super-admin') ? true : null;
//        });
    }
}
