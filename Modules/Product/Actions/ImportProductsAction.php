<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use InvalidArgumentException;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\Products;
use RuntimeException;
use Throwable;

class ImportProductsAction
{
    public function execute($file): void
    {
        if (! $file) {
            throw new InvalidArgumentException('No file provided.');
        }
        if (is_array($file)) {
            throw new InvalidArgumentException('Multiple files uploaded. Please upload only one file.');
        }

        try {
            Excel::import(new Products, $file);
        } catch (Throwable $e) {
            throw new RuntimeException('Failed to import products: '.$e->getMessage(), $e->getCode(), $e);
        }
    }
}
