<?php

namespace App\Http\Controllers;

use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PeriodeController extends Controller
{
     /*
        Berisi fungsi untuk mengelola data periode.
        API dipanggil di views PeriodeManager.vue
    */
    public function getAllPeriode()
    {
        $periode = new Periode();
        $data = $periode->getAllPeriode(); // dari Model Periode. Mendapatkan semua data periode.
        $active = $periode->getActiveYearPeriode(); //dari Model Periode. Mendapatkan periode yang statusnya aktif.

        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
                'activePeriode' => $active,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    // Remove the specified resource from storage.
    public function destroy($id)
    {
        $data = Periode::find($id); //mencari data berdsaar id

        if (is_null($data)) {
            return response([
                'message' => 'Periode Not Found',
                'data' => null
            ], 404); //return message data product tidak ditemukan
        }

        if ($data->delete()) {
            return response([
                'message' => 'Delete Periode Success',
                'data' => $data
            ], 200); //return message data product berhasil dihapus
        }

        return response([
            'message' => 'Delete Periode Failed',
            'data' => null
        ], 400); //return message data product gagal dihapus
    }

    public function store(Request $request) {
        $data = $request->all();
        $validate = Validator::make($data, [
            'YEAR_START' => 'required',
            'YEAR_FINISH' => 'required',
            'STATUS' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        $data = Periode::create($data); //menambah data pada product baru
        return response([
            'message' => 'Add Periode Success',
            'data' => $data
        ], 201);
    }

     // Update the specified resource in storage.
     public function update(Request $request, $id)
     {
         $data = Periode::find($id); //mencari data product berdasar id
         if (is_null($data)) {
             return response([
                 'message' => 'Periode Not Found',
                 'data' => null
             ], 404);
         } //return message saat data tidak ditemukan
 
         $updateData = $request->all(); //abil semua input dari api client
         $validate = Validator::make($updateData, [
            'YEAR_START' => 'required',
            'YEAR_FINISH' => 'required',
            'STATUS' => 'required',
         ]); //rule validasi input
 
         if ($validate->fails())
             return response(['message' => $validate->errors()], 400); //return error invalid input
 
         $data->YEAR_START = $updateData['YEAR_START'];
         $data->YEAR_FINISH = $updateData['YEAR_FINISH'];
         $data->STATUS = $updateData['STATUS'];
 
         if ($data->save()) {
             return response([
                 'message' => 'Update Periode Success',
                 'data' => $data
             ], 200);
         } //return data yang telah diedit dalam bentuk json
 
 
         return response([
             'message' => 'Update Periode Failed',
             'data' => $data
         ], 400);  //return message saat produk gagal diedit
     }
 
}
