<?php

declare(strict_types=1);

namespace Modules\Product\Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Cart\Models\Cart;
use Modules\Core\Helpers\Helper;
use Modules\Product\Models\Product;
use Modules\Product\Tests\ProductTestCase;
use Modules\User\Models\User;

class ProductTypeTest extends ProductTestCase
{
    use RefreshDatabase;

    public function test_product_is_downloadable(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
        ]);
        
        $this->assertTrue($product->isDownloadable());
        $this->assertFalse($product->isVirtual());
        $this->assertFalse($product->requiresShipping());
    }

    public function test_product_is_downloadable_with_flag(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
            'is_downloadable' => true,
        ]);
        
        $this->assertTrue($product->isDownloadable());
    }

    public function test_product_is_virtual(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_VIRTUAL,
        ]);
        
        $this->assertTrue($product->isVirtual());
        $this->assertFalse($product->isDownloadable());
        $this->assertFalse($product->requiresShipping());
    }

    public function test_product_is_virtual_with_flag(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
            'is_virtual' => true,
        ]);
        
        $this->assertTrue($product->isVirtual());
    }

    public function test_simple_product_requires_shipping(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
        ]);
        
        $this->assertTrue($product->requiresShipping());
        $this->assertFalse($product->isVirtual());
        $this->assertFalse($product->isDownloadable());
    }

    public function test_configurable_product_requires_shipping(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_CONFIGURABLE,
        ]);
        
        $this->assertTrue($product->requiresShipping());
    }

    public function test_helper_cart_requires_shipping_with_physical_products(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
            'price' => 100,
        ]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
        ]);
        
        $this->assertTrue(Helper::cartRequiresShipping($user->id));
    }

    public function test_helper_cart_requires_shipping_with_virtual_products(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'type' => Product::TYPE_VIRTUAL,
            'price' => 100,
        ]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
        ]);
        
        $this->assertFalse(Helper::cartRequiresShipping($user->id));
    }

    public function test_helper_cart_requires_shipping_with_downloadable_products(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
            'price' => 100,
        ]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
        ]);
        
        $this->assertFalse(Helper::cartRequiresShipping($user->id));
    }

    public function test_helper_cart_requires_shipping_with_mixed_products(): void
    {
        $user = User::factory()->create();
        
        $physicalProduct = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
            'price' => 100,
        ]);
        
        $virtualProduct = Product::factory()->create([
            'type' => Product::TYPE_VIRTUAL,
            'price' => 50,
        ]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $physicalProduct->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
        ]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $virtualProduct->id,
            'quantity' => 1,
            'price' => 50,
            'amount' => 50,
        ]);
        
        // Should require shipping because of physical product
        $this->assertTrue(Helper::cartRequiresShipping($user->id));
    }

    public function test_helper_cart_has_downloadable(): void
    {
        $user = User::factory()->create();
        
        $downloadableProduct = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
            'price' => 100,
        ]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $downloadableProduct->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
        ]);
        
        $this->assertTrue(Helper::cartHasDownloadable($user->id));
    }

    public function test_helper_cart_has_no_downloadable(): void
    {
        $user = User::factory()->create();
        
        $simpleProduct = Product::factory()->create([
            'type' => Product::TYPE_SIMPLE,
            'price' => 100,
        ]);
        
        Cart::create([
            'user_id' => $user->id,
            'product_id' => $simpleProduct->id,
            'quantity' => 1,
            'price' => 100,
            'amount' => 100,
        ]);
        
        $this->assertFalse(Helper::cartHasDownloadable($user->id));
    }

    public function test_product_casts(): void
    {
        $product = Product::factory()->create([
            'type' => Product::TYPE_DOWNLOADABLE,
            'is_virtual' => true,
            'is_downloadable' => true,
            'max_downloads' => 5,
            'download_expiry_days' => 7,
            'service_duration_minutes' => 60,
        ]);
        
        $this->assertIsBool($product->is_virtual);
        $this->assertIsBool($product->is_downloadable);
        $this->assertIsInt($product->max_downloads);
        $this->assertIsInt($product->download_expiry_days);
        $this->assertIsInt($product->service_duration_minutes);
    }
}
