<?php

use App\Providers\AppServiceProvider;
use App\Providers\PolicyServiceProvider;
use Barryvdh\TranslationManager\ManagerServiceProvider;
use Kalimeromk\Filterable\FilterableServiceProvider;
use Lab404\Impersonate\ImpersonateServiceProvider;
use Laravel\Socialite\SocialiteServiceProvider;
use Modules\Admin\Providers\AdminServiceProvider;
use Modules\Attribute\Providers\AttributeServiceProvider;
use Modules\Banner\Providers\BannerServiceProvider;
use Modules\Billing\Providers\BillingServiceProvider;
use Modules\Brand\Providers\BrandServiceProvider;
use Modules\Bundle\Providers\BundleServiceProvider;
use Modules\Cart\Providers\CartServiceProvider;
use Modules\Category\Providers\CategoryServiceProvider;
use Modules\Complaint\Providers\ComplaintServiceProvider;
use Modules\Core\Providers\CoreServiceProvider;
use Modules\Coupon\Providers\CouponServiceProvider;
use Modules\Front\Providers\FrontServiceProvider;
use Modules\Google2fa\Providers\Google2faServiceProvider;
use Modules\Message\Providers\MessageServiceProvider;
use Modules\Newsletter\Providers\NewsletterServiceProvider;
use Modules\Notification\Providers\NotificationServiceProvider;
use Modules\OpenAI\Providers\OpenAIServiceProvider;
use Modules\Order\Providers\OrderServiceProvider;
use Modules\Page\Providers\PageServiceProvider;
use Modules\Permission\Providers\PermissionServiceProvider;
use Modules\Post\Providers\PostServiceProvider;
use Modules\Product\Providers\ProductServiceProvider;
use Modules\Role\Providers\RoleServiceProvider;
use Modules\Settings\Providers\SettingsServiceProvider;
use Modules\Shipping\Providers\ShippingServiceProvider;
use Modules\Size\Providers\SizeServiceProvider;
use Modules\Tag\Providers\TagServiceProvider;
use Modules\Tenant\Providers\TenantServiceProvider;
use Modules\User\Providers\UserServiceProvider;
use Spatie\Feed\FeedServiceProvider;

return [
    AppServiceProvider::class,
    PolicyServiceProvider::class,
    /*
         * Package Service Providers...
         */
    AdminServiceProvider::class,
    AttributeServiceProvider::class,
    BannerServiceProvider::class,
    BillingServiceProvider::class,
    BundleServiceProvider::class,
    BrandServiceProvider::class,
    CartServiceProvider::class,
    CategoryServiceProvider::class,
    ComplaintServiceProvider::class,
    CoreServiceProvider::class,
    CouponServiceProvider::class,
    FeedServiceProvider::class,
    FilterableServiceProvider::class,
    FrontServiceProvider::class,
    Google2faServiceProvider::class,
    ImpersonateServiceProvider::class,
    ManagerServiceProvider::class,
    MessageServiceProvider::class,
    NewsletterServiceProvider::class,
    NotificationServiceProvider::class,
    OpenAIServiceProvider::class,
    OrderServiceProvider::class,
    PageServiceProvider::class,
    PostServiceProvider::class,
    PermissionServiceProvider::class,
    ProductServiceProvider::class,
    RoleServiceProvider::class,
    ShippingServiceProvider::class,
    SizeServiceProvider::class,
    SocialiteServiceProvider::class,
    SettingsServiceProvider::class,
    TagServiceProvider::class,
    TenantServiceProvider::class,
    UserServiceProvider::class,

];
