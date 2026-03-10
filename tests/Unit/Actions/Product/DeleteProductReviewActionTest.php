<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\DeleteProductReviewAction;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductReview;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class DeleteProductReviewActionTest extends ActionTestCase
{
    public function testExecuteDeletesReviewSuccessfully(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $review = ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->assertDatabaseHas('product_reviews', ['id' => $review->id]);

        $action = app(DeleteProductReviewAction::class);
        $action->execute($review->id);

        $this->assertDatabaseMissing('product_reviews', ['id' => $review->id]);
    }

    public function testExecuteThrowsExceptionForNonExistentReview(): void
    {
        $action = app(DeleteProductReviewAction::class);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        $action->execute(99999);
    }
}
