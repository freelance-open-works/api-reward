<?php

namespace App\Http\Controllers;

use App\Models\Catalogue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CatalogueStockController extends Controller
{
    /*
        Berisi fungsi untuk: menginput data stok gudang katalog.
        API dipanggil di views: CatalogueStock.vue
    */

    public function index()
    {
        $catalogue = DB::table('catalogue AS c')
        ->join('catalogue_type AS ct', 'ct.ID_CTG_TYPE', '=', 'c.ID_CTG_TYPE')
        ->get();

        if (count($catalogue) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $catalogue,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    public function update(Request $request, $id)
    {
        $data = Catalogue::find($id); //mencari data Catalogue berdasar id
        if (is_null($data)) {
            return response([
                'message' => 'Catalogue Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //abil semua input dari api client
        $validate = Validator::make($updateData, [
            'STOCK_GUDANG' => 'required',
        ]); //validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $data->STOCK_GUDANG = $updateData['STOCK_GUDANG'];

        if ($data->save()) {
            return response([
                'message' => 'Update StockGudang Catalogue Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update StockGudang Catalogue Failed',
            'data' => $data
        ], 400);  //return message saat Catalogue gagal diedit
    }
}
