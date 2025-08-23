<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Attribute\Database\Seeders\AttributeGroupSeeder;
use Modules\Attribute\Database\Seeders\AttributeOptionSeeder;
use Modules\Attribute\Database\Seeders\AttributeSeeder;
use Modules\Attribute\Database\Seeders\AttributeValueSeeder;
use Modules\Banner\Models\Banner;
use Modules\Brand\Models\Brand;
use Modules\Bundle\Models\BundleProduct;
use Modules\Cart\Models\Cart;
use Modules\Category\Models\Category;
use Modules\Complaint\Models\Complaint;
use Modules\Complaint\Models\ComplaintReply;
use Modules\Coupon\Database\Seeders\CouponSeeder;
use Modules\Message\Models\Message;
use Modules\Newsletter\Models\Newsletter;
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
        Category::factory()->count(10)->create();
        Banner::factory()->count(5)->create();
        Brand::factory()->count(5)->create();
        $this->call(CouponSeeder::class);
        Tag::factory()->count(100)->create();
        Post::factory()->count(200)->withCategoriesAndTags()->create();
        Message::factory()->count(50)->create();
        $this->call(PageSeeder::class);
        $this->call(AttributeGroupSeeder::class);
        $this->call(AttributeSeeder::class);
        $this->call(AttributeValueSeeder::class);
        $this->call(AttributeOptionSeeder::class);
        Product::factory()->count(400)->withCategoriesAndTags()->withAttributes()->create();
        Cart::factory()->count(200)->create();
        BundleProduct::factory()->count(50)->create();
        $this->call(ProductReviewSeeder::class);
        Newsletter::factory()->count(20)->create();
        Setting::factory()->create();
        Complaint::factory()->count(50)->create();
        \Modules\Complaint\Database\Factories\ComplaintReplaiesFactory::new()->count(50)->create();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
