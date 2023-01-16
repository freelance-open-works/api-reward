<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\ElearningChallenge;
use Illuminate\Http\Request;
use Validator; //import library untuk validasi
use Illuminate\Support\Facades\DB;

class ElearningHistoryController extends Controller
{
    /*
        Berisi fungsi untuk menampilkan e-learning history. 
        E-learning history adalah riwayat log kuliah yang diambil dari data log moodle, tetapi sudah disesuaikan dengan e-learning challenge.
        API dipanggil di views HistoryElearn.vue
    */
    public function getDetailHistoryElearn($id)
    {
        /*
            Fungsi: Mendapatkan data e-learning history berdasarkan ID e-learning history tertentu.
            Params: 
                -id             -> integer -> ID E-learning history
            Return: 
                - message       -> String -> Pesan pemanggilan API
                - data          -> Object -> Data event moodle
        */
        $elearn = DB::table('elearning_history AS eh')
            ->join('elearning_challenge AS ec', 'ec.ID_ELEARNING_CHALLENGE', '=', 'eh.ID_ELEARNING_CHALLENGE')
            ->join('users AS u', 'u.ID_USERS', '=', 'eh.ID_USERS')
            ->select(
                'eh.ID_ELEARNING_HISTORY',
                'eh.ID_USERS',
                'u.NAME AS USERNAME',
                'eh.ID_ELEARNING_CHALLENGE',
                'ec.NAME',
                'eh.DATE_HISTORY',
                'ec.DESTINATION',
                'eh.TYPE',
                'ec.EVENTNAME_CODE',
                'ec.POINT',
                'ec.MAX_COUNT',
                'eh.INFO'
            )
            ->where('eh.ID_ELEARNING_HISTORY', '=', $id)
            ->get()
            ->first();

        if (!is_null($elearn)) {
            return response([
                'message' => 'Retrieve user Success',
                'data' => $elearn
            ], 200);
        } //return data history yang ditemukan dalam bentuk json

        return response([
            'message' => 'user Not Found',
            'data' => null
        ], 404); //return message saat data history tidak ditemukan
    }

    public function index(Request $request)
    {
        /////Mencoba pagination (not used)//////
        $rowsPerPage = ($request->rowsPerPage);

        // $data = DB::table('elearning_history AS eh')
        //     ->join('elearning_challenge AS ec', 'ec.ID_ELEARNING_CHALLENGE', '=', 'eh.ID_ELEARNING_CHALLENGE')
        //     ->join('users AS u', 'u.ID_USERS', '=', 'eh.ID_USERS')
        //     ->select(
        //         'eh.ID_ELEARNING_HISTORY',
        //         'eh.ID_USERS',
        //         'u.NAME',
        //         'eh.ID_ELEARNING_CHALLENGE',
        //         'eh.ID_LOG_KULIAH',
        //         'eh.IP_ADDRESS',
        //         'eh.DATE_HISTORY',
        //         'eh.TYPE'
        //     )
        //     ->orderBy('eh.ID_ELEARNING_HISTORY', 'desc')
        //     ->paginate($rowsPerPage);
        /////////////////////////////////////////
        
        $data = DB::select("
                SELECT eh1.*, u.NAME, ec.ID_PERIOD
                FROM elearning_history eh1
                JOIN users u ON u.ID_USERS = eh1.ID_USERS
                JOIN elearning_challenge ec ON  ec.ID_ELEARNING_CHALLENGE = eh1.ID_ELEARNING_CHALLENGE
                JOIN (
                    SELECT ID_ELEARNING_HISTORY
                    FROM elearning_history
                    ORDER BY DATE_HISTORY DESC
                    LIMIT 6000
                ) AS eh2
                ON eh1.ID_ELEARNING_HISTORY = eh2.ID_ELEARNING_HISTORY
                ");


        if (count($data) > 0) {
            return response([
                'message' => 'Retrieve All Success',
                'data' => $data,
            ], 200);
        } //return data semua elearninghistory dalam bentuk json

        return response([
            'message' => 'Empty',
            'data' => null,
        ], 404); //return message data elearninghistory kosong
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $data = DB::table('elearning_history as eh')
            ->join('users AS u', 'u.ID_USERS', '=', 'eh.ID_USERS')
            ->select(
                'eh.ID_ELEARNING_HISTORY',
                'eh.ID_USERS',
                'u.NAME',
                'eh.ID_ELEARNING_CHALLENGE',
                'eh.ID_LOG_KULIAH',
                'eh.IP_ADDRESS',
                'eh.DATE_HISTORY',
                'eh.TYPE'
            )
            ->where('eh.ID_ELEARNING_HISTORY', 'LIKE', '%' . $search . '%')
            ->orWhere('eh.ID_USERS', 'LIKE', '%' . $search . '%')
            ->orWhere('u.NAME', 'LIKE', '%' . $search . '%')
            ->orWhere('eh.ID_ELEARNING_CHALLENGE', 'LIKE', '%' . $search . '%')
            ->orWhere('eh.ID_LOG_KULIAH', 'LIKE', '%' . $search . '%')
            ->orWhere('eh.IP_ADDRESS', 'LIKE', '%' . $search . '%')
            ->orWhere('eh.DATE_HISTORY', 'LIKE', '%' . $search . '%')
            ->orWhere('eh.TYPE', 'LIKE', '%' . $search . '%')
            ->orderBy('eh.ID_ELEARNING_HISTORY', 'desc')
            ->paginate();


        return response([
            'message' => 'Retrieve All Success',
            'pagination' => $data
        ]);
    }
}
