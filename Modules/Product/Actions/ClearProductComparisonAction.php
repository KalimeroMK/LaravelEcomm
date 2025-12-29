<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Closure;

readonly class ClearProductComparisonAction
{
    public function __construct(
        private Closure $clearStorage
    ) {}

    public function execute(): void
    {
        $clearStorage = $this->clearStorage;
        $clearStorage();
    }
}
