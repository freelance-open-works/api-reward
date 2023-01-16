<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;
use Validator; //import library untuk validasi
use App\Models\User; //import model user
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /*
        Berisi fungsi untuk melihat data user yang setidaknya pernah sekali login
        di aplikasi mobile amtareward.
        API dipanggil di views Users.vue
    */

    public function __construct()
    {
        $this->periode = new Periode();
        $this->user = new User();
    }

    public function index()
    {
        //Data Mahasiswa
        $datastudent = DB::table('users AS u')
            ->join('user_device AS ud', 'u.ID_USERS', '=', 'ud.ID_USERS')
            ->select(
                'u.ID_USERS',
                'u.NAME',
                'u.PRODI',
                'u.FAKULTAS',
                'u.PHOTO',
                'u.POINTS',
                'u.EMAIL',
                'u.TYPE',
                'ud.API_KEY'
            )
            ->where('u.TYPE', '=', 'student')
            ->get();
        //Data Dosen
        $datateacher = DB::table('users AS u')
            ->join('user_device AS ud', 'u.ID_USERS', '=', 'ud.ID_USERS')
            ->select(
                'u.ID_USERS',
                'u.NAME',
                'u.PRODI',
                'u.FAKULTAS',
                'u.PHOTO',
                'u.POINTS',
                'u.EMAIL',
                'u.TYPE',
                'ud.API_KEY'
            )
            ->where('u.TYPE', '=', 'teacher')
            ->get();

        if (count($datastudent) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'datamhs' => $datastudent,
                'datadosen' => $datateacher
            ], 200);
        } //return data semua user dalam bentuk json

        return response([
            'message' => 'Empty',
            'datamhs' => null,
        ], 404); //return message data user kosong
    }

    public function getLeaderboardByPeriode($periode) {
        $periode = $this->periode->getPeriodeById($periode);

        $start = $periode->YEAR_START;
        $end = $periode->YEAR_FINISH;

        return response([
            'message' => 'success',
            'student' => $this->user->getLeaderboardByPeriode($start, $end, 'student'),
            'teacher' => $this->user->getLeaderboardByPeriode($start, $end, 'teacher')
        ], 200);
    }

    //Method untuk mengambil detail 1 baris tabel
    public function getDetailUser($id)
    {
        /**
         * Fungsi: Mendapatkan data user berdasarkan ID User tertentu.
         * Param:
         *      - id -> ID User yang detailnya akan dicari
         * Return:
         *      - message -> String -> Pesan pemanggilan API
         *      - data     -> Object -> Data informasi user.
         */
        $user = DB::table('users AS u')
            ->join('user_device AS ud', 'u.ID_USERS', '=', 'ud.ID_USERS')
            ->select(
                'u.ID_USERS',
                'u.NAME',
                'u.PRODI',
                'u.FAKULTAS',
                'u.PHOTO',
                'u.POINTS',
                'u.EMAIL',
                'u.TYPE',
                'ud.API_KEY'
            )
            ->where('u.ID_USERS', '=', $id)
            ->get()
            ->first();

        if (!is_null($user)) {
            return response([
                'message' => 'Retrieve user Success',
                'data' => $user
            ], 200);
        } //return data user yang ditemukan dalam bentuk json

        return response([
            'message' => 'user Not Found',
            'data' => null
        ], 404); //return message saat data user tidak ditemukan
    }


    public function getAllIdUsers()
    {
        /**
         * Fungsi: Mendapatkan data semua ID User.
         * Return:
         *      - message -> String -> Pesan pemanggilan API
         *      - data     -> Object -> Data semua ID User.
         *      - active   -> Date  -> Data periode yang aktif
         */

        $user = new User();
        $id = $user->getIdUsers();
        //Menitip data periode aktif untuk formulir package
        $periode = new Periode();
        $active = $periode->getActiveYearPeriode();

        if (count($id) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $id,
                'active' => $active
            ], 200);
        } //return data semua user dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404); //return message data user kosong

    }

    //method untuk menghapus 1 data user (delete)
    public function destroy($id)
    {
        $user = DB::table('users')->where('ID_USERS', $id)->first();

        if (is_null($user)) {
            return response([
                'message' => 'user Not Found',
                'data' => null
            ], 404);
        } //return message saat data user tidak ditemukan

        if ($user->delete()) {
            return response([
                'message' => 'Delete User Success',
                'data' => $user,
            ], 200);
        } //return message saat berhasil menghapus data user
        return response([
            'message' => 'Delete User Failed',
            'data' => null,
        ], 400); //return message saat gagal menghapus data user
    }


}
