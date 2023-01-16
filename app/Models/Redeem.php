<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Redeem extends Model
{
    /**
     * Model untuk tabel redeem_log.
     * Isi tabel: data redeem produk yang dilakukan oleh pengguna amtarewards.
     * Digunakan pada controller: PackageController, User/CatalogueController, User/RedeemController, User/MainController
     */
    use HasFactory;

    public $timestamps = false;
    protected  $primaryKey = 'ID_REDEEM_LOG';
    protected $table = 'redeem_log';
    //protected $fillable = ['YEAR_START', 'YEAR_FINISH', 'STATUS', 'YEAR_PERIODE'];

    public function getAllRedeem()
    {
        /* Mendapatkan semua data redeem produk. */
        return DB::table('redeem_log')
            ->get();
    }

    public function getConsumedPointRedeem($userId, $periode)
    {
        /* Mendapatkan jumlah poin yang sudah digunakan untuk redeem berdasarkan ID user dan ID periode tertentu. */
        $result = DB::table('redeem_log AS r')
            ->join('catalogue AS c', 'r.ID_CATALOGUE', '=', 'c.ID_CATALOGUE')
            ->where('r.ID_USERS', $userId)
            ->where('c.ID_PERIOD', $periode)
            ->select(DB::raw('SUM(c.POINT_REQ) AS POINT'))
            ->first();
        if ($result->POINT == NULL)
            return 0;
        else
            return $result->POINT;
    }

    public function getRedeemUser($userId, $periode)
    {
         /* Mendapatkan data redeem berdasarkan ID user dan ID periode tertentu. */
        return DB::table('redeem_log AS rl')
            ->join('catalogue AS c', 'rl.ID_CATALOGUE', '=', 'c.ID_CATALOGUE')
            ->join('redeem_status AS rs', 'rl.ID_REDEEM_STATUS', '=', 'rs.ID_REDEEM_STATUS')
            ->where('rl.ID_USERS', $userId)
            ->where('c.ID_PERIOD', $periode)
            ->get();
    }

    public function getRedeemedCatalog($idCatalog, $userId)
    {
        /* Mendapatkan data redeem berdasarkan ID user dan ID catalogue (ID produk) tertentu. */
        return DB::table('redeem_log')
            ->where('ID_CATALOGUE', $idCatalog)
            ->where('ID_USERS', $userId)
            ->get();
    }

    public function redeemByUser($redeemData, $idCatalog, $stock, $idUser, $point)
    {
        /* Menambahkan data redeem, mengurangi stock untuk ID catalogue tertentu, dan mengurangi poin ID User tertentu. */
        if (DB::table('redeem_log')->insert($redeemData)) {
            $stock = $stock - 1;

            $sql1 = DB::table('catalogue')
                ->where('ID_CATALOGUE', $idCatalog)
                ->update(['STOCK' => $stock]);

            $sql2 = DB::table('users')
                ->where('ID_USERS', $idUser)
                ->update(['POINTS' => $point]);

            return true;
        } else {
            return false;
        }
    }

    public function getUserGrandprizeRedeemed($userId, $periode)
    {
        /* Mendapatkan data redeem bertipe Grand berdasarkan ID user dan ID periode tertentu. */
        $sql = DB::select("SELECT * from redeem_log rl join catalogue c on rl.ID_CATALOGUE = c.ID_CATALOGUE where rl.ID_USERS = $userId and c.ID_PERIOD = $periode and c.ID_CTG_TYPE = 1");
        return $sql;
    }

    public function getCountAllRedeem($periode)
    {
        /* Mendapatkan data redeem yang di-group berdasarkan user. Data diolah menjadi kolom jumlah total redeem, pending, finished, dan not started. */
        return DB::table('redeem_log AS rl')
            ->join('catalogue AS c', 'rl.ID_CATALOGUE', '=', 'c.ID_CATALOGUE')
            ->join('users AS u', 'rl.ID_USERS', '=', 'u.ID_USERS')
            ->where("c.ID_PERIOD", $periode)
            ->select(
                'rl.ID_USERS',
                'u.NAME',
                //Total data redeem
                DB::raw('COUNT(rl.ID_REDEEM_LOG) AS TOTAL'), 
                //Jumlah data redeem yang pending
                DB::raw("case when (SELECT count(ID_REDEEM_LOG) 
                            FROM redeem_log as rl2
                            JOIN catalogue as c ON c.ID_CATALOGUE = rl2.ID_CATALOGUE
                            WHERE ID_REDEEM_STATUS=1 AND ID_USERS = rl.ID_USERS 
                            AND c.ID_PERIOD = $periode
                            GROUP BY ID_USERS) is null 
                            then 0 else 
                                (SELECT count(ID_REDEEM_LOG) 
                                FROM redeem_log rl2
                                JOIN catalogue as c ON c.ID_CATALOGUE = rl2.ID_CATALOGUE
                                WHERE ID_REDEEM_STATUS=1 AND ID_USERS = rl.ID_USERS 
                                AND c.ID_PERIOD = $periode
                                GROUP BY ID_USERS) end 
                        AS PENDING"),
                //Jumlah data redeem yang on process
                DB::raw("case when (SELECT count(ID_REDEEM_LOG) 
                        FROM redeem_log rl2
                        JOIN catalogue as c ON c.ID_CATALOGUE = rl2.ID_CATALOGUE
                        WHERE ID_REDEEM_STATUS=3 AND ID_USERS = rl.ID_USERS 
                        AND c.ID_PERIOD = $periode       
                        GROUP BY ID_USERS) is null
                        then 0 else (SELECT count(ID_REDEEM_LOG) 
                            FROM redeem_log rl2
                            JOIN catalogue as c ON c.ID_CATALOGUE = rl2.ID_CATALOGUE
                            WHERE ID_REDEEM_STATUS=3 AND ID_USERS = rl.ID_USERS 
                            AND c.ID_PERIOD = $periode 
                            GROUP BY ID_USERS) end
                    AS ON_PROCESS"),
                //Jumlah data redeem yang finished
                DB::raw("case when (SELECT count(ID_REDEEM_LOG) 
                            FROM redeem_log rl2
                            JOIN catalogue as c ON c.ID_CATALOGUE = rl2.ID_CATALOGUE
                            WHERE ID_REDEEM_STATUS=2 AND ID_USERS = rl.ID_USERS 
                            AND c.ID_PERIOD = $periode       
                            GROUP BY ID_USERS) is null
                            then 0 else (SELECT count(ID_REDEEM_LOG) 
                                FROM redeem_log rl2
                                JOIN catalogue as c ON c.ID_CATALOGUE = rl2.ID_CATALOGUE
                                WHERE ID_REDEEM_STATUS=2 AND ID_USERS = rl.ID_USERS 
                                AND c.ID_PERIOD = $periode 
                                GROUP BY ID_USERS) end
                        AS FINISHED"),
                'c.ID_PERIOD'
            )
            ->groupBy('rl.ID_USERS')->get();
    }

    public function getCountAllRedeemUser($periode)
    {
        /* Mendapatkan jumlah data redeem berdasarkan ID periode.  */
        return DB::table('redeem_log AS rl')
            ->join('catalogue AS c', 'rl.ID_CATALOGUE', '=', 'c.ID_CATALOGUE')
            ->where("c.ID_PERIOD", $periode)
            ->count();
    }


    public function getProductListUser($userId, $periode)
    {
        /* Medapatkan data redeem berdasarkan ID User dan ID periode. */
        return DB::table('redeem_log AS rl')
            ->join('catalogue AS c', 'rl.ID_CATALOGUE', '=', 'c.ID_CATALOGUE')
            ->where("rl.ID_USERS", $userId)
            ->where("c.ID_PERIOD", $periode)
            ->select('rl.ID_REDEEM_LOG', 'c.NAME_CATALOGUE', 'rl.ID_REDEEM_STATUS', 'rl.REDEEM_KEY')
            ->get();
    }
}
