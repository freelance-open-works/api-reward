<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Passport\HasApiTokens;
use Carbon\Carbon;

class User extends Authenticatable
{
    /**
     * Model untuk tabel users.
     * Isi tabel: data users (mahasiswa dan dosen) yang login di amtarewards.
     * Digunakan pada controller: PackageController, User/CatalogueController, User/EventController, User/HistoryController, 
     *                            User/LeaderboardController, User/LoginController, User/RedeemController, User/MainController,
     *                            User/ReviewController, User/SocialController
     */

    use HasFactory;
    public $timestamps = false;
    protected $table = 'users';
    protected $fillable = [
        'ID_USERS', 'NAME', 'PRODI', 'FAKULTAS', 'POINTS', 'EMAIL', 'TYPE', 'CREATE_TIME', 'PHOTO', 'PHOTO_THUMB'
    ];

    public function getCreatedAtAttribute()
    {
        /* Convert atribut created_at ke format Y-m-d H:i:s */
        if (!is_null($this->attributes['created_at'])) {
            return Carbon::parse($this->attributes['created_at'])->format('Y-m-d H:i:s');
        }
    }

    public function getUpdatedAtAttribute()
    {
        /* Convert atribut updated_at ke format Y-m-d H:i:s */
        if (!is_null($this->attributes['updated_at'])) {
            return Carbon::parse($this->attributes['updated_at'])->format('Y-m-d H:i:s');
        }
    }

    //OTHERS    
    public function getApiKey($id)
    {
        /* 
            Mendapatkan API Key dari tabel user_device berdasarkan ID User tertentu. 
            API Key digunakan untuk autentikasi saat melakukan kegiatan di aplikasi amtarewards.
        */
        return DB::table('user_device')->where('ID_USERS', $id)->select('API_KEY')->first()->API_KEY;
    }

    public function isApiKeyExist($api_key)
    {
        /* Memeriksa apakah API Key yang ada pada parameter URL yang dikirim user itu ada di tabel user_device atau tidak. */
        return DB::table('user_device')->orderBy('ID_USER_DEVICE', 'desc')->where('API_KEY', $api_key)->first();
    }

    public function getUsersById($id)
    {
        /* Mendapatkan data user berdasarkan ID. */
        return DB::table('users')->where('ID_USERS', $id)->first();
    }

    public function insertNewUser($data)
    {
        /* Menambahkan data user baru. */
        return DB::table('users')->insert($data);
    }

    public function insertNewUserDevice($data)
    {
        /* Menambahkan data user device baru. */
        return DB::table('user_device')->insert($data);
    }

    public function getProfileStudent($id_user_device)
    {
        /* 
        * Mendapatkan data ID User berdasarkan ID User Device dari tabel user_device.
        * ID User tersebut digunakan untuk mendapatkan data user dari tabel users.
        */
        $id_user =  DB::table('user_device')->where('ID_USER_DEVICE', $id_user_device)->first()->ID_USERS;
        return User::where('ID_USERS', $id_user)->first();
    }

    public function addPointUser($newPoint, $idUser)
    {
        /* Memperbaharui dengan menambah poin user tertentu. */
        $recentPoint = User::where('ID_USERS', $idUser)->select('POINTS')->first()->POINTS;
        $totalPoint = $recentPoint + $newPoint;

        $this->updatePointUser($totalPoint, $idUser);
    }

    public function updatePointUser($point, $idUser)
    {
        /* Memperbaharui data poin user tertentu. Dipanggil di fungsi addPointUser. */
        return User::where('ID_USERS', $idUser)->update(['POINTS' => $point]);
    }

