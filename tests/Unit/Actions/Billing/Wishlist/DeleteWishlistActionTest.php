<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Billing\Wishlist;

use Modules\Billing\Actions\Wishlist\DeleteWishlistAction;
use Modules\Billing\Models\Wishlist;
use Modules\Product\Models\Product;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class DeleteWishlistActionTest extends ActionTestCase
{
    public function testExecuteDeletesWishlistSuccessfully(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
            'amount' => 100.00,
        ]);

        $this->assertDatabaseHas('wishlists', ['id' => $wishlist->id]);

        $action = app(DeleteWishlistAction::class);
        $action->execute($wishlist->id);

        $this->assertDatabaseMissing('wishlists', ['id' => $wishlist->id]);
    }

    public function testExecuteDeletesSpecificWishlist(): void
    {
        $user = User::factory()->create();
        $product1 = Product::factory()->create();
        $product2 = Product::factory()->create();

        $wishlist1 = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product1->id,
            'quantity' => 1,
            'price' => 50.00,
            'amount' => 50.00,
        ]);

        $wishlist2 = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 75.00,
            'amount' => 75.00,
        ]);

        $action = app(DeleteWishlistAction::class);
        $action->execute($wishlist1->id);

        $this->assertDatabaseMissing('wishlists', ['id' => $wishlist1->id]);
        $this->assertDatabaseHas('wishlists', ['id' => $wishlist2->id]);
    }

    public function testExecuteReturnsVoid(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $wishlist = Wishlist::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 100.00,
            'amount' => 100.00,
        ]);

        $action = app(DeleteWishlistAction::class);
        $result = $action->execute($wishlist->id);

        $this->assertNull($result);
    }
}
