<?php

declare(strict_types=1);

namespace Modules\Cart\Actions;

use Modules\Cart\Models\Cart;
use Modules\Cart\Repository\CartRepository;

readonly class FindCartAction
{
    public function __construct(private CartRepository $repository) {}

    public function execute(int $id): Cart
    {
        return $this->repository->findById($id);
    }
}
