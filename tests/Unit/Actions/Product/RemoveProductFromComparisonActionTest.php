<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\RemoveProductFromComparisonAction;
use Tests\Unit\Actions\ActionTestCase;

class RemoveProductFromComparisonActionTest extends ActionTestCase
{
    private array $storage = [];

    private function createAction(): RemoveProductFromComparisonAction
    {
        return new RemoveProductFromComparisonAction(
            getStorage: fn () => $this->storage,
            putStorage: fn (array $value) => $this->storage = $value
        );
    }

    public function testExecuteRemovesProductFromComparison(): void
    {
        $this->storage = [1, 2, 3];
        $action = $this->createAction();

        $result = $action->execute(2);

        $this->assertEquals(2, $result['product_id']);
        $this->assertEquals(2, $result['comparison_count']);
        $this->assertNotContains(2, $result['products']);
        $this->assertContains(1, $result['products']);
        $this->assertContains(3, $result['products']);
    }

    public function testExecuteReturnsCorrectComparisonCount(): void
    {
        $this->storage = [10, 20, 30, 40];
        $action = $this->createAction();

        $result = $action->execute(20);

        $this->assertEquals(3, $result['comparison_count']);
    }

    public function testExecuteDoesNothingForNonExistentProduct(): void
    {
        $this->storage = [1, 2, 3];
        $action = $this->createAction();

        $result = $action->execute(999);

        $this->assertEquals(3, $result['comparison_count']);
        $this->assertContains(1, $result['products']);
        $this->assertContains(2, $result['products']);
        $this->assertContains(3, $result['products']);
    }

    public function testExecuteReindexesArrayAfterRemoval(): void
    {
        $this->storage = [1, 2, 3];
        $action = $this->createAction();

        $result = $action->execute(2);

        // Array should be reindexed (0 => 1, 1 => 3)
        $this->assertArrayHasKey(0, $result['products']);
        $this->assertArrayHasKey(1, $result['products']);
        $this->assertArrayNotHasKey(2, $result['products']);
    }

    public function testExecuteUpdatesStorage(): void
    {
        $this->storage = [1, 2, 3];
        $action = $this->createAction();

        $action->execute(2);

        $this->assertNotContains(2, $this->storage);
        $this->assertCount(2, $this->storage);
    }
}
