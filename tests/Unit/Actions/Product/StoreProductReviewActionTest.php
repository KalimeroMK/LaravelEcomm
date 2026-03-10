<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\StoreProductReviewAction;
use Modules\Product\DTOs\ProductReviewDTO;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductReview;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class StoreProductReviewActionTest extends ActionTestCase
{
    public function testExecuteCreatesReviewSuccessfully(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $dto = new ProductReviewDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            review: 'Great product! Highly recommended.',
            rate: 5
        );

        $action = app(StoreProductReviewAction::class);
        $result = $action->execute($dto);

        $this->assertInstanceOf(ProductReview::class, $result);
        $this->assertEquals($product->id, $result->product_id);
        $this->assertEquals($user->id, $result->user_id);
        $this->assertEquals('Great product! Highly recommended.', $result->review);
        $this->assertEquals(5, $result->rate);
    }

    public function testExecuteSavesReviewToDatabase(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $dto = new ProductReviewDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            review: 'Test review',
            rate: 4
        );

        $action = app(StoreProductReviewAction::class);
        $action->execute($dto);

        $this->assertDatabaseHas('product_reviews', [
            'product_id' => $product->id,
            'user_id' => $user->id,
            'review' => 'Test review',
            'rate' => 4,
        ]);
    }

    public function testExecuteCreatesReviewWithMinimumRate(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $dto = new ProductReviewDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            review: 'Not good',
            rate: 1
        );

        $action = app(StoreProductReviewAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(1, $result->rate);
    }

    public function testExecuteCreatesReviewWithMaximumRate(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $dto = new ProductReviewDTO(
            id: null,
            product_id: $product->id,
            user_id: $user->id,
            review: 'Excellent!',
            rate: 5
        );

        $action = app(StoreProductReviewAction::class);
        $result = $action->execute($dto);

        $this->assertEquals(5, $result->rate);
    }
}
