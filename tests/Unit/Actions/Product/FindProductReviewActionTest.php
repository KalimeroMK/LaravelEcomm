<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\FindProductReviewAction;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductReview;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class FindProductReviewActionTest extends ActionTestCase
{
    public function testExecuteFindsReviewById(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $review = ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rate' => 5,
            'review' => 'Excellent product!',
        ]);

        $action = app(FindProductReviewAction::class);
        $result = $action->execute($review->id);

        $this->assertInstanceOf(ProductReview::class, $result);
        $this->assertEquals($review->id, $result->id);
        $this->assertEquals(5, $result->rate);
        $this->assertEquals('Excellent product!', $result->review);
    }

    public function testExecuteLoadsUserAndProductRelations(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $review = ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $action = app(FindProductReviewAction::class);
        $result = $action->execute($review->id);

        $this->assertTrue($result->relationLoaded('user'));
        $this->assertTrue($result->relationLoaded('product'));
        $this->assertEquals($user->id, $result->user->id);
        $this->assertEquals($product->id, $result->product->id);
    }

    public function testExecuteThrowsExceptionForNonExistentReview(): void
    {
        $action = app(FindProductReviewAction::class);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute(99999);
    }
}
