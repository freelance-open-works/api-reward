<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Challenge extends Model
{
    /* 
        Model untuk tabel events.
        Tabel ini berisi data challenge yang pernah atau sedang diadakan.
        Digunakan pada controller: ElearningChallengeController, User/HistoryController, User/MainController
    */
    use HasFactory;
    public $timestamps = false;
    protected  $primaryKey = 'ID_EVENTS';
    protected $table = 'events';
    protected $fillable = ['DATE_START', 'ID_EVENT_TYPE', 'DATE_FINISH', 'DESC_EVENTS', 'NAME_EVENTS', 'PHOTO_SMALL', 'PHOTO_MEDIUM', 'PHOTO_LARGE', 'DESTINATION', 'ID_PERIOD'];

    public function getAllChallengeEvent()
    {
        /* Mendapatkan semua data event.*/
        return DB::table('events')
            ->get();
    }
}
