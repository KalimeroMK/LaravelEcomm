<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\UpdateProductReviewAction;
use Modules\Product\DTOs\ProductReviewDTO;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductReview;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class UpdateProductReviewActionTest extends ActionTestCase
{
    public function testExecuteUpdatesReviewSuccessfully(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $review = ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rate' => 3,
            'review' => 'Original review',
        ]);

        $dto = new ProductReviewDTO(
            id: $review->id,
            product_id: $product->id,
            user_id: $user->id,
            review: 'Updated review text',
            rate: 5
        );

        $action = app(UpdateProductReviewAction::class);
        $result = $action->execute($review->id, $dto);

        $this->assertInstanceOf(ProductReview::class, $result);
        $this->assertEquals('Updated review text', $result->review);
        $this->assertEquals(5, $result->rate);
    }

    public function testExecuteSavesChangesToDatabase(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $review = ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rate' => 2,
            'review' => 'Bad product',
        ]);

        $dto = new ProductReviewDTO(
            id: $review->id,
            product_id: $product->id,
            user_id: $user->id,
            review: 'Actually, it\'s good!',
            rate: 4
        );

        $action = app(UpdateProductReviewAction::class);
        $action->execute($review->id, $dto);

        $this->assertDatabaseHas('product_reviews', [
            'id' => $review->id,
            'review' => 'Actually, it\'s good!',
            'rate' => 4,
        ]);
    }

    public function testExecuteThrowsExceptionForNonExistentReview(): void
    {
        $dto = new ProductReviewDTO(
            id: null,
            product_id: 1,
            user_id: 1,
            review: 'Test',
            rate: 5
        );

        $action = app(UpdateProductReviewAction::class);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute(99999, $dto);
    }

    public function testExecuteUpdatesOnlySpecifiedFields(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $review = ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rate' => 4,
            'review' => 'Good product',
            'status' => 'active',
        ]);

        $dto = new ProductReviewDTO(
            id: $review->id,
            product_id: $product->id,
            user_id: $user->id,
            review: 'Excellent product!',
            rate: 5
        );

        $action = app(UpdateProductReviewAction::class);
        $result = $action->execute($review->id, $dto);

        // Check that the review text was updated
        $this->assertEquals('Excellent product!', $result->review);
        // Check that rate was updated
        $this->assertEquals(5, $result->rate);
        // Status should remain unchanged (if not part of DTO)
        $this->assertEquals('active', $result->fresh()->status);
    }
}
