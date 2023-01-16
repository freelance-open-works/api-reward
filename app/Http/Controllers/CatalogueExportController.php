<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\CatalogueExport;
use Maatwebsite\Excel\Facades\Excel;

class CatalogueExportController extends Controller
{
    /*
        Berisi fungsi untuk: export data katalog menjadi file excel.
        API dipanggil di views: CatalogueManager.vue
    */

    private $excel;

    public function __construct(Excel $excel)
    {
        $this->excel = $excel;
    }

    public function export(Request $request)
    {
        // return Excel::download(new CatalogueExport, 'stock-catalogue.xlsx');
        // return response([
        //     'message' => 'Download Success',
            
        // ], 200);

        // $from_date=$request->datestart;
        // $to_date = $request->datefinish;

        $periodeid = $request->periodeLaporan;

        return Excel::download(new CatalogueExport($periodeid), 'laporan-stok.xlsx');
    }
}
