<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Catalogue extends Model
{
    /*
        Model untuk tabel catalogue. 
        Isi tabel: Data katalog/produk yang ditawarkan di amtarewards.
        Digunakan pada controller: User/CatalogueController, User/RedeemController
    */

    use HasFactory;
    public $timestamps = false;
    protected  $primaryKey = 'ID_CATALOGUE';
    protected $table = 'catalogue';
    protected $fillable = ['ID_PERIOD', 'NAME_CATALOGUE', 'DESCRIPTION', 'PHOTO_SMALL', 'PHOTO_MEDIUM', 'PHOTO_LARGE', 'ID_CTG_TYPE', 'POINT_REQ', 'STOCK', 'STOCK_GUDANG', 'DESTINATION'];

    public function getAllCatalogue($period, $type)
    {
        /*
            Mendapatkan semua data katalog berdasarkan periode dan target user (teacher, student, all user) tertentu. Untuk admin, data katalog ditampilkan semua.
        */

        if($type == 'admin'){
            $sql = DB::table('catalogue AS c')
            ->join('catalogue_type AS ct', 'c.ID_CTG_TYPE', '=', 'ct.ID_CTG_TYPE')
            ->where('c.ID_PERIOD', '=', $period)
            ->orderBy('c.STOCK', 'desc')
            ->get();
        }else{
            $sql = DB::table('catalogue AS c')
            ->join('catalogue_type AS ct', 'c.ID_CTG_TYPE', '=', 'ct.ID_CTG_TYPE')
            ->where('c.ID_PERIOD', '=', $period)
            ->where('c.DESTINATION', '=', $type)
            ->orWhere('c.DESTINATION', '=', 'all user')
            ->orderBy('c.STOCK', 'desc')
            ->get();
        }
    	return $sql;
    }

    public function getCatalogById($catalogId)
    {
        /*
            Mendapatkan data katalog berdasarkan ID katalog tertentu.
        */

        $sql = DB::select("SELECT * FROM catalogue c JOIN catalogue_type ct ON c.ID_CTG_TYPE = ct.ID_CTG_TYPE WHERE ID_CATALOGUE = $catalogId ");
        return $sql;
    }
}
