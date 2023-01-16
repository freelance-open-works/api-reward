<?php

namespace App\Http\Controllers;

use App\Models\Amtanesia;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;

class NewsController extends Controller
{
    /*
        Berisi fungsi untuk mengelola berita.
        API dipanggil di views NewsManager.vue
    */

    public function index()
    {
        $news = new News();
        $data = $news->getAllNews(); //dari Model News. Mendapatkan semua data berita.

        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
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
            'NEWS_TITLE' => 'required',
            'NEWS_DESCRIPTION' => 'required',
            'PHOTO' => 'required|image',
            'DATE' => 'required',
            'ID_PERIOD' => 'required',
        ]); //membuat rule validasi input


        if ($validate->fails()) {
            return response(['message' => $validate->errors()], 400); //return error invalid input
        }

        //Save Photo (3 Size)
        $file = $request->file('PHOTO');
        $photoData = $this->uploadPhoto($file);
        $photoSizes = array('PHOTO_SMALL', 'PHOTO_MEDIUM', 'PHOTO_LARGE');
        foreach ($photoSizes as $photo) {
            $storeData[$photo] = $photoData[$photo];
        }

        $data = News::create($storeData);

        //Send notification
        if ($storeData['NOTIFICATION'] == "true") {
            $this->sendNotification($request);
        }

        return response([
            'message' => 'Add News Success',
            'data' => $data,
            'notif' => $storeData['NOTIFICATION'],
        ], 200); //return message data product tidak ditemukan
    }


    public function update(Request $request, $id)
    {
        $data = News::find($id); //mencari data product berdasar id
        if (is_null($data)) {
            return response([
                'message' => 'News Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //abil semua input dari api client
        $validate = Validator::make($updateData, [
            'NEWS_TITLE' => 'required',
            'NEWS_DESCRIPTION' => 'required',
            'PHOTO' => 'sometimes',
        ]); //validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

        //Apabila ada file gambar
        if ($request->file('PHOTO')) {
            $this->deletePhoto($data);
            $file = $request->file('PHOTO');
            $photoData = $this->uploadPhoto($file);
            $data->PHOTO_SMALL = $photoData['PHOTO_SMALL'];
            $data->PHOTO_MEDIUM = $photoData['PHOTO_MEDIUM'];
            $data->PHOTO_LARGE = $photoData['PHOTO_LARGE'];
        }

        $data->NEWS_TITLE = $updateData['NEWS_TITLE'];
        $data->NEWS_DESCRIPTION = $updateData['NEWS_DESCRIPTION'];

        if ($data->save()) {
            return response([
                'message' => 'Update News Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Update News Failed',
            'data' => $data
        ], 400);  //return message saat produk gagal diedit
    }


    public function destroy($id)
    {
        $data = News::find($id); //mencari data berdsaar id

        $this->deletePhoto($data);

        if (is_null($data)) {
            return response([
                'message' => 'News Not Found',
                'data' => null
            ], 404); //return message data product tidak ditemukan
        }

        if ($data->delete()) {
            return response([
                'message' => 'Delete News Success',
                'data' => $data
            ], 200); //return message data product berhasil dihapus
        }

        return response([
            'message' => 'Delete News Failed',
            'data' => null
        ], 400); //return message data product gagal dihapus
    }

    //Save Photo (3 Size)
    public function uploadPhoto($file)
    {
        /*
            Fungsi: Mendapatkan mengubah ukuran foto menjadi tiga ukuran berbeda (small, medium, large).
            Param: 
                - file          -> Data gambar yang akan diubah ukuran fotonya
            Return: 
                - updateData    -> Array -> berisi data gambar yang sudah diubah ukurannya
        */
        $PHOTO_SMALL = Image::make($file->getRealPath());
        $PHOTO_MEDIUM = Image::make($file->getRealPath());
        $PHOTO_LARGE = Image::make($file->getRealPath());

        $photoData = array('PHOTO_SMALL', 'PHOTO_MEDIUM', 'PHOTO_LARGE');
        $nameSizes = array('thumb_small_', 'thumb_medium_', 'thumb_large_');
        $sizes = array(300, 400, 500);

        for ($i = 0; $i <= 2; $i++) {
            ${$photoData[$i]}->resize(null, $sizes[$i], function ($constraint) {
                $constraint->aspectRatio();
            });

            $extension = $file->getClientOriginalExtension();
            $filename = $nameSizes[$i] . time() . '.' . $extension;
            ${$photoData[$i]}->save(public_path('image_upload/' . $filename), 80);
            $updateData[$photoData[$i]] = 'api_amtareward/public/image_upload/' . $filename;
        }
        return $updateData;
    }

    public function deletePhoto($data)
    {

        /*
            Fungsi: Menghapus gambar ketika gambar sudah tidak diperlukan. Contohnya, data berita dihapus.
            Param: 
                - data          -> Data gambar yang akan dihapus
        */
        $deletePhoto = array('PHOTO_SMALL', 'PHOTO_MEDIUM', 'PHOTO_LARGE');
        foreach ($deletePhoto as $photo) {
            $imagePath = public_path('image_upload/' . $data[$photo]);
            File::delete($imagePath);
        }
    }

    public function sendNotification(Request $request)
    {
        /*
            Fungsi: Mengirim notifikasi Firebase.
            Param: 
                - request          -> Berisi data input dari formulir di views.
        */
        $storeData = $request->all(); //mengambil semua input dari api client
        $amtanesia = new Amtanesia();
        //$topic ="ePm8E4S2TIyiZ01B-9DAdG:APA91bHbw4IkkmyaFtKgamlVBGBsiajvnzmIukKtq-RkATAiF76sM23VvD9AS6O-bZm4Jo6nIGIfB1gcdMnrhR7FbD0Lew1-ZcnyOK_imdD3wDxoGfF-oEOGmqgRIxm9bzaKnDE2cmC2";
        //TODO: ganti topics jadi NEWS lagi kalo sudah fix.
        $topic = "/topics/NEWS";
        if ($storeData['MESSAGE'] != null)
            $notif = $amtanesia->sendNotification($topic, $storeData['NEWS_TITLE'], $storeData['MESSAGE']);
        else
            $notif = $amtanesia->sendNotification($topic, $storeData['NEWS_TITLE'], $storeData['NEWS_DESCRIPTION']);

        if ($notif) {
            return response([
                'message' => 'Send notification success!',
            ], 200);
        } //return data yang telah diedit dalam bentuk json


        return response([
            'message' => 'Send notification failed!',
        ], 400);  //return message saat produk gagal diedit

    }
}
