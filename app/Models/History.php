<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class History extends Model
{
    /*  
        Model untuk tabel history.  
        Isi tabel: Data riwayat event/tantangan yang telah dilakukan oleh pengguna.
        Digunakan pada controller: UserChallengeHistoryController, User/EventController, User/HistoryController
    */
    use HasFactory;
    protected $primaryKey = 'ID_HISTORY';
    protected $table = 'history';
    public $timestamps = false;
    protected $fillable = ['ID_EVENTS', 'ID_USERS', 'DATE_HISTORY'];

    public function getHistoryUser($userId)
    {
        /*Mendapatkan data riwayat event berdasarkan ID User. */
        return DB::table('history_detail AS hd')
                ->join('history AS h', 'hd.ID_HISTORY', '=', 'h.ID_HISTORY')
                ->join('event_detail AS ed', 'hd.ID_EVENT_DETAIL', '=', 'ed.ID_EVENT_DETAIL') 
                ->join('events AS e', 'h.ID_EVENTS' , '=', 'e.ID_EVENTS')
                ->select('h.ID_HISTORY', 'e.ID_EVENTS', 'e.NAME_EVENTS', 'h.DATE_HISTORY', 
                'e.PHOTO_SMALL', 'e.PHOTO_MEDIUM', 'e.PHOTO_LARGE', 'hd.POINT_REACHED') 
                ->where('h.ID_USERS', '=', $userId)
                ->get();
    }

    public function insertHistory($historyData)
    {   
        /* Menambah data riwayat event. */
        $result = History::create($historyData);
        return $result;
    }


    public function getHistoryByEvent($id)
    {
        /* Mendapatkan data riwayat event berdasarkan ID Event tertentu. */
        return History::where('ID_EVENTS', $id)->get()->toArray();
    }

    public function getAllHistory()
    {
        /* Mendapatkan semua data riwayat event. */
        return DB::table('history AS h')
                ->join('events AS e', 'h.ID_EVENTS' , '=', 'e.ID_EVENTS')
                ->get();
    }
}