    public function getLeaderboardByPeriode($start, $end, $type)
    {
        /* Mendapatkan TOP 10 mahasiswa yang mempunyai point tertinggi. */
        $sql = DB::select("
                SELECT
                    u.ID_USERS,
                    u.NAME,
                    u.FAKULTAS, 
                    u.PRODI,
                    SUM(ec.POINT)-(case when (
                                    SELECT SUM(c.POINT_REQ) 
                                    FROM redeem_log as rl
                                    JOIN users as u2 on u2.ID_USERS = rl.ID_USERS
                                    JOIN catalogue as c on c.ID_CATALOGUE = rl.ID_CATALOGUE
                                    where u2.ID_USERS = u.ID_USERS AND rl.REDEEM_TIME >= '$start' AND rl.REDEEM_TIME <= '$end'
                                ) is null 
                                then 0 
                                else (
                                    SELECT SUM(c.POINT_REQ) 
                                    FROM redeem_log as rl
                                    JOIN users as u2 on u2.ID_USERS = rl.ID_USERS
                                    JOIN catalogue as c on c.ID_CATALOGUE = rl.ID_CATALOGUE
                                    where u2.ID_USERS = u.ID_USERS AND rl.REDEEM_TIME >= '$start' AND rl.REDEEM_TIME <= '$end'
                                ) 
                                end) 
                                as point,
                    u.PHOTO_THUMB
                FROM elearning_history as eh
                JOIN users as u on eh.ID_USERS = u.ID_USERS
                JOIN elearning_challenge as ec on ec.ID_ELEARNING_CHALLENGE = eh.ID_ELEARNING_CHALLENGE
                WHERE u.TYPE = '$type' AND eh.DATE_HISTORY >= '$start' AND eh.DATE_HISTORY <= '$end'
                GROUP BY u.ID_USERS
                ORDER BY point desc
                limit 10
                ");
        return $sql;
    }

    public function getUserLeaderboardStudent()
    {
        /* Mendapatkan TOP 10 mahasiswa yang mempunyai point tertinggi. */
        $sql = DB::select("
                select 
                    ID_USERS, 
                    NAME, 
                    FAKULTAS, 
                    PRODI,
                    POINTS, 
                    PHOTO_THUMB, 
                    @curRank := @curRank +1 AS rank
                from users u, (select @curRank := 0) r 
                where TYPE = 'student' 
                order by POINTS desc 
                limit 10
        ");
        return $sql;
    }

    public function getUserLeaderboardTeacher()
    {
        /* Mendapatkan TOP 10 dosen yang mempunyai point tertinggi. */
        $sql = DB::select("
                select 
                    ID_USERS, 
                    NAME, 
                    FAKULTAS, 
                    PRODI,
                    POINTS, 
                    PHOTO_THUMB, 
                    @curRank := @curRank +1 AS rank
                from users u, (select @curRank := 0) r 
                where TYPE = 'teacher' 
                order by POINTS desc 
                limit 10
        ");
        return $sql;
    }

    public function getUserRank($id_user, $type)
    {
        /* Mendapatkan ranking poin user tertentu. */
        $sql = DB::select("
                select 
                    ID_USERS, 
                    @curRank := @curRank +1 AS rank 
                    from users u, (select @curRank := 0) r 
                    where TYPE = '" . $type . "' 
                    order by POINTS desc
        ");

        $i_user         = array_search($id_user, array_column($sql, 'ID_USERS'));
        $rank_user      = $sql[$i_user]->rank;

        return $rank_user;
    }

    public function getUserSocial($userId)
    {
        /* Mendapatkan data sosial media user yang disambungkan dengan aplikasi amtarewards. */
        return DB::table('user_social')->where('ID_USERS', $userId)->first();
    }

    public function getUserSocialByIdUser($idUser, $socialType)
    {
        /* Mendapatkan data sosial media user yang disambungkan dengan aplikasi amtarewards berdasarkan ID User tertentu dan tipe sosial media (facebook/twitter) */
        return DB::table('user_social')
            ->where('ID_USERS', $idUser)
            ->where('SOCIAL_TYPE', $socialType)
            ->get();
    }

    public function insertUserSocial($value)
    {
        /* Menambah data sosial media user.  */
        return DB::table('user_social')->insert($value);
    }

    public function updateUserSocial($value, $idUser, $socialType)
    {
        /* Memperbaharui data sosial media user.  */
        return DB::table('user_social')
            ->where('ID_USERS', $idUser)
            ->where('SOCIAL_TYPE', $socialType)
            ->update($value);
    }

    public function getIdUsers()
    {
        /* Mendapatkan semua data ID user.  */
        return DB::table('users')->select('ID_USERS')->get();
    }
}
