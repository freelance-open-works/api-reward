<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Event extends Model
{
     /*  
        TODO_DOC: gaada inisialisasi nama tabel??
        Model untuk tabel events.  
        Isi tabel: Data challenge/tantangan yang dapat dilakukan oleh pengguna untuk mendapat poin amtarewards.
    */
    use HasFactory;

    public function getEvents($eventType, $periode, $type){
        /* 
            Mendapatkan data events berdasarkan tipe event, tipe pengguna (teacher, student, all user), dan ID Periode tertentu. 
            Untuk admin, akan menampilkan data events dengan semua tipe pengguna.
            Digunakan pada controller: ChallengeController, User/EventController
        */
        if ($type == 'admin') {
            $sql = DB::table('event_detail AS ed')
            ->join('events AS e', 'ed.ID_EVENTS', '=', 'e.ID_EVENTS')
            ->join('event_role AS er', 'ed.ID_EVENT_ROLE', '=', 'er.ID_EVENT_ROLE')
            ->where('e.ID_EVENT_TYPE', $eventType)
            ->where('e.ID_PERIOD', $periode)
            ->select('ed.ID_EVENTS', 'ed.ID_EVENT_ROLE', 'e.NAME_EVENTS', 'e.DESC_EVENTS', 
                DB::raw('DATE_FORMAT(e.DATE_START, "%d-%b-%Y") as DATE_START'), DB::raw('DATE_FORMAT(e.DATE_FINISH, "%d-%b-%Y") as DATE_FINISH'), 'e.PHOTO_SMALL', 
                 'e.PHOTO_MEDIUM', 'e.PHOTO_LARGE', 'er.NAME_EVENT_ROLE', 'er.ROLE_DETAIL', 'ed.ROLE_INFO', 'ed.POINT')
            ->orderBy('e.DATE_START')->get();
        } else {
            $sql = DB::table('event_detail AS ed')
            ->join('events AS e', 'ed.ID_EVENTS', '=', 'e.ID_EVENTS')
            ->join('event_role AS er', 'ed.ID_EVENT_ROLE', '=', 'er.ID_EVENT_ROLE')
            ->where('e.ID_EVENT_TYPE', $eventType)
            ->where('e.ID_PERIOD', $periode)
            ->where(function ($query) use($type) {
                $query->where('e.DESTINATION', $type)->orWhere('e.DESTINATION', 'all user');
            })
            ->select('ed.ID_EVENTS', 'ed.ID_EVENT_ROLE', 'e.NAME_EVENTS', 'e.DESC_EVENTS', 
            DB::raw('DATE_FORMAT(e.DATE_START, "%d-%b-%Y") as DATE_START'), DB::raw('DATE_FORMAT(e.DATE_FINISH, "%d-%b-%Y") as DATE_FINISH'), 'e.PHOTO_SMALL', 
                 'e.PHOTO_MEDIUM', 'e.PHOTO_LARGE', 'er.NAME_EVENT_ROLE', 'er.ROLE_DETAIL', 'ed.ROLE_INFO', 'ed.POINT')
            ->orderBy('e.DATE_START')->get();
        }
        return $sql;
    }

    public function getEventDetailIdByParams($eventId, $eventRoleId){
        /*Mendapatkan data event detail berdasarkan ID Event dan ID Event Role (barcode, photo, twitter, facebook) tertentu. */
        return DB::table('event_detail')->where('ID_EVENTS', $eventId)
            ->where('ID_EVENT_ROLE', $eventRoleId) 
            ->orderBy('ID_EVENT_DETAIL')
            ->first();
    }
}
