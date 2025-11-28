<?php

declare(strict_types=1);

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Modules\Product\Actions\ExportProductsAction;
use Modules\Product\Actions\ImportProductsAction;
use Modules\Product\Http\Requests\Import;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ProductImportExportController extends Controller
{
    private ExportProductsAction $exportProductsAction;

    private ImportProductsAction $importProductsAction;

    public function __construct(
        ExportProductsAction $exportProductsAction,
        ImportProductsAction $importProductsAction
    ) {
        $this->exportProductsAction = $exportProductsAction;
        $this->importProductsAction = $importProductsAction;
    }

    /**
     * Display the import/export view.
     */
    public function index(): View
    {
        return view('product::ExportImport.index');
    }

    /**
     * Export the products to an Excel file.
     */
    public function export(): BinaryFileResponse
    {
        $filePath = $this->exportProductsAction->execute();

        return response()->download($filePath);
    }

    /**
     * Import products from an uploaded Excel file.
     */
    public function import(Import $request): RedirectResponse
    {
        try {
            $this->importProductsAction->execute($request->file('file'));
        } catch (Throwable $e) {
            return back()->withErrors(['msg' => $e->getMessage()]);
        }

        return back()->with('success', 'Products imported successfully.');
    }
}
