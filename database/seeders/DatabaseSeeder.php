<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Database\Seeders\ConditionSeeder;
use Modules\Admin\Database\Seeders\SizeSeeder;
use Modules\Attribute\Database\Seeders\AttributeDatabaseSeeder;
use Modules\Attribute\Database\Seeders\AttributeValueSeeder;
use Modules\Banner\Models\Banner;
use Modules\Brand\Models\Brand;
use Modules\Bundle\Models\BundleProduct;
use Modules\Cart\Models\Cart;
use Modules\Category\Database\Seeders\CategoryPostSeeder;
use Modules\Coupon\Database\Seeders\CouponSeeder;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use Modules\Notification\Models\Notification;
use Modules\Order\Models\Order;
use Modules\Page\Database\Seeders\PageSeeder;
use Modules\Post\Models\Post;
use Modules\Product\Database\Seeders\ProductReviewSeeder;
use Modules\Product\Models\Product;
use Modules\Settings\Models\Setting;
use Modules\Tag\Models\Tag;
use Modules\User\Database\Seeders\PermissionTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call(PermissionTableSeeder::class);
        $this->call(AttributeDatabaseSeeder::class);
        $this->call(AttributeValueSeeder::class);
        Banner::factory()->count(5)->create();
        Brand::factory()->count(5)->create();
        Cart::factory()->count(200)->create();
        $this->call(CouponSeeder::class);
        $this->call(ConditionSeeder::class);
        Tag::factory()->count(100)->create();
        Order::factory()->count(200)->create();
        $this->call(CategoryPostSeeder::class);
        Message::factory()->count(50)->create();
        $this->call(PageSeeder::class);
        Post::factory()->count(200)->withCategoriesAndTags()->create();
        Product::factory()->count(200)->withCategoriesAndTags()->create();
        BundleProduct::factory()->count(50)->create();
        $this->call(ProductReviewSeeder::class);
        $this->call(SizeSeeder::class);
        Newsletter::factory()->count(20)->create();
        Notification::factory()->count(50)->create();
        Setting::factory()->create();

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
