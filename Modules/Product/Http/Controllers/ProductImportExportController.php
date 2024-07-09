<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\Products;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ProductImportExportController extends Controller
{
    /**
     * Display the import/export view.
     */
    public function index(): View
    {
        return view('product::ExportImport.index');
    }

    /**
     * Export the products to an Excel file.
     *
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export(): BinaryFileResponse
    {
        return Excel::download(new Products(), 'products.xlsx');
    }

    /**
     * Import products from an uploaded Excel file.
     *
     * @return RedirectResponse
     */
    public function import(): RedirectResponse
    {
        $file = request()->file('file');

        if (!$file) {
            // Handle the case where no file is uploaded
            return back()->withErrors(['msg' => 'No file provided.']);
        }

        if (is_array($file)) {
            // Handle the case where multiple files are uploaded but only one is expected
            return back()->withErrors(['msg' => 'Multiple files uploaded. Please upload only one file.']);
        }

        try {
            Excel::import(new Products(), $file);
        } catch (Throwable $e) {
            // Handle the case where the import fails
            return back()->withErrors(['msg' => 'Failed to import products: '.$e->getMessage()]);
        }

        return back()->with('success', 'Products imported successfully.');
    }

}
