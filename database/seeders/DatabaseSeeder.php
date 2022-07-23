<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Modules\Banner\Models\Banner;
use Modules\Brand\Models\Brand;
use Modules\Cart\Models\Cart;
use Modules\Message\Models\Message;
use Modules\Order\Models\Order;
use Modules\Post\Models\PostComment;
use Modules\Settings\Models\Setting;
use Modules\Tag\Models\Tag;

class DatabaseSeeder extends Seeder
{
    private $faker;
    
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
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
