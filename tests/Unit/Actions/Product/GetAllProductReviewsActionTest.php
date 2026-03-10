<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Illuminate\Support\Collection;
use Modules\Product\Actions\GetAllProductReviewsAction;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductReview;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetAllProductReviewsActionTest extends ActionTestCase
{
    public function testExecuteReturnsAllReviews(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        ProductReview::factory()->count(5)->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $action = app(GetAllProductReviewsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(5, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenNoReviews(): void
    {
        $action = app(GetAllProductReviewsAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsProductReviewInstances(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rate' => 4,
        ]);

        $action = app(GetAllProductReviewsAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ProductReview::class, $result->first());
        $this->assertEquals(4, $result->first()->rate);
    }

    public function testExecuteReturnsReviewsFromMultipleProductsAndUsers(): void
    {
        $users = User::factory()->count(3)->create();
        $products = Product::factory()->count(2)->create();

        foreach ($users as $user) {
            foreach ($products as $product) {
                ProductReview::factory()->create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                ]);
            }
        }

        $action = app(GetAllProductReviewsAction::class);
        $result = $action->execute();

        $this->assertCount(6, $result);
    }
}
