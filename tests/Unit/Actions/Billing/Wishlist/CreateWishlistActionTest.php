<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Wishlist;

use Modules\Billing\Actions\Wishlist\CreateWishlistAction;
use Modules\Billing\DTOs\WishlistDTO;
use Modules\Billing\Models\Wishlist;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class CreateWishlistActionTest extends ActionTestCase
{
    public function testExecuteCreatesWishlistSuccessfully(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);

        $dto = new WishlistDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            quantity: 2,
            price: $product->price,
            discount: 10.0, // 10% discount
        );

        $action = app(CreateWishlistAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(Wishlist::class, $result);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals($product->id, $result->product_id);
        $this->assertEquals(2, $result->quantity);
        $this->assertDatabaseHas('wishlists', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function testExecuteCalculatesDiscountedPrice(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);

        $dto = new WishlistDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            quantity: 1,
            price: 100.00,
            discount: 20.0, // 20% discount
        );

        $action = app(CreateWishlistAction::class);
        $result = $action->execute($dto);

        // Price should be discounted: 100 - (100 * 20 / 100) = 80
        $this->assertEquals(80.00, $result->price);
    }

    public function testExecuteCalculatesAmount(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 50.00]);

        $dto = new WishlistDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            quantity: 3,
            price: 50.00,
            discount: 0.0,
        );

        $action = app(CreateWishlistAction::class);
        $result = $action->execute($dto);

        // Amount should be: 50 * 3 = 150
        $this->assertEquals(150.00, $result->amount);
    }

    public function testExecuteCalculatesAmountWithDiscount(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 100.00]);

        $dto = new WishlistDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            quantity: 2,
            price: 100.00,
            discount: 25.0, // 25% discount
        );

        $action = app(CreateWishlistAction::class);
        $result = $action->execute($dto);

        // Discounted price: 100 - (100 * 25 / 100) = 75
        // Amount: 75 * 2 = 150
        $this->assertEquals(75.00, $result->price);
        $this->assertEquals(150.00, $result->amount);
    }

    public function testExecuteWithNoDiscount(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['price' => 75.00]);

        $dto = new WishlistDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            quantity: 1,
            price: 75.00,
            discount: 0.0,
        );

        $action = app(CreateWishlistAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(75.00, $result->price);
        $this->assertEquals(75.00, $result->amount);
    }
}
