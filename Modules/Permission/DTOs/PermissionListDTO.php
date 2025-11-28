<?php

declare(strict_types=1);

namespace Modules\Permission\DTOs;

use Illuminate\Support\Collection;

readonly class PermissionListDTO
{
    public function __construct(
        public Collection $permissions
    ) {}
}
