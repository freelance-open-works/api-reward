<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ElearningChallenge extends Model
{
    /*  
        Model untuk tabel elearning_challenge.  
        Isi tabel: Data e-learning challenge (data kegiatan yang bisa dilakukan pada situs kuliah untuk mendapat poin amtarewards).
        Digunakan pada controller: ElearningChallengeController, User/HistoryController, User/MainController
    */
    use HasFactory;
    public $timestamps = false;
    protected  $primaryKey = 'ID_ELEARNING_CHALLENGE';
    protected $table = 'elearning_challenge';
    protected $fillable = ['NAME', 'DESC', 'ID_CATEGORY', 'MODULE', 'ACTION', 'CATEGORY', 'ID_EVENTNAME', 
                        'EVENTNAME_DISPLAY', 'EVENTNAME_CODE', 'POINT', 'DESTINATION', 'MAX_COUNT', 'ID_PERIOD'];

    public function getAllElearningChallenge($periode, $dest)
    {
        /* Mendapatkan data E-learning Challenge berdasarkan periode dan target user (teacher, student, all user) tertentu. Untuk admin, semua data E-learning Challenge diambil. */
        if ($dest == "admin") {
            return ElearningChallenge::all();
        } else {
            return ElearningChallenge::where('ID_PERIOD', $periode)->where(function ($query) use ($dest) {
                $query->where('DESTINATION', $dest)
                    ->orWhere('DESTINATION', 'all user');
            })->get();
        }
    }

    public function getAllMoodleEventList()
    {
        /* Mendapatkan semua data event moodle. */
        return DB::table('mdl_event_list')->orderBy('NAME_DISPLAY')->get();
    }

    public function getAllKuliahLogDisplay()
    {
        /* 
            Mendapatkan semua data old event moodle dari tabel dbstudents (dari database moodle)
            Tabel dbstudents diakses dengan koneksi ke database moodle. Koneksi diset pada file config/database.php dan .env.
        */
        return DB::connection('dbstudents')->table('mdl_log_display')->get();
    }

    public function getAllElearningHistoryByUserId($userId)
    {
        /* 
            Mendapatkan data E-learning History (log kuliah yang sudah disimpan di tabel elearning_history) berdasarkan ID User tertentu. 
        */
        return DB::table('elearning_history AS eh')
            ->join('elearning_challenge AS ec', 'eh.ID_ELEARNING_CHALLENGE', '=', 'ec.ID_ELEARNING_CHALLENGE')
            ->where('eh.ID_USERS', $userId)
            ->groupBy('eh.ID_ELEARNING_CHALLENGE')
            ->select('eh.ID_ELEARNING_HISTORY', 'eh.ID_ELEARNING_CHALLENGE', 'ec.NAME', 'ec.DESC', 'ec.POINT', 'eh.DATE_HISTORY', DB::raw('sum(ec.POINT) as TOTAL_POINT'))
            ->get();
    }

    public function getElearningHistoryByLogKuliahIdAndUserId($log_kul_id, $user_id)
    {
        /* 
            Mendapatkan data E-learning History (log kuliah yang sudah disimpan di tabel elearning_history) berdasarkan ID Log Kuliah dan ID User tertentu. 
        */
        return DB::table('elearning_history')
            ->where('ID_LOG_KULIAH', $log_kul_id)
            ->where('ID_USERS', $user_id)
            ->first();
    }

    public function getItemElearningHistoryByIdChall($idChall, $userId)
    {
        /* 
            Mendapatkan data E-learning History (log kuliah yang sudah disimpan di tabel elearning_history) berdasarkan ID E-learning Challenge dan ID User tertentu. 
        */
        return DB::table('elearning_history as eh')
            ->join('elearning_challenge as ec', 'eh.ID_ELEARNING_CHALLENGE', '=', 'ec.ID_ELEARNING_CHALLENGE')
            ->where('eh.ID_ELEARNING_CHALLENGE', $idChall)
            ->where('eh.ID_USERS', $userId)
            ->orderByDesc('eh.DATE_HISTORY')
            ->get();
    }

    public function getKuliahLogTableRef($tableName)
    {
        /*Mendapat data table ref berdasarkan nama tabel */
        return DB::table('kuliah_log_table_ref')->where('TABLE_NAME', $tableName)->first();
    }

    public function insertElearningHistory($data)
    {
        /* Memasukkan data E-learning History baru. */
        return DB::table('elearning_history')->insert($data);
    }

    public function getAll_new_ElearningHistoryByUserId($userId)
    {
         /* Mendapatkan data E-learning History dengan tipe 'new' berdasarkan ID User tertentu. */
        return DB::table('elearning_history')
            ->where('ID_USERS', $userId)
            ->where('TYPE', 'new')
            ->get();
    }

    public function getAll_new_ElearningHistoryByUserIdAndLogKuliahId($log_kul_id, $userId)
    {
    /* Mendapatkan data E-learning History dengan tipe data 'new' berdasarkan ID User dan ID Log Kuliah tertentu. */
        return DB::table('elearning_history')
            ->where('ID_USERS', $userId)
            ->where('ID_LOG_KULIAH', $log_kul_id)
            ->where('TYPE', 'new')
            ->get();
    }

    public function getReachedPointElearningHistory($userId, $periode, $start, $finish)
    {
        /* Mendapatkan jumlah poin dari E-learning History berdasarkan ID User, ID Periode dan tanggal tertentu. */
        $result =  DB::table('elearning_history AS eh')
            ->join('elearning_challenge AS ec', 'eh.ID_ELEARNING_CHALLENGE', '=', 'ec.ID_ELEARNING_CHALLENGE')
            ->where('eh.ID_USERS', $userId)
            ->where('ec.ID_PERIOD', $periode)
            ->where('DATE_HISTORY', '>', $start)
            ->where('DATE_HISTORY', '<', $finish)
            ->select(DB::raw('SUM(ec.POINT) AS POINT'))
            ->first();

        if ($result->POINT == NULL)
            return 0;
        else
            return $result->POINT;
    }
}
