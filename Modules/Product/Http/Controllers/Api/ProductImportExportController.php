<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Modules\Core\Http\Controllers\Api\CoreController;
use Modules\Product\Actions\ExportProductsAction;
use Modules\Product\Actions\ImportProductsAction;
use Modules\Product\Http\Requests\Api\Import;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ProductImportExportController extends CoreController
{
    public function __construct(
        private readonly ExportProductsAction $exportProductsAction,
        private readonly ImportProductsAction $importProductsAction
    ) {}

    /**
     * Export the products to an Excel file.
     */
    public function export(): BinaryFileResponse|JsonResponse
    {
        $this->authorize('export', \Modules\Product\Models\Product::class);

        try {
            $filePath = $this->exportProductsAction->execute();

            return response()->download($filePath, 'products_'.now()->format('Y-m-d_H-i-s').'.xlsx');
        } catch (Throwable $e) {
            return $this
                ->setMessage('Export failed: '.$e->getMessage())
                ->setStatusCode(500)
                ->respond(null);
        }
    }

    /**
     * Import products from an uploaded Excel file.
     */
    public function import(Import $request): JsonResponse
    {
        $this->authorize('import', \Modules\Product\Models\Product::class);

        try {
            $this->importProductsAction->execute($request->file('file'));

            return $this
                ->setMessage('Products imported successfully.')
                ->respond(null);
        } catch (Throwable $e) {
            return $this
                ->setMessage('Import failed: '.$e->getMessage())
                ->setStatusCode(422)
                ->respond(null);
        }
    }
}
