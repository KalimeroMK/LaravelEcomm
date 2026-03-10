<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Illuminate\Support\Collection;
use Modules\Product\Actions\GetProductReviewsByUserAction;
use Modules\Product\Models\Product;
use Modules\Product\Models\ProductReview;
use Modules\User\Models\User;
use Tests\Unit\Actions\ActionTestCase;

class GetProductReviewsByUserActionTest extends ActionTestCase
{
    public function testExecuteReturnsReviewsForAuthenticatedUser(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        ProductReview::factory()->count(3)->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user);

        $action = app(GetProductReviewsByUserAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(3, $result);
    }

    public function testExecuteReturnsEmptyCollectionWhenUserHasNoReviews(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $action = app(GetProductReviewsByUserAction::class);
        $result = $action->execute();

        $this->assertInstanceOf(Collection::class, $result);
        $this->assertCount(0, $result);
        $this->assertTrue($result->isEmpty());
    }

    public function testExecuteReturnsOnlyCurrentUserReviews(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $product = Product::factory()->create();

        // Create reviews for user1
        ProductReview::factory()->count(2)->create([
            'user_id' => $user1->id,
            'product_id' => $product->id,
        ]);

        // Create reviews for user2
        ProductReview::factory()->count(4)->create([
            'user_id' => $user2->id,
            'product_id' => $product->id,
        ]);

        $this->actingAs($user1);

        $action = app(GetProductReviewsByUserAction::class);
        $result = $action->execute();

        $this->assertCount(2, $result);
        $this->assertTrue($result->every(fn ($review) => $review->user_id === $user1->id));
    }

    public function testExecuteReturnsReviewInstances(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        ProductReview::factory()->create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'rate' => 5,
            'review' => 'Great product!',
        ]);

        $this->actingAs($user);

        $action = app(GetProductReviewsByUserAction::class);
        $result = $action->execute();

        $this->assertCount(1, $result);
        $this->assertInstanceOf(ProductReview::class, $result->first());
        $this->assertEquals(5, $result->first()->rate);
        $this->assertEquals('Great product!', $result->first()->review);
    }
}
