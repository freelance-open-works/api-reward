<?php

namespace App\Http\Controllers;

use App\Models\History;
use App\Models\HistoryDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserChallengeHistoryController extends Controller
{
    //04/08/2021
    /*
        Berisi fungsi untuk melihat riwayat keikutsertaan user pada suatu challenge.
        API dipanggil di views UserChallengeHistory.vue
        TODO_DOC: get detail history?
    */

    public function getAllHistory() {
        $history = new History();
        $data = $history->getAllHistory(); 

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

    public function getDetailHistory($id) {
        $history = new HistoryDetail();
        $data = $history->getDetailHistoryUser_admin($id); 

        if(!is_null($data)){
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
    
}
