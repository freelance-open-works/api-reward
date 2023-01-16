<?php

namespace App\Http\Controllers;

use App\Models\CatalogueType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CatalogueTypeController extends Controller
{
    /*
        Berisi fungsi untuk: mengolah data tipe katalog.
        API dipanggil di views: CatalogueType.vue
    */

    public function index()
    {
        $cat_type = DB::table('catalogue_type')
        ->get();

        if (count($cat_type) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $cat_type,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    public function store(Request $request){
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'CTG_TYPE' => 'required',
            'CTG_MAX_REDEEM' => 'required',
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        $catalogue_type = CatalogueType::create($storeData); //menambah data tipe baru
        return response([
            'message' => 'Add catalogue type Success',
            'data' => $catalogue_type,
        ],200); //return data tipe baru dalam bentuk json
    }
    public function update(Request $request, $id){
        $catalogue_type = CatalogueType::find($id); //mencari data redeem berdasarkan id
        if(is_null($catalogue_type)){
            return response([
                'message' => 'Type Not Found',
                'data' => null
            ],404);
        } //return message saat data redeem tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'CTG_TYPE' => 'required',
            'CTG_MAX_REDEEM' => 'required',
        ]); //membuat rule validasi input

        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $catalogue_type->CTG_TYPE = $updateData['CTG_TYPE']; //edit nama tipe
        $catalogue_type->CTG_MAX_REDEEM = $updateData['CTG_MAX_REDEEM']; //edit status meja

        if($catalogue_type->save()){
            return response([
                'message' => 'Update status Success',
                'data' => $catalogue_type,
            ],200);
        } //return data redeem yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update status Failed',
            'data' => null,
        ],400); //return message saat redeem gagal di edit
    }

    //Fungsi Delete berdasarkan ID
    public function destroy($id)
    {
        $type_del = CatalogueType::find($id); //mencari data Catalogue Type berdasarkan id

        if(is_null($type_del)){
            return response([
                'message' => 'Type Not Found',
                'data' => null
            ],404);
        } //return message saat data Catalogue Type tidak ditemukan

        if($type_del->delete()){
            return response([
                'message' => 'Delete Catalogue Type Success',
                'data' => $type_del,
            ],200);
        } //return message saat berhasil menghapus data Catalogue Type
        return response([
            'message' => 'Delete Catalogue Type Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data Catalogue Type
    }
}
