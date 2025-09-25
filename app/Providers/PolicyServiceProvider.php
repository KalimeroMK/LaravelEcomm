<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeGroup;
use Modules\Attribute\Models\Policies\AttributeGroupPolicy;
use Modules\Attribute\Models\Policies\AttributePolicy;
use Modules\Banner\Models\Banner;
use Modules\Banner\Models\Policies\BannerPolicy;
use Modules\Billing\Models\PaymentProvider;
use Modules\Billing\Models\Policies\PaymentProviderPolicy;
use Modules\Brand\Models\Brand;
use Modules\Brand\Models\Policies\BrandPolicy;
use Modules\Bundle\Models\Bundle;
use Modules\Bundle\Models\Policy\BundlePolicy;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\Policies\CartPolicy;
use Modules\Category\Models\Category;
use Modules\Category\Models\Policies\CategoryPolicy;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\Polices\ComplaintPolicy;
use Modules\Coupon\Models\Coupon;
use Modules\Coupon\Models\Policies\CouponPolicy;
use Modules\Message\Models\Message;
use Modules\Message\Models\Policies\MessagePolicy;
use Modules\Newsletter\Models\Newsletter;
use Modules\Newsletter\Models\Policies\NewsletterPolicy;
use Modules\Order\Models\Order;
use Modules\Order\Models\Policies\OrderPolicy;
use Modules\Page\Models\Page;
use Modules\Page\Models\Policy\PagePolicy;
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
use Modules\Tag\Models\Policies\TagPolicy;
use Modules\Tag\Models\Tag;
use Modules\Tenant\Models\Policy\TenantPolicy;
use Modules\Message\Models\Policies\MessagePolicy;
use Modules\Tenant\Models\Tenant;
use Modules\User\Models\Policies\UserPolicy;
use Modules\User\Models\User;
use Spatie\Permission\Models\Permission;

class PolicyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Gate::policy(Attribute::class, AttributePolicy::class);
        Gate::policy(Banner::class, BannerPolicy::class);
        Gate::policy(PaymentProvider::class, PaymentProviderPolicy::class);
        Gate::policy(Brand::class, BrandPolicy::class);
        Gate::policy(Bundle::class, BundlePolicy::class);
        Gate::policy(Cart::class, CartPolicy::class);
        Gate::policy(Category::class, CategoryPolicy::class);
        Gate::policy(Complaint::class, ComplaintPolicy::class);
        Gate::policy(Coupon::class, CouponPolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Newsletter::class, NewsletterPolicy::class);
        Gate::policy(Order::class, OrderPolicy::class);
        Gate::policy(Page::class, PagePolicy::class);
        Gate::policy(Permission::class, PermissionPolicy::class);
        Gate::policy(Post::class, PostPolicy::class);
        Gate::policy(PostComment::class, PostCommentPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Role::class, RolePolicy::class);
        Gate::policy(Setting::class, SettingsPolicy::class);
        Gate::policy(Tag::class, TagPolicy::class);
        Gate::policy(User::class, UserPolicy::class);
        Gate::policy(Tenant::class, TenantPolicy::class);
        Gate::policy(AttributeGroup::class, AttributeGroupPolicy::class);

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super-admin') ? true : null;
        });
    }
}
