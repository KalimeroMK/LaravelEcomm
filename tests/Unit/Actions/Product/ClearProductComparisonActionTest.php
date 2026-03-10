<?php

declare(strict_types=1);

namespace Tests\Unit\Actions\Product;

use Modules\Product\Actions\ClearProductComparisonAction;
use Tests\Unit\Actions\ActionTestCase;

class ClearProductComparisonActionTest extends ActionTestCase
{
    private array $storage = [1, 2, 3];

    private function createAction(): ClearProductComparisonAction
    {
        return new ClearProductComparisonAction(
            clearStorage: fn () => $this->storage = []
        );
    }

    public function testExecuteClearsComparisonStorage(): void
    {
        $action = $this->createAction();

        $this->assertNotEmpty($this->storage);

        $action->execute();

        $this->assertEmpty($this->storage);
    }

    public function testExecuteWorksWithEmptyStorage(): void
    {
        $this->storage = [];
        $action = $this->createAction();

        // Should not throw any exception
        $action->execute();

        $this->assertEmpty($this->storage);
    }
}
