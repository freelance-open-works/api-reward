<?php

namespace App\Http\Controllers;

use App\Models\Maintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MaintenanceController extends Controller
{
     /*
        Berisi fungsi untuk mengelola maintenance (aplikasi sedang down/tidak).
        API dipanggil di views MaintenanceManager.vue
    */

    public function __construct()
    {
        $this->maintenance = new Maintenance();
    }
  
    public function getStatus()
    {   
        /*
            Fungsi: Mendapatkan status maintenance aplikasi amtarewards.
            Return: 
                - message       -> String -> Pesan pemanggilan API
                - data          -> Object -> Data stautus maintenance
        */
        $data = $this->maintenance->getRecentStatusValue(); //dari Model Maintenance. Untuk mendapat data status maintenance

        if (!is_null($data)) {
            return response([
                'message' => 'Retrieve Success!',
                'data' => $data
            ], 200);
        } //return message saat data tidak ditemukan

    }

    public function update(Request $request, Maintenance $maintenance)
    {
        $data = $this->maintenance->getRecentStatusValue(); //dari Model Maintenance. Untuk mendapat data status maintenance
        if (is_null($data)) {
            return response([
                'message' => 'Maintenance Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //abil semua input dari api client
        $validate = Validator::make($updateData, [
           'STATUS' => 'required',
           'UPDATE_TIME' => 'required',
        ]); //rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $data->STATUS = $updateData['STATUS'];
        $data->UPDATE_TIME = $updateData['UPDATE_TIME'];

        if ($data->save()) {
            return response([
                'message' => 'Update Maintenance Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update Maintenance Failed',
            'data' => $data
        ], 400);  //return message saat produk gagal diedit
    }
}
