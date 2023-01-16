<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class EventDetail extends Model
{
    /*  
        Model untuk tabel event_detail.  
        Isi tabel: Data e-learning history (data riwayat kegiatan pada situs kuliah yang dilakukan oleh pengguna).
        Digunakan pada controller: User/MainController
    */

    use HasFactory;
    protected  $primaryKey = 'ID_EVENT_DETAIL';
    protected $table = 'event_detail';
    protected $fillable = ['ID_EVENT_ROLE', 'ID_EVENTS', 'POINTS', 'ROLE_INFO'];

    public function getAllChallengeEvent()
    {
        return DB::table('event_detail')->get();
    }

    public function getReachedPointChallenge($userId, $periode, $start, $finish){
        /* Mendapat jumlah poin dari history keikutsertaan event berdasarkan user dan periode tertentu*/
        $result = DB::table('history AS h')
                ->join('events AS e', 'h.ID_EVENTS', '=', 'e.ID_EVENTS')
                ->join('event_detail AS ed', 'e.ID_EVENTS', '=', 'ed.ID_EVENTS')
                 ->where('DATE_HISTORY', '>', $start)
                ->where('DATE_HISTORY', '<', $finish)
                ->where('h.ID_USERS', $userId)
                ->where('e.ID_PERIOD', $periode)
                ->select(DB::raw('SUM(ed.POINT) AS POINT'))
                ->first();
        if($result->POINT == NULL)
            return 0;
        else
            return $result->POINT;
    }
}
