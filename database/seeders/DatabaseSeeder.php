<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Admin\Database\Seeders\ConditionSeeder;
use Modules\Admin\Database\Seeders\SizeSeeder;
use Modules\Attribute\Models\Attribute;
use Modules\Attribute\Models\AttributeValue;
use Modules\Banner\Models\Banner;
use Modules\Brand\Models\Brand;
use Modules\Cart\Models\Cart;
use Modules\Category\Database\Seeders\CategoryPostSeeder;
use Modules\Category\Database\Seeders\CategoryProductSeeder;
use Modules\Coupon\Database\Seeders\CouponSeeder;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
use Modules\Order\Models\Order;
use Modules\Post\Models\PostComment;
use Modules\Product\Database\Seeders\ProductReviewSeeder;
use Modules\Settings\Models\Setting;
use Modules\Tag\Models\Tag;
use Modules\User\Database\Seeders\PermissionTableSeeder;

class DatabaseSeeder extends Seeder
{
    
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $this->call(PermissionTableSeeder::class);
        $this->call(CouponSeeder::class);
        Setting::factory()->create();
        Tag::factory()->count(100)->create();
        Brand::factory()->count(5)->create();
        Banner::factory()->count(5)->create();
        $this->call(CategoryProductSeeder::class);
        Order::factory()->count(200)->create();
        $this->call(CategoryPostSeeder::class);
        Message::factory()->count(50)->create();
        Cart::factory()->count(200)->create();
        PostComment::factory()->count(200)->create();
        $this->call(ProductReviewSeeder::class);
        $this->call(SizeSeeder::class);
        $this->call(ConditionSeeder::class);
        Newsletter::factory()->count(20)->create();
        Attribute::factory()->count(20)->create();
        AttributeValue::factory()->count(200)->create();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
