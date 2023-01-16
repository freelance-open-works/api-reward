<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class HistoryDetail extends Model
{
    /*  
        Model untuk tabel history_detail.  
        Isi tabel: Data detail riwayat event yang dilakukan oleh pengguna.
        Digunakan pada controller: UserChallengeHistoryController, User/EventController
    */
    use HasFactory;
    protected $table = 'history_detail';
    public $timestamps = false;
    protected $fillable = ['ID_HISTORY', 'ID_EVENT_DETAIL', 'POINT_REACHED', 'HISTORY_DETAIL_INFO'];

    public function getDetailHistoryUser($historyId)
    {
        /* Mendapatkan data detail riwayat event berdasarkan ID riwayat tanpa data user (nama dan NPM).  */
        return DB::table('history_detail AS hd')
                ->join('history AS h', 'hd.ID_HISTORY', '=', 'h.ID_HISTORY')
                ->join('event_detail AS ed', 'hd.ID_EVENT_DETAIL', '=', 'ed.ID_EVENT_DETAIL') 
                ->join('events AS e', 'h.ID_EVENTS' , '=', 'e.ID_EVENTS')
                ->join('event_role AS er', 'ed.ID_EVENT_ROLE' , '=', 'er.ID_EVENT_ROLE')
                ->select('h.ID_HISTORY', 'e.ID_EVENTS', 'e.NAME_EVENTS', 'e.DESC_EVENTS', 'e.DATE_START', 'e.DATE_FINISH', 'h.DATE_HISTORY', 'ed.ID_EVENT_ROLE', 
                        'er.NAME_EVENT_ROLE', 'ed.ROLE_INFO', 'ed.POINT', 'e.PHOTO_SMALL', 'e.PHOTO_MEDIUM', 'e.PHOTO_LARGE', 'hd.POINT_REACHED') 
                ->where('h.ID_HISTORY', '=', $historyId)
                ->get();
    }

    public function getDetailHistoryUser_admin($historyId)
    {
        /* Mendapatkan data detail riwayat event berdasarkan ID riwayat dengan data user (nama dan NPM).  */
        return DB::table('history_detail AS hd')
                ->join('history AS h', 'hd.ID_HISTORY', '=', 'h.ID_HISTORY')
                ->join('users AS u', 'h.ID_USERS', '=', 'u.ID_USERS') 
                ->join('event_detail AS ed', 'hd.ID_EVENT_DETAIL', '=', 'ed.ID_EVENT_DETAIL') 
                ->join('events AS e', 'h.ID_EVENTS' , '=', 'e.ID_EVENTS')
                ->join('event_role AS er', 'ed.ID_EVENT_ROLE' , '=', 'er.ID_EVENT_ROLE')
                ->join('event_type AS et', 'e.ID_EVENT_TYPE', '=', 'et.ID_EVENT_TYPE') 
                ->select('h.ID_HISTORY', 'u.ID_USERS', 'u.NAME', 'e.ID_EVENTS', 'e.NAME_EVENTS', 'et.EVENT_TYPE', 'e.DESTINATION', 
                        'h.DATE_HISTORY', 'er.NAME_EVENT_ROLE', 'hd.POINT_REACHED') 
                ->where('h.ID_HISTORY', '=', $historyId)
                ->first();
    }

    public function insertDetailHistory($data)
    {
        /* Menambah data riwayat event.  */
        if (HistoryDetail::create($data)) {
            return true;
        } else {
            return false;
        }
    }

}
