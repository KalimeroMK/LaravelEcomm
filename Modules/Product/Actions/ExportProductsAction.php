<?php

declare(strict_types=1);

namespace Modules\Product\Actions;

use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\Products;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportProductsAction
{
    public function execute(): BinaryFileResponse
    {
        return Excel::download(new Products, 'products.xlsx');
    }
}
