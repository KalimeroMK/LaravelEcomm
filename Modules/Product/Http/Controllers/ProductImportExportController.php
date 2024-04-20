<?php

namespace Modules\Product\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Product\Exports\Products;
use PhpOffice\PhpSpreadsheet\Exception;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductImportExportController extends Controller
{

    public function index()
    {
        return view('product::ExportImport.index');
    }

    /**
     * @return BinaryFileResponse
     * @throws Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */

    public function export()

    {
        return Excel::download(new Products(), 'products.xlsx');
    }


    /**
     * @return RedirectResponse
     */

    public function import()

    {
        Excel::import(new Products(), request()->file('file'));


        return back();
    }
}
