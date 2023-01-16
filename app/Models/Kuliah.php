<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Kuliah extends Model
{
    /*  
        Model untuk tabel-tabel yang berhubungan dengan moodle.
        Untuk menyambungkan dengan database moodle, digunakan "connection('dbstudents') yang telah disetting di .env dan config/database.php.
        Digunakan pada controller: User/MainController.
    */
    use HasFactory;

    public function getKuliahLogById($id)
    {   
        /* Mendapat data log kuliah. */
        return DB::connection('dbstudents')->table('mdl_log')
            ->where('id', $id)
            ->first();
    }

    public function getKuliahLogStandardById($id)
    {
        /* Mendapat data standard log kuliah. */
        return DB::connection('dbstudents')->table('mdl_logstore_standard_log')
            ->where('id', $id)
            ->first();
    }

    public function getKuliahLogDisplay($module, $action)
    {
        /* Mendapat data standard log kuliah berdasarkan mpdule atau action tertentu*/
        return DB::connection('dbstudents')->table('mdl_logstore_standard_log')
            ->where('module', $module)
            ->where('action', $action)
            ->first();
    }

    public function getKuliahLogDetail($mtable, $field, $fetch_id)
    {
         /* Mendapatkan data dari berbagai tabel (berdasarkan parameter $mtable). */
        $mtable = 'mdl_' . $mtable;
        return DB::connection('dbstudents')->table($mtable)
            ->where('id', $fetch_id)
            ->first();
    }

    public function getInstanceIdFromCourseModule($id)
    {
        /* Mendapat instance berdasarkan ID CMID/Fetch ID tertentu */
        return DB::connection('dbstudents')->table('mdl_course_modules')
            ->select('instance')
            ->where('id', $id)
            ->first();
    }

    public function getKuliahCourseFullname($id)
    {
        /* Mendapat nama lengkap dari sebuah mata kuliah */
        return DB::connection('dbstudents')->table('mdl_course')
            ->select('fullname')
            ->where('id', $id)
            ->first();
    }

    public function getAllKuliahLogDisplay()
    {
        /* Mendapatkan seluruh data dari tabel mdl_log_display */
        return DB::connection('dbstudents')->table('mdl_log_display')->get();
    }

    public function getKuliahLog($userid)
    {
        /* Mendapatkan data log kuliah yang berjenis OLD*/
        return DB::connection('dbstudents')->table('mdl_log')
            ->where('userid', $userid)
            ->get();
    }

    public function getKuliahLogByDate($userid, $start, $finish)
    {
        /* Mendapatkan data log kuliah kemaren (1 hari sebelumnya) yang berjenis OLD*/
        $time = Carbon::yesterday()->timezone('Asia/Jakarta');
        return DB::connection('dbstudents')->table('mdl_log')
            ->where('userid', $userid)
            ->where('time', '>=', strtotime($start))
            ->where('time', '>=', strtotime($time))
            ->where('time', '<=', strtotime($finish))
            ->get();
    }
    
    public function getKuliahLogByPeriode($userid, $start, $finish)
    {
        /* Mendapatkan data log kuliah ang berjenis OLD sesuai dengan periode*/
        return DB::connection('dbstudents')->table('mdl_log')
            ->where('userid', $userid)
            ->where('time', '>=', strtotime($start))
            ->where('time', '<=', strtotime($finish))
            ->get();
    }
    

    public function getUser($npm)
    {
        /* Mendapatkan data user dari situs kuliah*/
        return DB::connection('dbstudents')->table('mdl_user')
            ->where('username', $npm)
            ->first();
    }

    public function getKuliahLogStandard($userid, $start, $finish)
    {
        /* Mendapatkan semua data log kuliah untuk pengguna tertentu yang lognya berjenis NEW*/
        return DB::connection('dbstudents')->table('mdl_logstore_standard_log')
            ->where('userid', $userid)
            ->where('timecreated', '>=', strtotime($start))
            ->where('timecreated', '<=', strtotime($finish))
            ->get();
    }

    public function getKuliahLogStandardYesterday($userid, $start, $finish)
    {
        /* Mendapatkan data log kuliah kemaren (1 hari sebelumnya) yang berjenis NEW*/
        $time = Carbon::yesterday()->timezone('Asia/Jakarta');
        return DB::connection('dbstudents')->table('mdl_logstore_standard_log')
            ->where('userid', $userid)
            ->where('timecreated', '>=', strtotime($start))
            ->where('timecreated', '>=', strtotime($time))
            ->where('timecreated', '<=', strtotime($finish))
            ->get();
    }

    public function getFieldTable($mtable)
    {
        /* Mendapat field */
        return DB::connection('dbstudents')->table('mdl_log_display')
            ->select('field')
            ->where('mtable', $mtable)
            ->limit(1)
            ->first();
    }

    public function getLogObjectId($id, $objecttable, $field)
    {
        /* Mendapat data dari berbagai tabel sesuai dengan parameter berdasarkan ID */
        $mtable = 'mdl_' . $objecttable;
        $result = DB::connection('dbstudents')->table($mtable)
            ->select($field) // FIXME: kurang tau $field isinya 1 kolom atau banyak
            ->where('id', $id)
            ->first();

        if (!is_null($result)) {
            return $result->$field;
        } else {
            return false;
        }
    }

    // public function getRowObjectId()
    // {
    //     $result = DB::connection('dbstudents')->table('mdl_quiz_attempts')
    //         ->where('id', 10739)
    //         ->first();
    //     var_dump($result);
    // }
}
