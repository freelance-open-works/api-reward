<?php

namespace App\Http\Controllers;

use App\Models\Challenge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManagerStatic as Image;
use Illuminate\Support\Facades\File;

class ChallengeController extends Controller
{
    /*
        Berisi fungsi untuk: mengolah data challenge event.
        API dipanggil di views: ChallengeManager.vue
    */
    public function getAllEvent()
    {
        /*
        Menggunakan metode subquery untuk menyambungkan tabel events dengan tabel detail event
        yang memiliki dua kolom unik yaitu poin dan info
        */
        $query = DB::table("events")
        ->select("events.*",
                DB::raw("(SELECT POINT FROM event_detail
                              WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 1
                              GROUP BY event_detail.ID_EVENTS) as PointQR"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 1
                            GROUP BY event_detail.ID_EVENTS) as INFO_QR"),
                DB::raw("(SELECT POINT FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 2
                            GROUP BY event_detail.ID_EVENTS) as PointFoto"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 2
                            GROUP BY event_detail.ID_EVENTS) as INFO_PHOTO"),
                DB::raw("(SELECT POINT FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 3
                            GROUP BY event_detail.ID_EVENTS) as PointTW"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 3
                            GROUP BY event_detail.ID_EVENTS) as INFO_TW"),
                DB::raw("(SELECT POINT FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 4
                            GROUP BY event_detail.ID_EVENTS) as PointFB"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 4
                            GROUP BY event_detail.ID_EVENTS) as INFO_FB"),
                DB::raw("(SELECT POINT FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 5
                            GROUP BY event_detail.ID_EVENTS) as PointIG"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 5
                            GROUP BY event_detail.ID_EVENTS) as INFO_IG"))
        ->get();

        $yearperiode = DB::table('year_period')
        ->select('ID_PERIOD')
        ->selectRaw("CONCAT(DATE(YEAR_START), ' / ', DATE(YEAR_FINISH)) AS year_period")
        ->get();

        $chall_type = DB::table('event_type')
        ->select('ID_EVENT_TYPE', 'EVENT_TYPE')
        ->get();

        if (count($query) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $query,
                'yearperiod' => $yearperiode,
                'chall_type' => $chall_type,
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
            'ID_PERIOD' => 'required',
            'ID_EVENT_TYPE' => 'required',
            'NAME_EVENTS' => 'required',
            'DESC_EVENTS' => 'required',
            'DATE_START' => 'required',
            'DATE_FINISH' => 'required',
            'DESTINATION' => 'required',
            'PHOTO' => 'image',
            'PointQR' => '',
            'INFO_QR' => '',
            'PointPhoto' => '',
            'INFO_PHOTO' => '',
            'PointTW' => '',
            'INFO_TW' => '',
            'PointFB' => '',
            'INFO_FB' => '',
            'PointIG' => '',
            'INFO_IG' => '',

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

        // $extension = $PHOTO_MEDIUM->getClientOriginalExtension();
        $filename = 'thumb_medium_' . time() . '.' . $extension;
        $PHOTO_MEDIUM->save(public_path('image_upload/' . $filename), 80);
        $storeData['PHOTO_MEDIUM'] = url('') . '/image_upload/' . $filename;

        // $extension = $PHOTO_LARGE->getClientOriginalExtension();
        $filename = 'thumb_large_' . time() . '.' . $extension;
        $PHOTO_LARGE->save(public_path('image_upload/' . $filename), 80);
        $storeData['PHOTO_LARGE'] = url('') . '/image_upload/' . $filename;
        // $storeData['gambar'] = "http://192.168.0.114:8000/" . $filename;
        // error_log($storeData['gambar']);

        $event = Challenge::create($storeData); //menambah data katalog baru
        DB::table('event_detail')->insert([
            'ID_EVENT_ROLE' => 1,
            'ID_EVENTS' => $event['ID_EVENTS'],
            'POINT' => $storeData['PointQR'],
            'ROLE_INFO' => $storeData['INFO_QR']
        ]);

        DB::table('event_detail')->insert([
            'ID_EVENT_ROLE' => 2,
            'ID_EVENTS' => $event['ID_EVENTS'],
            'POINT' => $storeData['PointPhoto'],
            'ROLE_INFO' => $storeData['INFO_PHOTO']
        ]);

        DB::table('event_detail')->insert([
            'ID_EVENT_ROLE' => 3,
            'ID_EVENTS' => $event['ID_EVENTS'],
            'POINT' => $storeData['PointTW'],
            'ROLE_INFO' => $storeData['INFO_TW']
        ]);

        DB::table('event_detail')->insert([
            'ID_EVENT_ROLE' => 4,
            'ID_EVENTS' => $event['ID_EVENTS'],
            'POINT' => $storeData['PointFB'],
            'ROLE_INFO' => $storeData['INFO_FB']
        ]);

        DB::table('event_detail')->insert([
            'ID_EVENT_ROLE' => 5,
            'ID_EVENTS' => $event['ID_EVENTS'],
            'POINT' => $storeData['PointIG'],
            'ROLE_INFO' => $storeData['INFO_IG']
        ]);

        return response([
            'message' => 'Add event Success',
            'data' => $event,
        ],200); //return data katalog baru dalam bentuk json
    }

    public function getDetailChallenge($id){
        $query = DB::table("events")
        ->select("events.*",
                DB::raw("(SELECT POINT FROM event_detail
                              WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 1
                              GROUP BY event_detail.ID_EVENTS) as PointQR"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 1
                            GROUP BY event_detail.ID_EVENTS) as INFO_QR"),
                DB::raw("(SELECT POINT FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 2
                            GROUP BY event_detail.ID_EVENTS) as PointFoto"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 2
                            GROUP BY event_detail.ID_EVENTS) as INFO_PHOTO"),
                DB::raw("(SELECT POINT FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 3
                            GROUP BY event_detail.ID_EVENTS) as PointTW"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 3
                            GROUP BY event_detail.ID_EVENTS) as INFO_TW"),
                DB::raw("(SELECT POINT FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 4
                            GROUP BY event_detail.ID_EVENTS) as PointFB"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 4
                            GROUP BY event_detail.ID_EVENTS) as INFO_FB"),
                DB::raw("(SELECT POINT FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 5
                            GROUP BY event_detail.ID_EVENTS) as PointIG"),
                DB::raw("(SELECT ROLE_INFO FROM event_detail
                            WHERE event_detail.ID_EVENTS = events.ID_EVENTS AND event_detail.ID_EVENT_ROLE = 5
                            GROUP BY event_detail.ID_EVENTS) as INFO_IG"))
        ->where('ID_EVENTS', '=', $id)
        ->get()
        ->first();

        if(!is_null($query)){
            return response([
                'message' => 'Retrieve challenge Success',
                'data' => $query
            ],200);
        } //return data catalogue yang ditemukan dalam bentuk json

        return response([
            'message' => 'challenge Not Found',
            'data' => null
        ],404); //return message saat data catalogue tidak ditemukan
    }

    public function update(Request $request, $id)
    {
        $data = Challenge::find($id); //mencari data Challenge berdasar id
        if (is_null($data)) {
            return response([
                'message' => 'Challenge Not Found',
                'data' => null
            ], 404);
        } //return message saat data tidak ditemukan

        $updateData = $request->all(); //abil semua input dari api client
        $validate = Validator::make($updateData, [
            'ID_PERIOD' => 'required',
            'ID_EVENT_TYPE' => 'required',
            'NAME_EVENTS' => 'required',
            'DESC_EVENTS' => 'required',
            'DATE_START' => 'required',
            'DATE_FINISH' => 'required',
            'DESTINATION' => 'required',
            'PHOTO' => '',
            'PointQR' => '',
            'INFO_QR' => '',
            'PointPhoto' => '',
            'INFO_PHOTO' => '',
            'PointTW' => '',
            'INFO_TW' => '',
            'PointFB' => '',
            'INFO_FB' => '',
            'PointIG' => '',
            'INFO_IG' => '',
        ]); //validasi input

        if ($validate->fails())
            return response(['message' => $validate->errors()], 400); //return error invalid input

         //Delete Photo
         $deletePhoto = array('PHOTO_SMALL','PHOTO_MEDIUM','PHOTO_LARGE');
         foreach ($deletePhoto as $photo) {
             $imagePath=public_path('image_upload/'.$data[$photo]);
             File::delete($imagePath);
         }

         if (!empty($updateData['PHOTO'])){
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

            // $extension = $PHOTO_MEDIUM->getClientOriginalExtension();
            $filename = 'thumb_medium_' . time() . '.' . $extension;
            $PHOTO_MEDIUM->save(public_path('image_upload/' . $filename), 80);
            $updateData['PHOTO_MEDIUM'] = url('') . '/image_upload/' . $filename;

            // $extension = $PHOTO_LARGE->getClientOriginalExtension();
            $filename = 'thumb_large_' . time() . '.' . $extension;
            $PHOTO_LARGE->save(public_path('image_upload/' . $filename), 80);
            $updateData['PHOTO_LARGE'] = url('') . '/image_upload/' . $filename;
            // $storeData['gambar'] = "http://192.168.0.114:8000/" . $filename;
            // error_log($storeData['gambar']);

            $data->PHOTO_SMALL = $updateData['PHOTO_SMALL'];
            $data->PHOTO_MEDIUM = $updateData['PHOTO_MEDIUM'];
            $data->PHOTO_LARGE = $updateData['PHOTO_LARGE'];
         }

        $data->ID_PERIOD = $updateData['ID_PERIOD'];
        $data->ID_EVENT_TYPE = $updateData['ID_EVENT_TYPE'];
        $data->NAME_EVENTS = $updateData['NAME_EVENTS'];
        $data->DESC_EVENTS = $updateData['DESC_EVENTS'];
        $data->DATE_START = $updateData['DATE_START'];
        $data->DATE_FINISH = $updateData['DATE_FINISH'];
        $data->DESTINATION = $updateData['DESTINATION'];

            DB::table('event_detail')
            ->where('ID_EVENTS', '=', $id)
            ->where('ID_EVENT_ROLE', '=', 1)
            ->update(['POINT' => $updateData['PointQR'],
            'ROLE_INFO' => $updateData['INFO_QR']]);

            DB::table('event_detail')
            ->where('ID_EVENTS', '=', $id)
            ->where('ID_EVENT_ROLE', '=', 2)
            ->update(['POINT' => $updateData['PointPhoto'],
            'ROLE_INFO' => $updateData['INFO_PHOTO']]);

            DB::table('event_detail')
            ->where('ID_EVENTS', '=', $id)
            ->where('ID_EVENT_ROLE', '=', 3)
            ->update(['POINT' => $updateData['PointTW'],
            'ROLE_INFO' => $updateData['INFO_TW']]);

            DB::table('event_detail')
            ->where('ID_EVENTS', '=', $id)
            ->where('ID_EVENT_ROLE', '=', 4)
            ->update(['POINT' => $updateData['PointFB'],
            'ROLE_INFO' => $updateData['INFO_FB']]);

            DB::table('event_detail')
            ->where('ID_EVENTS', '=', $id)
            ->where('ID_EVENT_ROLE', '=', 5)
            ->update(['POINT' => $updateData['PointIG'],
            'ROLE_INFO' => $updateData['INFO_IG']]);

        if ($data->save()) {
            return response([
                'message' => 'Update Challenge Success',
                'data' => $data
            ], 200);
        } //return data yang telah diedit dalam bentuk json
    }
    //Fungsi Delete berdasarkan ID
    public function destroy($id)
    {
        $event = Challenge::find($id); //mencari data challenge berdasarkan id

        if(is_null($event)){
            return response([
                'message' => 'Challenge Not Found',
                'data' => null
            ],404);
        } //return message saat data Challenge tidak ditemukan

        if($event->delete()){
            return response([
                'message' => 'Delete Challenge Success',
                'data' => $event,
            ],200);
        } //return message saat berhasil menghapus data Challenge
        return response([
            'message' => 'Delete Challenge Failed',
            'data' => null,
        ],400); //return message saat gagal menghapus data Challenge
    }
}
