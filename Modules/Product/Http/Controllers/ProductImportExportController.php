<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\Products;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
     */
    public function import(): RedirectResponse
    {
        Excel::import(new Products(), request()->file('file'));

        return back();
    }
}
