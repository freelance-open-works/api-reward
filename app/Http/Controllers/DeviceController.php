<?php

namespace App\Http\Controllers;

use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DeviceController extends Controller
{
    /*
        Berisi fungsi untuk CRUD device.
        API dipanggil di views DeviceManager.vue
    */

    public function index()
    {
        $device = new Device();
        $data = $device->getAllDevice();

        if(count($data) > 0){
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ],200);
        } //return data semua history dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null,
        ],404); //return message data history kosong
    }

    public function store(Request $request)
    {
        $data = $request->all();
        $validate = Validator::make($data, [
            'DEVICE' => 'required',
            'VERSION' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        $data = Device::create($data); //menambah data pada device baru
        return response([
            'message' => 'Add Device Success',
            'data' => $data
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $data = Device::find($id); //mencari data device berdasar id
        if (is_null($data)) {
            return response([
                'message' => 'Device Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //abil semua input dari api client
        $validate = Validator::make($updateData, [
           'DEVICE' => 'required',
           'VERSION' => 'required',
        ]); //rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $data->DEVICE = $updateData['DEVICE'];
        $data->VERSION = $updateData['VERSION'];

        if ($data->save()) {
            return response([
                'message' => 'Update Device Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update Device Failed',
            'data' => $data
        ], 400);  //return message saat produk gagal diedit
    }

    public function destroy($id)
    {
        $data = Device::find($id); //mencari data berdsaar id

        if (is_null($data)) {
            return response([
                'message' => 'Device Not Found',
                'data' => null
            ], 404); //return message data device tidak ditemukan
        }

        if ($data->delete()) {
            return response([
                'message' => 'Delete Device Success',
                'data' => $data
            ], 200); //return message data device berhasil dihapus
        }

        return response([
            'message' => 'Delete Device Failed',
            'data' => null
        ], 400); //return message data device gagal dihapus
    }

}
