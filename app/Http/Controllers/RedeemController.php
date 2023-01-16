<?php

namespace App\Http\Controllers;

use App\Models\Redeem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class RedeemController extends Controller
{
    /*
        Berisi fungsi untuk mengelola data redeem.
        API dipanggil di views RedeemManager.vue
    */
    public function getAllRedeem()
    {
        //Data Redeem Mahasiswa
        $redeem1 = DB::table('redeem_log AS r')
        ->join('users AS u', 'u.ID_USERS', '=', 'r.ID_USERS')
        ->join('catalogue AS c', 'c.ID_CATALOGUE', '=', 'r.ID_CATALOGUE')
        ->join('redeem_status AS rs', 'rs.ID_REDEEM_STATUS', '=', 'r.ID_REDEEM_STATUS')
        ->select('r.ID_REDEEM_LOG', 'r.ID_USERS', 'u.NAME', 'r.ID_CATALOGUE', 'c.NAME_CATALOGUE', 'r.REDEEM_TIME',
        'r.REDEEM_FINISHED_TIME', 'rs.REDEEM_STATUS', 'r.REDEEM_KEY', 'c.ID_PERIOD')
        ->where('u.TYPE', '=', 'student')
        ->get();

        //Data Redeem Dosen
        $redeem2 = DB::table('redeem_log AS r')
        ->join('users AS u', 'u.ID_USERS', '=', 'r.ID_USERS')
        ->join('catalogue AS c', 'c.ID_CATALOGUE', '=', 'r.ID_CATALOGUE')
        ->join('redeem_status AS rs', 'rs.ID_REDEEM_STATUS', '=', 'r.ID_REDEEM_STATUS')
        ->select('r.ID_REDEEM_LOG', 'r.ID_USERS', 'u.NAME', 'r.ID_CATALOGUE', 'c.NAME_CATALOGUE', 'r.REDEEM_TIME',
        'r.REDEEM_FINISHED_TIME', 'rs.REDEEM_STATUS', 'r.REDEEM_KEY', 'c.ID_PERIOD')
        ->where('u.TYPE', '=', 'teacher')
        ->get();

        //Data Tahun Periode
        $yearperiode = DB::table('year_period')
        ->selectRaw("CONCAT(DATE(YEAR_START), ' / ', DATE(YEAR_FINISH)) AS year_period")
        ->get();

        //Data Max Redeem (Jumlah maksimal user dapat menukar poin dalam satu tahun ajaran)
        $maxredeem = DB::table('helper')
        ->select('VALUE')
        ->where('NAME', '=', 'MAX_REDEEM')
        ->get()
        ->first()->VALUE;

        if (count($redeem1) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'datamhs' => $redeem1,
                'datadosen' => $redeem2,
                'yearperiod' => $yearperiode,
                'maxredeem' => $maxredeem,
            ], 200);
        }

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404);
    }

    //method untuk mengubah redeem status
    public function update(Request $request, $id){
        $redeem = Redeem::find($id); //mencari data redeem berdasarkan id
        if(is_null($redeem)){
            return response([
                'message' => 'redeem Not Found',
                'data' => null
            ],404);
        } //return message saat data redeem tidak ditemukan

        $updateData = $request->all(); //mengambil semua input dari api client
        $validate = Validator::make($updateData, [
            'redeemstatus' => 'required'
        ]); //membuat rule validasi input

        if($validate->fails())
            return response(['message' => $validate->errors()],400); //return error invalid input
        
        $id_redeemstatus = DB::table('redeem_status')
        ->select('ID_REDEEM_STATUS')
        ->where('REDEEM_STATUS', $updateData['redeemstatus'])
        ->get()
        ->first()->ID_REDEEM_STATUS; //Convert data dari select DB dari array ke int

        $redeemupdate = DB::table('redeem_log')
        ->where('ID_REDEEM_LOG', $id)
        ->update(['ID_REDEEM_STATUS' => $id_redeemstatus]);

        $timeupdate = DB::table('redeem_log')
        ->where('ID_REDEEM_LOG', $id)
        ->update(['REDEEM_FINISHED_TIME' => Carbon::now(+7)->toDateTimeString()]);

        if($redeem->save()){
            return response([
                'message' => 'Update status Success',
                'data' => $redeem,
            ],200);
        } //return data redeem yang telah di edit dalam bentuk json
        return response([
            'message' => 'Update status Failed',
            'data' => null,
        ],400); //return message saat redeem gagal di edit
    }

    public function maxredeem(Request $request){
         /** 
         * Fungsi: Mengubah jumlah maksimal redeem dalam satu tahun ajaran.
         * Param:
         *      - request  -> data input yang dikirim dari front-end.
        */
        $updateData = $request->all(); //mengambil semua input dari api client
        
        $redeemupdate = DB::table('helper')
        ->where('NAME', '=', 'MAX_REDEEM')
        ->update(['VALUE' => $updateData['value_max']]);

        return response([
            'message' => 'Update Max Redeem Success',
            'data' => $redeemupdate,
        ],200);
    }
}
