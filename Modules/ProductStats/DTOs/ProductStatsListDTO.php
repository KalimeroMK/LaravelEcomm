<?php

declare(strict_types=1);

namespace Modules\ProductStats\DTOs;

use Illuminate\Pagination\LengthAwarePaginator;

class ProductStatsListDTO
{
    /** @var LengthAwarePaginator<ProductStatsDTO> */
    public LengthAwarePaginator $stats;

    /**
     * @param LengthAwarePaginator<ProductStatsDTO> $stats
     */
    public function __construct(LengthAwarePaginator $stats)
    {
        $this->stats = $stats;
    }
}
