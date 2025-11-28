<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\Products;

class ExportProductsAction
{
    public function execute(string $filename = 'products.xlsx'): string
    {
        $filePath = storage_path('app/exports/'.$filename);
        Excel::store(new Products, 'exports/'.$filename);

        return $filePath;
    }
}
