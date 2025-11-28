<?php

declare(strict_types=1);

namespace Modules\Cart\Actions;

use Modules\Cart\Repository\CartRepository;

readonly class DeleteCartAction
{
    public function __construct(private CartRepository $repository) {}

    public function execute(int $id): void
    {
        $this->repository->destroy($id);
    }
}
