<?php

namespace App\Http\Controllers;

use App\Models\Catalogue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;

class CatalogueController extends Controller
{
    /*
        Berisi fungsi untuk CRUD katalog / produk.
        API dipanggil di views: CatalogueManager.vue
    */

    public function getDetailCatalogue($id)
    {
        /*
            Fungsi: untuk mendapat satu data katalog berdasarkan ID katalog.
            Param:
                - id      -> integer
            Return: 
                - message       -> String -> Pesan pemanggilan API
                - data          -> Object -> Data katalog
        */

        $ctg = DB::table('catalogue')
            ->where('ID_CATALOGUE', '=', $id)
            ->get()
            ->first();

        if (!is_null($ctg)) {
            return response([
                'message' => 'Retrieve catalogue Success',
                'data' => $ctg
            ], 200);
        } //return data catalogue yang ditemukan dalam bentuk json

        return response([
            'message' => 'catalogue Not Found',
            'data' => null
        ], 404); //return message saat data catalogue tidak ditemukan
    }

    public function index()
    {
        $catalogue = DB::table('catalogue AS c')
            ->join('catalogue_type AS ct', 'ct.ID_CTG_TYPE', '=', 'c.ID_CTG_TYPE')
            ->get();

        $yearperiode = DB::table('year_period')
            ->select('ID_PERIOD')
            ->selectRaw("CONCAT(DATE(YEAR_START), ' / ', DATE(YEAR_FINISH)) AS year_period")
            ->get();

        $cat_type = DB::table('catalogue_type')
            ->select('ID_CTG_TYPE', 'CTG_TYPE')
            ->get();

        if (count($catalogue) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $catalogue,
                'yearperiod' => $yearperiode,
                'cat_type' => $cat_type,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    public function store(Request $request)
    {
        $storeData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($storeData, [
            'ID_PERIOD' => 'required',
            'NAME_CATALOGUE' => 'required',
            'DESCRIPTION' => 'required',
            'ID_CTG_TYPE' => 'required',
            'POINT_REQ' => 'required',
            'STOCK' => 'required',
            'DESTINATION' => 'required',
            'PHOTO' => 'image',
        ]); //membuat rule validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        //Save Photo (3 Size)
        $file = $request->file('PHOTO');
        $PHOTO_SMALL = Image::make($file->getRealPath());
        $PHOTO_MEDIUM = Image::make($file->getRealPath());
        $PHOTO_LARGE = Image::make($file->getRealPath());

        $PHOTO_SMALL->resize(null, 300, function ($constraint) {
            $constraint->aspectRatio();
        });
        $PHOTO_MEDIUM->resize(null, 500, function ($constraint) {
            $constraint->aspectRatio();
        });

        $PHOTO_LARGE->resize(null, 700, function ($constraint) {
            $constraint->aspectRatio();
        });

        $extension = $file->getClientOriginalExtension();
        $filename = 'thumb_small_' . time() . '.' . $extension;
        $PHOTO_SMALL->save(public_path('image_upload/' . $filename), 80);
        $storeData['PHOTO_SMALL'] = url('') . '/image_upload/' . $filename;

        $filename = 'thumb_medium_' . time() . '.' . $extension;
        $PHOTO_MEDIUM->save(public_path('image_upload/' . $filename), 80);
        $storeData['PHOTO_MEDIUM'] = url('') . '/image_upload/' . $filename;

    
        $filename = 'thumb_large_' . time() . '.' . $extension;
        $PHOTO_LARGE->save(public_path('image_upload/' . $filename), 80);
        $storeData['PHOTO_LARGE'] = url('') . '/image_upload/' . $filename;

        $catalogue = Catalogue::create($storeData); //menambah data katalog baru
        return response([
            'message' => 'Add katalog Success',
            'data' => $catalogue,
        ], 200); //return data katalog baru dalam bentuk json
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
            'ID_PERIOD' => 'required',
            'NAME_CATALOGUE' => 'required',
            'DESCRIPTION' => 'required',
            'ID_CTG_TYPE' => 'required',
            'POINT_REQ' => 'required',
            'STOCK' => 'required',
            'DESTINATION' => 'required',
            'PHOTO' => '',
        ]); //validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        //Delete Photo
        $deletePhoto = array('PHOTO_SMALL', 'PHOTO_MEDIUM', 'PHOTO_LARGE');
        foreach ($deletePhoto as $photo) {
            $imagePath = public_path('image_upload/' . $data[$photo]);
            File::delete($imagePath);
        }

        if (!empty($updateData['PHOTO'])) {
            //Save Photo (3 Size)
            $file = $request->file('PHOTO');
            $PHOTO_SMALL = Image::make($file->getRealPath());
            $PHOTO_MEDIUM = Image::make($file->getRealPath());
            $PHOTO_LARGE = Image::make($file->getRealPath());

            $PHOTO_SMALL->resize(null, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
            $PHOTO_MEDIUM->resize(null, 500, function ($constraint) {
                $constraint->aspectRatio();
            });

            $PHOTO_LARGE->resize(null, 700, function ($constraint) {
                $constraint->aspectRatio();
            });

            $extension = $file->getClientOriginalExtension();
            $filename = 'thumb_small_' . time() . '.' . $extension;
            $PHOTO_SMALL->save(public_path('image_upload/' . $filename), 80);
            $updateData['PHOTO_SMALL'] = url('') . '/image_upload/' . $filename;

            $filename = 'thumb_medium_' . time() . '.' . $extension;
            $PHOTO_MEDIUM->save(public_path('image_upload/' . $filename), 80);
            $updateData['PHOTO_MEDIUM'] = url('') . '/image_upload/' . $filename;

            $filename = 'thumb_large_' . time() . '.' . $extension;
            $PHOTO_LARGE->save(public_path('image_upload/' . $filename), 80);
            $updateData['PHOTO_LARGE'] = url('') . '/image_upload/' . $filename;

            $data->PHOTO_SMALL = $updateData['PHOTO_SMALL'];
            $data->PHOTO_MEDIUM = $updateData['PHOTO_MEDIUM'];
            $data->PHOTO_LARGE = $updateData['PHOTO_LARGE'];
        }



        $data->ID_PERIOD = $updateData['ID_PERIOD'];
        $data->NAME_CATALOGUE = $updateData['NAME_CATALOGUE'];
        $data->DESCRIPTION = $updateData['DESCRIPTION'];
        $data->ID_CTG_TYPE = $updateData['ID_CTG_TYPE'];
        $data->POINT_REQ = $updateData['POINT_REQ'];
        $data->STOCK = $updateData['STOCK'];
        $data->DESTINATION = $updateData['DESTINATION'];

        if ($data->save()) {
            return response([
                'message' => 'Update Catalogue Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update Catalogue Failed',
            'data' => $data
        ], 400);  //return message saat Catalogue gagal diedit
    }

    //Fungsi Delete berdasarkan ID
    public function destroy($id)
    {
        $catalog = Catalogue::find($id); //mencari data catalog berdasarkan id

        $checkredeem = DB::table('redeem_log AS rl')
        ->join('catalogue AS c', 'c.ID_CATALOGUE', '=', 'rl.ID_CATALOGUE')
        ->where('rl.ID_CATALOGUE', '=', $id)
        ->get();

        if (is_null($catalog)) {
            return response([
                'message' => 'Catalogue Not Found',
                'data' => null
            ], 404);
        } //return message saat data Catalogue tidak ditemukan

        if(!$checkredeem->isEmpty()){
            return response([
                'message' => 'Masih Terdapat data Redeem, Catalogue tidak bisa dihapus',
                'data' => $checkredeem
            ],404);
        } //return message jika ID katalog masih terpakai di data redeem

        if($catalog->delete()){
            return response([
                'message' => 'Delete Catalogue Success',
                'data' => $catalog,
            ], 200);
        } //return message saat berhasil menghapus data Catalogue
        return response([
            'message' => 'Delete Catalogue Failed',
            'data' => null,
        ], 400); //return message saat gagal menghapus data Catalogue
    }
}
